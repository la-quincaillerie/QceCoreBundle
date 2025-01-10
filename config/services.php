<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Qce\CoreBundle\Hooks\WPHook;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function ( ContainerConfigurator $container, ContainerBuilder $builder ) {
	$container
		->services()
			->set( '.qce_core.constant_env_var_loader', \Qce\CoreBundle\DependencyInjection\EnvVarLoader::class )
				->tag( 'container.env_var_loader' )
			->set( 'qce_core.hooks_manager', \Qce\CoreBundle\Hooks\HooksManager::class )
				->public()
				->arg( 0, abstract_arg( 'Liste des hooks à enregistrer.' ) )
	;

	$builder->registerAttributeForAutoconfiguration( WPHook::class, static function ( ChildDefinition $definition, WPHook $hook, \ReflectionMethod|\ReflectionClass $reflector ): void {
		try {
			$args           = get_object_vars( $hook );
			$args['method'] = ( $reflector instanceof \ReflectionClass ? $reflector->getMethod( '__invoke' ) : $reflector )->getName();
			$definition->addTag( 'qce_core.hook', $args );
		} catch ( \ReflectionException $e ) {
			throw new InvalidConfigurationException( sprintf( '%s can only be used on methods or invokable services.', WPHook::class ), previous: $e );
		}
	} );

	if(class_exists(\Symfony\Component\Console\Application::class)) {
		$container
			->services()
				 ->set( 'qce_core.console', \Qce\CoreBundle\Console\Console::class )
		          ->args( [ service( 'kernel' ) ] )
		          ->tag( 'qce_core.hook', [ 'name' => 'cli_init', 'method' => 'register' ] );
	}
};