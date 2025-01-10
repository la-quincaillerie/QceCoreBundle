<?php

namespace Qce\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class HooksPass implements CompilerPassInterface {
	public function process( ContainerBuilder $container ): void {
		if ( ! $container->hasDefinition( 'qce_core.hooks_manager' ) ) {
			return;
		}

		$tagged_services = $container->findTaggedServiceIds( 'qce_core.hook' );

		$hooks = [];
		foreach ( $tagged_services as $id => $tags ) {
			foreach ( $tags as $tag ) {
				$hooks[] = [
					$tag['name'],
					[ new ServiceClosureArgument( new Reference( $id ) ), $tag['method'] ?? '__invoke' ],
					$tag['priority'] ?? 10,
					$tag['accepted_args'] ?? 1,
				];
			}
		}

		if ( $hooks ) {
			$container->getDefinition( 'qce_core.hooks_manager' )->setArgument( 0, $hooks );
		} else {
			$container->removeDefinition( 'qce_core.hooks_manager' );
		}
	}
}