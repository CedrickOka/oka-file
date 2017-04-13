<?php
namespace Oka\FileBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 
 * @author cedrick
 * 
 */
class UpgradeImageCommand extends ContainerAwareCommand
{
	/**
	 * Configure
	 * 
	 * @see \Symfony\Component\Console\Command\Command::configure()
	 */
	protected function configure()
	{
		$this->setName('okafile:upgrade:image')
		->setDescription('Upgrade images')
		->setDefinition([
				new InputArgument('class', InputArgument::OPTIONAL, 'Image class name', null)
		])
		->setHelp(<<<EOF
Upgrade images.
EOF
				);
	}
	
	/**
	 * Execute
	 * 
	 * @see \Symfony\Component\Console\Command\Command::execute()
	 */
	public function execute(InputInterface $input, OutputInterface $output) {
		/** @var \Symfony\Component\DependencyInjection\Container $container */
		$container = $this->getContainer();
		/** @var \Oka\FileBundle\Model\FileManagerInterface $imageManager */
		$imageManager = $container->get('oka_file.image_manager');
		/** @var \Oka\FileBundle\Service\UploadedImageManager $uploadedImageManager */
		$uploadedImageManager = $container->get('oka_file.uploaded_image.manager');
		$objectManager = $imageManager->getObjectManager();
		
		if ($class = $input->getArgument('class')) {
			$imageManager->setClass($class);
		}
		
		$offset = 0;
		$output->writeln('Upgrading images...');
		
		while ($images = $imageManager->findFilesBy([], [], 100, $offset)) {
			/** @var \Oka\FileBundle\Model\Image $image */
			foreach ($images as $image) {
				$colorRGB = $uploadedImageManager->findImageDominantColor($image->getRealPath());
				$image->setDominantColor($colorRGB);
				$image->setSize(filesize($image->getRealPath()));
				
				if (OutputInterface::VERBOSITY_NORMAL === $output->getVerbosity()) {
					$output->writeln(sprintf(
							'[<comment>%s</comment>] Image with path <info>%s</info>, with size <comment>%s Mo</comment>, to as dominant color <comment>#%s</comment>.',
							date('H:i:s'),
							$image->getRealPath(),
							round($image->getSize() / 1048576, 2),
							$colorRGB
					));
				}
			}
			
			$objectManager->flush();
			$offset += 100;
		}
	}
}
