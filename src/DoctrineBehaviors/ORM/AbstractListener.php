<?php
namespace Oka\FileBundle\DoctrineBehaviors\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;
use Oka\FileBundle\DoctrineBehaviors\Common\AbstractListener as BaseAbstractListener;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * 
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 * 
 */
abstract class AbstractListener extends BaseAbstractListener
{
	/**
	 * @param string $class
	 * @param array $mapping
	 * @return array
	 */
	protected function handleEntityMapping($class, $mapping)
	{
		if (false === isset($this->mappings[$class])) {
			throw new InvalidConfigurationException(sprintf('No mapping is defined for the "%s" class to use the behavior.', $class));
		}
		
		$mapping['targetEntity'] = $this->mappings[$class]['target_object'];
		
		switch (strtoupper($this->mappings[$class]['fetch'])) {
			case 'EAGER':
				$mapping['fetch'] = ClassMetadata::FETCH_EAGER;
				break;
				
			case 'LAZY':
				$mapping['fetch'] = ClassMetadata::FETCH_LAZY;
				break;
				
			case 'EXTRA_LAZY':
				$mapping['fetch'] = ClassMetadata::FETCH_EXTRA_LAZY;
				break;
		}
		
		return $mapping;
	}
}
