<?php
namespace Oka\FileBundle\DoctrineBehaviors\ORM;

use Doctrine\Common\EventSubscriber;
use Oka\FileBundle\DoctrineBehaviors\Reflection\ClassAnalyzer;

/**
 * 
 * @author cedrick
 * 
 */
abstract class AbstractListener implements EventSubscriber
{
	/**
	 * @var ClassAnalyzer $classAnalyser
	 */
    private $classAnalyser;
    
    /**
     * @var boolean $isRecursive
     */
    protected $isRecursive;
    
    /**
	 * @var array $mappings
	 */
	protected $mappings;
    
    public function setClassAnalyzer(ClassAnalyzer $classAnalyser)
    {
    	$this->classAnalyser = $classAnalyser;
    }
    
    public function setRecursive($recursive)
    {
    	$this->isRecursive = $recursive;
    }
    
    protected function getClassAnalyzer()
    {
        return $this->classAnalyser;
    }
    
    protected abstract function isEntitySupported(\ReflectionClass $reflClass);
}