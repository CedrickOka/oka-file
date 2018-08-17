<?php
namespace Oka\FileBundle\Model;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Oka\FileBundle\Utils\FileUtil;
use Oka\FileBundle\Utils\ImageUtils;
use Symfony\Component\Finder\Finder;

/**
 *
 * @author  Cedrick Oka Baidai <okacedrick@gmail.com>
 * 
 */
abstract class Image extends File implements ImageInterface, ImageManipulatorInterface
{
	/**
	 * @var integer $width
	 */
	protected $width;
	
	/**
	 * @var integer $height
	 */
	protected $height;
	
	/**
	 * @var string $dominantColor
	 */
	protected $dominantColor;
	
	/**
	 * @var string $thumbnailMode
	 */
	protected $thumbnailMode;
	
	/**
	 * @var integer $thumbnailQuality
	 */
	protected $thumbnailQuality;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->width = 0;
		$this->height = 0;
		$this->dominantColor = 'ffffff';
	}
	
	/**
	 * @return integer
	 */
	public function getWidth()
	{
		return $this->width;
	}
	
	/**
	 * @param integer $width
	 */
	public function setWidth($width)
	{
		$this->width = $width;
		return $this;
	}
	
	/**
	 * @return integer
	 */
	public function getHeight()
	{
		return $this->height;
	}
	
	/**
	 * @param integer $height
	 */
	public function setHeight($height)
	{
		$this->height = $height;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getDominantColor()
	{
		return $this->dominantColor;
	}
	
	/**
	 * @param string $dominantColor
	 */
	public function setDominantColor($dominantColor)
	{
		$this->dominantColor = $dominantColor;
		return $this;
	}
	
	public function setThumbnailMode($thumbnailMode)
	{
		$this->thumbnailMode = $thumbnailMode;
		return $this;
	}
	
	public function setThumbnailQuality($thumbnailQuality)
	{
		$this->thumbnailQuality = $thumbnailQuality;
		return $this;
	}
	
	public static function createDirnameWith($mode, $quality, $width = null, $height = null)
	{
		$dirname = $mode . '/' . $quality . '/';
		$dirname .= $width === null ? '_' : $width;
		$dirname .= $height === null ? 'x_' : 'x'.$height;
		
		return $dirname;
	}
	
	public function getPathFor($width = null, $height = null, $mode = null, $quality = null)
	{
		$mode = $mode ?: $this->thumbnailMode;
		$quality = $quality ?: $this->thumbnailQuality;
		
		return $this->getPath() . '/' . self::createDirnameWith($mode, $quality, $width, $height);
	}
	
	public function getRealPathFor($width = null, $height = null, $mode = null, $quality = null)
	{
		return $this->getPathFor($width, $height, $mode, $quality) . '/' . $this->getFilename();
	}
	
	public function getUriFor($width = null, $height = null, $mode = null, $quality = null)
	{
		$mode = $mode ?: $this->thumbnailMode;
		$quality = $quality ?: $this->thumbnailQuality;
		
		$uri = ($this->secure === true ? 'https://' : 'http://') . $this->host;
		$uri .=  $this->port === null ? '' : ':' . $this->port;
		$uri .= $this->dirname ? '/' . $this->dirname : '';
		$uri .= '/' . self::createDirnameWith($mode, $quality, $width, $height);
		$uri .= '/' . $this->getFilename().$this->createQueryStringForURI();
		
		if (false === $this->exists($this->getRealPathFor($width, $height, $mode, $quality))) {
			$this->thumbnail($width, $height, $mode, $quality);
		}
		
		return $uri;
	}
	
	public function getRealPathsFor($width = null, $height = null, $mode = null, $quality = null)
	{
		$files = [];
		$finder = new Finder();
		
		try {
			$finder->files()->in($this->getPathFor($width, $height, $mode, $quality))->name($this->getFileName());
		} catch (\InvalidArgumentException $e) {
			return [];
		}
		
		/** @var \SplFileInfo $file */
		foreach ($finder as $file) {
			$files[] = $file->getRealPath();
		}
		
		return $files;
	}
	
	public function removeFileFor($width = null, $height = null, $mode = null, $quality = null)
	{
		$realPath = $this->getRealPathFor($width, $height, $mode, $quality);
		
		if (true === $this->exists($realPath)) {
			FileUtil::getFs()->remove($realPath);
		}
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Oka\FileBundle\Model\ImageInterface::thumbnail()
	 */
	public function thumbnail($width = null, $height = null, $mode = null, $quality = null)
	{
		if ($height === null && $width === null) {
			throw new \LogicException('Vous devez au moins spécifier une taille pour créér une miniature.');
		}
		
		$path = $this->getRealPath();
		$mode = $mode ?: $this->thumbnailMode;
		$quality = $quality ?: $this->thumbnailQuality;
		
		if (false === $this->exists($path)) {
			throw new \LogicException(sprintf('Aucune image n\'a été trouvée dans ce chemin "%s".', $path));
		}
		
		$imagine = new \Imagine\Imagick\Imagine();
		/** @var \Imagine\Image\ImageInterface $image */
		$image = $imagine->open($path);
		$box = $image->getSize();
		
		if ($height !== null && $width !== null) {
			switch ($mode) {
				case 'ratio':
					$image->resize($box->widen($width));
						
					if (($width / $height) <= ($box->getWidth() / $box->getHeight())) {
						$image->resize($box->widen($width));
					} else {
						$image->resize($box->heighten($height));
					}
					break;
				case 'inset':
					$image = $image->thumbnail(new Box($width, $height), \Imagine\Image\ImageInterface::THUMBNAIL_INSET);
					break;
				case 'outbound':
					$image = $image->thumbnail(new Box($width, $height), \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND);
					break;
			}
		} elseif ($height === null && $width !== null && $box->getWidth() > $width) {
			$image->resize($box->widen($width));
				
		} elseif ($height !== null && $width === null && $box->getHeight() > $height) {
			$image->resize($box->heighten($height));
		}
		
		$dirPath = $this->getPathFor($width, $height, $mode, $quality);
		
		if (false === $this->exists($dirPath)) {
			$this->mkdir($dirPath);
		}
		
		$image->save($this->getRealPathFor($width, $height, $mode, $quality), ['quality' => $quality]);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Oka\FileBundle\Model\ImageManipulatorInterface::crop()
	 */
	public function crop($x0, $y0, $x1, $y1, $destination = null, $format = null)
	{
		if ($x1 < $x0 || $y1 < $y0) {
			throw new \LogicException('Les coordonnées "x1" et "y1" doivent être respectivement supérieures à "x0" et "y0".');
		}
		
		$path = $this->getRealPath();
		
		if (false === $this->exists($path)) {
			throw new \LogicException(sprintf('Aucune image n\'a été trouvée dans ce chemin "%s".', $path));
		}
		
		$imagine = new \Imagine\Imagick\Imagine();
		/** @var \Imagine\Image\ImageInterface $image */
		$image = $imagine->open($path);
		$image->crop(new Point($x0, $y0), new Box(($x1 - $x0), ($y1 - $y0)));
		
		if (null === $destination) {
			$this->removeFile();
		}
		
		$image->save($destination ?: $this->getRealPath(), $format === null ? [] : ['format' => $format]);
	}
	
	public function getPlaceholder()
	{
		return ImageUtils::getImageGIFPlaceholder($this->dominantColor);
	}
}