<?php
namespace Oka\FileBundle\Doctrine;

use Doctrine\Common\Persistence\ObjectManager;
use Oka\FileBundle\Model\FileManager;

/**
 * 
 * @author cedrick
 * 
 */
class ImageManager extends FileManager
{
	/**
	 * Constructor.
	 * 
	 * @param ObjectManager	$om
	 * @param string		$class
	 */
	public function __construct(ObjectManager $om, $class) {
		parent::__construct($om, $class);
	}
}