<?php
namespace Oka\FileBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
	/**
	 * @var array $supportedDrivers
	 */
	protected static $supportedDrivers = ['orm', 'mongodb'];
	
	/**
	 * {@inheritDoc}
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode = $treeBuilder->root('oka_file');

		$rootNode
			->addDefaultsIfNotSet()
			->children()
				->scalarNode('db_driver')
					->cannotBeEmpty()
					->defaultValue('orm')
					->validate()
						->ifNotInArray(self::$supportedDrivers)
						->thenInvalid('The driver %s is not supported. Please choose one of '.json_encode(self::$supportedDrivers))
					->end()
				->end()
				->scalarNode('model_manager_name')->defaultNull()->end()
				->append($this->objectClassNodeDefinition())
				->append($this->storageNodeDefinition())
				->append($this->imageNodeDefinition())
				->append($this->behaviorsNodeDefinition())				
				->arrayNode('routing')
					->useAttributeAsKey('name')
					->prototype('array')
						->addDefaultsIfNotSet()
						->children()
							->scalarNode('file_class')->isRequired()->end()
							->scalarNode('host')->defaultNull()->end()
							->scalarNode('scheme')->defaultNull()->end()
							->scalarNode('prefix')
								->defaultNull()
								->treatNullLike('/')
								->validate()
								->ifTrue(function($value){
									if ($value) {
										if ($value == '/') {
											return false;
										}
										
										$i = strlen($value) - 1;
										return $value[0] !== '/' || $value[$i] === '/';
									}
								})
								->thenInvalid('Invalid prefix "%s". The prefix must start with "/" and can not end with "/".')
								->end()
							->end()
							->arrayNode('defaults')
								->useAttributeAsKey('name')
								->prototype('scalar')->end()
							->end()
						->end()
					->end()
				->end()
			->end();

		return $treeBuilder;
	}

	private function objectClassNodeDefinition()
	{
		$node = new ArrayNodeDefinition('object_default_class');
		$node
			->isRequired()
			->addDefaultsIfNotSet()
			->children()
				->scalarNode('image')->isRequired()->cannotBeEmpty()->end()
				->scalarNode('video')->defaultNull()->end()
				->scalarNode('audio')->defaultNull()->end()
				->scalarNode('file')->defaultNull()->end()
			->end()
		->end();
		
		return $node;
	}
	
	private function storageNodeDefinition()
	{
		$node = new ArrayNodeDefinition('storage');
		$node
			->addDefaultsIfNotSet()
			->isRequired()
			->children()
				->scalarNode('handler_id')
					->defaultNull()
					->info('The service ID of file storage handler.')
				->end()
				->scalarNode('root_path')
					->isRequired()
					->cannotBeEmpty()
					->validate()
						->ifTrue(function($value){
							$i = strlen($value) - 1;
							return $value[$i] == '/' || $value[$i] == '\\';
						})
						->thenInvalid('Invalid root path "%s". The path should not end with "/" or "\".')
					->end()
					->info('The path to the root folder that will host the files.')
				->end()
				->arrayNode('data_dirnames')
					->addDefaultsIfNotSet()
					->children()
						->append($this->createStorageDataDirnameNodeDefinition('image', 'images'))
						->append($this->createStorageDataDirnameNodeDefinition('video', 'videos'))
						->append($this->createStorageDataDirnameNodeDefinition('audio', 'audios'))
						->append($this->createStorageDataDirnameNodeDefinition('file', 'files'))
					->end()
				->end()
				->arrayNode('object_dirnames')
					->useAttributeAsKey('name')
					->treatNullLike([])
					->prototype('scalar')
						->validate()
							->ifTrue(function($value){
								if ($value) {
									$i = strlen($value) - 1;
									return ($value[0] == '/' || $value[0] == '\\') || ($value[$i] == '/' || $value[$i] == '\\');
								}
								return false;
							})
							->thenInvalid('Invalid dirname "%s". The path should not start and end with "/" or "\"')
						->end()
					->end()
				->end()
				->arrayNode('web_server')
					->addDefaultsIfNotSet()
					->isRequired()
					->children()
						->booleanNode('secure')
							->defaultFalse()
							->info('Use http or https protocol.')
						->end()
						->scalarNode('host')
							->defaultValue('localhost')
							->treatNullLike('localhost')
							->info('File storage server host.')
						->end()
						->scalarNode('port')->defaultNull()->end()
						->scalarNode('path')
							->defaultNull()
							->treatNullLike('')
							->validate()
								->ifTrue(function($value){
									if ($value) {
										$i = strlen($value) - 1;
										return $value[0] !== '/' || $value[$i] === '/';
									}
								})
								->thenInvalid('Invalid path "%s". The path must start with "/" and can not end with "/".')
							->end()
						->end()
					->end()
				->end()
			->end()
		->end();
		
		return $node;
	}
	
	private function createStorageDataDirnameNodeDefinition($name, $defaultValue)
	{
		$node = new ScalarNodeDefinition($name);
		$node
			->defaultValue($defaultValue)
			->treatNullLike('')
			->validate()
				->ifTrue(function($value){
					if ($value) {
						$i = strlen($value) - 1;
						return ($value[0] == '/' || $value[0] == '\\') || ($value[$i] == '/' || $value[$i] == '\\');
					}
					return false;
				})
				->thenInvalid('Invalid dirname "%s". The path should not start and end with "/" or "\"')
			->end()
			->info('The name of the folder where the resource will be stored if null then in the root dir.')
		->end();
		
		return $node;
	}
	
	private function imageNodeDefinition()
	{
		$node = new ArrayNodeDefinition('image');
		$node
			->addDefaultsIfNotSet()
			->children()
				->arrayNode('uploaded')
					->addDefaultsIfNotSet()
					->children()
						->arrayNode('detect_dominant_color')
							->treatNullLike(['enabled' => false, 'method' => 'quantize'])
							->treatFalseLike(['enabled' => false, 'method' => 'quantize'])
							->treatTrueLike(['enabled' => true, 'method' => 'quantize'])
							->addDefaultsIfNotSet()
							->canBeDisabled()
							->children()
								->scalarNode('method')
									->defaultValue('quantize')
									->validate()
										->ifNotInArray(['k-means', 'quantize'])
										->thenInvalid('Dominant color detection method "%s" is not valid. Valid methods are "'.implode(', ', ['k-means', 'quantize']).'".')
									->end()
								->end()
								->arrayNode('options')
									->prototype('variable')->end()
								->end()
							->end()
						->end()
						->scalarNode('default_thumbnail_factory')->defaultNull()->end()
						->arrayNode('thumbnail_factory')
							->treatNullLike([])
							->useAttributeAsKey('name')
							->prototype('array')
								->requiresAtLeastOneElement()
								->prototype('array')
									->validate()
										->ifTrue(function($value){
											return $value['width'] === null && $value['height'] === null;
										})
										->thenInvalid('Invalid factory, "width" and "height" can not be empty.')
									->end()
									->children()
										->integerNode('width')->defaultNull()->end()
										->integerNode('height')->defaultNull()->end()
										->append($this->thumbnailModeNodeDefinition())
										->integerNode('quality')->defaultNull()->end()
									->end()
								->end()
							->end()
						->end()
					->end()
				->end()
				->arrayNode('thumbnail')
					->addDefaultsIfNotSet()
					->children()
						->append($this->thumbnailModeNodeDefinition('ratio'))
						->integerNode('quality')->defaultValue(100)->end()
					->end()
				->end()
			->end()
		->end();
		
		return $node;
	}
	
	private function thumbnailModeNodeDefinition($defaultValue = null)
	{
		$node = new ScalarNodeDefinition('mode');
		$node
			->defaultValue($defaultValue)
			->validate()
				->ifNotInArray(['inset', 'outbound', 'ratio'])
				->thenInvalid('Invalid mode "%s" for thumbnail.')
			->end()
		->end();
		
		return $node;
	}
	
	private function behaviorsNodeDefinition()
	{
		$node = new ArrayNodeDefinition('behaviors');
		$node
			->canBeDisabled()
			->addDefaultsIfNotSet()
			->treatNullLike(['enabled' => false])
			->children()
				->arrayNode('reflection')
					->addDefaultsIfNotSet()
					->children()
						->booleanNode('enable_recursive')->defaultTrue()->end()
					->end()
				->end()
				->arrayNode('picture_coverizable')
					->addDefaultsIfNotSet()
					->canBeDisabled()
					->children()
						->append($this->createBehaviorMappingNodeDefinition())
					->end()
				->end()
				->arrayNode('avatarizable')
					->addDefaultsIfNotSet()
					->canBeDisabled()
					->children()
						->append($this->createBehaviorMappingNodeDefinition())
						->arrayNode('default_avatar_url')->end()
					->end()
				->end()
			->end()
		->end();
		
		return $node;
	}
	
	private function createBehaviorMappingNodeDefinition()
	{
		$node = new ArrayNodeDefinition('mappings');
		$node
			->useAttributeAsKey('name')
			->prototype('array')
				->children()
// 					->scalarNode('target_entity')
// 						->setDeprecated('The "%path%.%node%" configuration key is deprecated since version 1.2.0. Use "%path%.target_object" instead.')
// 						->cannotBeEmpty()
// 					->end()
					->scalarNode('target_object')->cannotBeEmpty()->end()
					->booleanNode('embedded')
						->defaultTrue()
						->info('Used only with the database driver `mongodb`.')
					->end()
					->scalarNode('fetch')
						->defaultValue('EAGER')
						->validate()
							->ifTrue(function($value){
								return !in_array(strtoupper($value), ['EAGER', 'LAZY', 'EXTRA_LAZY']);
							})
							->thenInvalid('Invalid fetch mode "%s"! The fetch mode must be EAGER, LAZY or EXTRA_LAZY.')
						->end()
					->end()
					->arrayNode('options')
						->treatNullLike([])
						->prototype('scalar')->end()
					->end()
				->end()
			->end()
		->end();
		
		return $node;
	}
}