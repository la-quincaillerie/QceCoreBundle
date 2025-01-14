<?php

namespace Qce {
	if (
		! class_exists( Kernel::class )
		&& trait_exists( \Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait::class )
		&& function_exists( 'get_stylesheet_directory' )
	) {
		class Kernel extends \Symfony\Component\HttpKernel\Kernel {
			use \Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

			private string $project_dir;

			public function getProjectDir(): string {
				return $this->project_dir ??= get_stylesheet_directory();
			}
		}
	}
}

namespace {
	if ( ! class_exists( \Qce\Kernel::class ) ) {
		return;
	}

	if ( ! function_exists( 'qce_kernel' ) ) {
		function qce_kernel( string $env = null, bool $debug = null ) {
			static $kernel;

			return $kernel ??= new \Qce\Kernel(
				match ( $env ??= \wp_get_environment_type() ) {
					'production' => 'prod',
					'local', 'staging', 'development' => 'dev',
					default => $env,
				},
				$debug ?? WP_DEBUG
			);
		}
	}

	if ( ! function_exists( 'qce' ) ) {
		function qce( ?string $id = null ) {
			$container = qce_kernel()->getContainer();

			return $id ? $container->get( $id ) : $container;
		}
	}
}