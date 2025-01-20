<?php

namespace Qce\CoreBundle;

use Qce\CoreBundle\DependencyInjection\HooksPass;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class QceCoreBundle extends AbstractBundle {
	public function build( ContainerBuilder $container ) {
		$container->addCompilerPass( new HooksPass() );
	}

	public function configure( DefinitionConfigurator $definition ): void {
		$definition->import( '../config/definition.php' );
	}

	public function loadExtension( array $config, ContainerConfigurator $container, ContainerBuilder $builder ): void {
		$container->parameters()
			->set('qce_core.assets.path', $config['assets']['build_path'])
			->set('qce_core.blocks.path', $config['assets']['blocks_path'])
			->set('qce_core.blocks.manifest', $config['assets']['blocks_manifest'])
		;

		$container->import( '../config/services.php' );

		if($config['assets']['register']) {
			$container->services()
				->set('qce_core.assets.registerer', \Qce\CoreBundle\Assets\AssetsRegisterer::class)
				->args([
					$config['assets']['prefix'],
					'%qce_core.assets.path%',
					$config['assets']['register']
				])
				->tag('qce_core.hook', ['name' => 'init', 'method' => 'register_assets'])
			;
		}

		if($config['assets']['enqueues']) {
			$container->services()
				->set('qce_core.assets.enqueuer', \Qce\CoreBundle\Assets\AssetsEnqueuer::class)
				->args([
					$config['assets']['enqueues'],
					$config['assets']['enqueue_scripts'],
					$config['assets']['enqueue_styles'],
				])
				->tag('qce_core.hook', ['name' => 'after_setup_theme', 'method' => 'hook_enqueues' ])
			;
		}
	}

	public function boot() {
		if ( $this->container->has( 'qce_core.hooks_manager' ) ) {
			$this->container->get( 'qce_core.hooks_manager' )->register_hooks();
		}

		set_exception_handler( null );
	}
}