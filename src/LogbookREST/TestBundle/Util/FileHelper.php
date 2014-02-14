<?php

namespace LogbookREST\TestBundle\Util;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Dumper;

class FileHelper
{
    /**
     * Load the yml file and return it contents in an array
     * @param string $path
     *
     * @return array $data
     */
    public static function loadYml($path)
    {
        $yaml = new Parser();

        try {
            $data = $yaml->parse(file_get_contents($path));
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }

        return $data;
    }

    /**
     * Load the xml file and return it
     * @param string $path
     *
     * @return simplexml $data
     */
    public static function loadXml($path)
    {
        if (!is_file($path)) {
            throw new \Exception("Cannot find file");
        }
        $xml = simplexml_load_file($path);

        return $xml;
    }
}
