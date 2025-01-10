<?php

namespace Qce\CoreBundle;

use Qce\CoreBundle\DependencyInjection\HooksPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class QceCoreBundle extends AbstractBundle {
	public function build( ContainerBuilder $container ) {
		$container->addCompilerPass( new HooksPass() );
	}

	public function loadExtension( array $config, ContainerConfigurator $container, ContainerBuilder $builder ): void {
		$container->import( '../config/services.php' );
	}

	public function boot() {
		if ( $this->container->has( 'qce_core.hooks_manager' ) ) {
			$this->container->get( 'qce_core.hooks_manager' )->register_hooks();
		}
	}
}