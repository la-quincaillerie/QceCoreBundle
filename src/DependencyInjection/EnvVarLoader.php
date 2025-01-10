<?php

namespace Qce\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\EnvVarLoaderInterface;

class EnvVarLoader implements EnvVarLoaderInterface {
	public function loadEnvVars(): array {
		$constants = get_defined_constants();

		if ( function_exists( 'get_stylesheet_directory' ) ) {
			$constants['WP_THEME_PATH'] ??= get_stylesheet_directory();
		}

		if ( function_exists( 'get_stylesheet_directory_uri' ) ) {
			$constants['WP_THEME_URI'] = get_stylesheet_directory_uri();
		}

		return $constants;
	}
}