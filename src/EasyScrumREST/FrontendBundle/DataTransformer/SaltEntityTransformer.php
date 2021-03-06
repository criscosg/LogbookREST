<?php

namespace EasyScrumREST\FrontendBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class SaltEntityTransformer implements DataTransformerInterface
{
	/**
	 * @var \Doctrine\Common\Persistence\ObjectManager
	 */
	private $om;
	private $entityClass;
	private $entityType;
	private $entityRepository;

	/**
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om, $entityClass, $entityRepository, $entityType)
	{
		$this->om = $om;
		$this->entityClass = $entityClass;
		$this->entityRepository = $entityRepository;
		$this->entityType = $entityType;
	}

	/**
	 * @param mixed $entity
	 *
	 * @return integer
	 */
	public function transform($entity)
	{
		if (null === $entity || !$entity instanceof $this->entityClass) {
			return '';
		} elseif(!\method_exists($entity, 'get'.$this->entityType)) {
			return $entity;
		}

		return $entity;
	}
	
	/**
	 * @param mixed $id
	 *
	 * @throws \Symfony\Component\Form\Exception\TransformationFailedException
	 *
	 * @return mixed|object
	 */
	public function reverseTransform($salt)
	{
		if (!$salt) {
			return null;
		}
	
		$entity = $this->om->getRepository($this->entityRepository)->findOneBy(array("salt" => $salt));

		if (null === $entity) {
			throw new TransformationFailedException(sprintf(
					'A %s with salt "%s" does not exist!',
					$this->entityType,
					$salt
			));
		}
	
		return $entity;
	}
	
	public function setEntityType($entityType)
	{
		$this->entityType = $entityType;
	}
	
	public function setEntityClass($entityClass)
	{
		$this->entityClass = $entityClass;
	}
	
	public function setEntityRepository($entityRepository)
	{
		$this->entityRepository = $entityRepository;
	}
}