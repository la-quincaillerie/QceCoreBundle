<?php

namespace Qce\CoreBundle\Assets;

class AssetsEnqueuer {
	public function __construct(
		private array $both,
		private array $scripts,
		private array $styles
	) {
	}

	public function hook_enqueues(): void {
		if ( ! function_exists( 'add_action' ) ) {
			return;
		}

		foreach ( array_keys(  $this->both + $this->scripts + $this->styles  ) as $hook ) {
			$scripts = array_merge( $this->both[ $hook ] ?? [], $this->scripts[ $hook ] ?? [] );
			$styles  = array_merge( $this->both[ $hook ] ?? [], $this->styles[ $hook ] ?? [] );

			add_action( $hook, static function () use ( $scripts, $styles ) {
				array_walk( $scripts, 'wp_enqueue_script' );
				array_walk( $styles, 'wp_enqueue_style' );
			} );
		}
	}
}