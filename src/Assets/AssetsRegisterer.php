<?php

namespace Qce\CoreBundle\Assets;

class AssetsRegisterer {
	public function __construct(
		private string $prefix,
		private string $asset_path,
		private array $assets
	) {
	}

	public function register_assets() {
		if (
			! function_exists( 'wp_register_script' )
			|| ! function_exists( 'wp_register_style' )
			|| ! function_exists( 'get_block_asset_url' )
		) {
			return;
		}

		array_map( $this->register_asset( ... ), $this->assets );
	}

	public function register_asset( string $name ): void {
		$path = "$this->asset_path/$name.asset.php";
		$uri  = get_block_asset_url( $this->asset_path );

		if ( ! file_exists( $path ) ) {
			return;
		}

		$asset = include $path;
		if ( file_exists( "$this->asset_path/$name.js" ) ) {
			\wp_register_script(
				$this->prefix . $name,
				"$uri/$name.js",
				$asset['dependencies'],
				$asset['version'],
				true
			);
		}

		if ( file_exists( "$this->asset_path/$name.css" ) ) {
			\wp_register_style( $this->prefix . $name, "$uri/$name.css", [], $asset['version'] );
		}
	}
}