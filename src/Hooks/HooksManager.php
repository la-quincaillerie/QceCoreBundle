<?php

namespace Qce\CoreBundle\Hooks;

class HooksManager {
	public function __construct( private readonly array $hooks ) {
	}

	public function register_hooks(): void {
		if ( ! \function_exists( 'add_filter' ) ) {
			return;
		}

		foreach ( $this->hooks as $hook ) {
			[ $service, $method ] = $hook[1];
			$hook[1] = static fn( ...$args ) => $service()->$method( ...$args );
			\add_filter( ...$hook );
		}
	}
}