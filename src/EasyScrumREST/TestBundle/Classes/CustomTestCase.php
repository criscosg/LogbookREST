<?php

namespace EasyScrumREST\TestBundle\Classes;

use FOS\RestBundle\Decoder\JsonDecoder;

use Symfony\Component\Serializer\Encoder\JsonDecode;

require_once __DIR__.'/../../../../app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\SchemaTool;
use \Doctrine\Common\Collections\Collection;
use EasyScrumREST\TestBundle\Util\FileHelper;

abstract class CustomTestCase extends WebTestCase
{
    const USERNAME = "jiminy@cricket.com";
    const PASSWORD = "123456";
    const OAUTH_CLIENT_ID = "1_25mlhmydi1lw8ook4k88kssg48koo8gk800oko80wkokg4kcg8";
    const OAUTH_SECRET = "2a1iled5fs00s84ocwogwkw0w4kk400kcs08wcs84koko8c48k";

    protected $entityManager;
    protected $container;
    protected $application;
    protected $client;
    protected $environment;

    protected function setUp()
    {
        $options = array('environment' => 'test', 'debug' => true,);

        $this->client = static::createClient($options);
        $this->container = static::$kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();

        $this->generateSchema();
        parent::setUp();
    }

    /**
     * Create tables and load it into sqlite test database
     * @throws Doctrine\DBAL\Schema\SchemaException
     */
    protected function generateSchema()
    {
        $metadatas = $this->getMetadatas();

        if (!empty($metadatas)) {
            $tool = new SchemaTool($this->entityManager);
            $tool->dropDatabase();
            $tool->createSchema($metadatas);
        } else {
            throw new Doctrine\DBAL\Schema\SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * Overwrite this method to get specific metadatas.
     *
     * @return Array
     */
    protected function getMetadatas()
    {
        return $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * Mock Doctrine Entity Manager
     * @param string $fakeRepositoryName
     *
     * @return $emMock
     */
    protected function getEmMock($fakeRepositoryName)
    {
        $emMock = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getMetadataFor', 'persist', 'flush'), array(), '', false);
        // CHANGELOG SYMFONY2.2 getClassMetadata for getMetadataFor
        $emMock->expects($this->any())->method('getRepository')->will($this->returnValue(new $fakeRepositoryName()));
        $emMock->expects($this->any())->method('getMetadataFor')->will($this->returnValue((object)array('name' => 'aClass'))); // CHANGELOG SYMFONY2.2 getClassMetadata for getMetadataFor
        $emMock->expects($this->any())->method('persist')->will($this->returnValue(null));
        $emMock->expects($this->any())->method('flush')->will($this->returnValue(null));

        return $emMock;
    }

    protected function tearDown()
    {
        $this->removeIntegrationTestObjects();
        parent::tearDown();
        $reflect = new \ReflectionClass($this);
        $properties = $reflect->getProperties();
        foreach ($properties as $p) {
            $attr = $p->getName();
            if ($p->isStatic()) {
                $p->setAccessible(1);
                $p->setValue(null);
            } else if (!is_array($this->$attr)) {
                $this->$attr = null;
            }
        }
    }

    /**
     * Return the service asked
     * @param string $service
     *
     * @return $service
     */
    protected function get($service)
    {
        return $this->container->get($service);
    }

    protected function printContent()
    {
        ldd($this->client->getResponse()->getContent());
    }

    protected function removeIntegrationTestObjects()
    {
        //template method
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));

        return $this->application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }

    protected function assertEqualsCollections(Collection $c1, Collection $c2, $fieldToCompare)
    {
        $fieldName = 'get' . ucfirst($fieldToCompare);

        $this->assertEquals($c1->count(), $c2->count());
        for ($i = 0; $i < $c1->count(); $i++) {
            $this->assertEquals($c1[$i]->$fieldName(), $c2[$i]->$fieldName());
        }
    }

    /**
     * Load the given fixtures
     * @param string $fixture the name of the file in which is defined the class which implements FixtureInterface
     * @param string $setDataFixtureFunction name of the callback function which create the objects from the read data. Default case implemented
     *
     * @return mixed $data Adapted to the needs of the test
     */
    protected function loadFixture($fixture, $setDataFixtureFunction = 'setDataFixture')
    {
        $extension = explode('.', $fixture);
        $extension = $extension[count($extension)-1];

        $loadFunction = "load" . ucfirst($extension);
        $data = FileHelper::$loadFunction($fixture);

        return $this->$setDataFixtureFunction($data);
    }

    /**
     * Template method: the child of this class must override this method
     * in order to set the objects from the fixture and persist them to the test database
     *
     * @param array $dataArray with data from the loaded fixture
     */
    protected function setDataFixture($dataArray)
    {
        foreach ($dataArray as $entity => $values) {
            $class = $entity;
            $this->createObjectAndSetValues($class, $values);
        }
    }

    protected function setDataFixtureObjects($class, $dataArray)
    {
        foreach ($dataArray as $array) {
            foreach ($array as $values) {
                $this->createObjectAndSetValues($class, $values);
            }
        }
    }

    protected function createObjectAndSetValues($class, $values)
    {
        $o = new $class();
        foreach ($values as $field => $value) {
            $this->setField($o, $field, $value);
        }

        $this->entityManager->persist($o);
        $this->entityManager->flush();
    }

    protected function setField($o, $field, $value)
    {
        if (!is_array($value)){
            if (strpos($value, "__id__") !== false) {
                $parts = explode("__id__", $value);
                $bundleName = $parts[0];
                $objectId = $parts[1];
                $this->setFixtureObject($bundleName, $objectId, $field, $o);
            } else if (strpos($value, "__date__") !== false) {
                $parts = explode("__date__", $value);
                $dateString = $parts[0];
                $date = new \DateTime($dateString);
                $method = 'set' . ucfirst($field);
                $o->$method($date);
            } else {
                $method = 'set' . ucfirst($field);
                $o->$method($value);
            }
        } else {
            $method = 'set' . ucfirst($field);
            $o->$method($value);
        }
    }

    protected function setFixtureObject($class, $objectId, $field, $o)
    {
        $object = $this->entityManager->getRepository($class)->findOneById($objectId);

        $method = 'set' . ucfirst($field);
        $o->$method($object);
    }

    /**
     * Function needed to set some $_SERVER variables in order
     * to run some tests for BackOffice.
     */
    protected function setBOParameters()
    {
        $_SERVER['HTTP_HOST'] = $this->client->getServerParameter('HTTP_HOST');
        $_SERVER['REQUEST_URI'] = '/app_dev.php/bo';
    }

    protected function getLastLineFromFile($filePath)
    {
        $file = file_get_contents($filePath);
        $lines = explode("\n", $file);
        array_pop($lines);

        return $line = $lines[count($lines) - 1];
    }

    protected function login($username = self::USERNAME, $password = self::PASSWORD)
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('Acceder')->form(array('_username' => $username, '_password'=>$password));
        $crawler = $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        return $crawler;
    }
    
    protected function loginOauth($username = self::USERNAME, $password = self::PASSWORD)
    {
        //ldd('/oauth/v2/token?client_id='.self::OAUTH_CLIENT_ID.'&client_secret='.self::OAUTH_SECRET.'&grant_type=password&username='.self::USERNAME.'&password='.self::PASSWORD);
        $crawler = $this->client->request('GET', '/oauth/v2/token?client_id='.self::OAUTH_CLIENT_ID.'&client_secret='.self::OAUTH_SECRET.'&grant_type=password&username='.self::USERNAME.'&password='.self::PASSWORD);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response=json_decode($this->client->getResponse()->getContent(), true);
        
        return $response['access_token'];
    }
}
