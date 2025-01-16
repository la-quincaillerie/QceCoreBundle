<?php

namespace Qce\CoreBundle\Blocks;

class BlocksManager {
	public function __construct(
		private readonly string $blocks_path,
		private readonly string $blocks_manifest,
	) {
	}

	public function register_blocks(): void {
		if ( ! function_exists( 'register_block_type' ) || ! is_readable( $this->blocks_manifest ) ) {
			return;
		}

		$blocks = array_keys( ( include $this->blocks_manifest ) ?? [] );
		$paths  = array_map( fn( $dir ) => "$this->blocks_path/$dir/block.json", $blocks );
		foreach ( $paths as $path ) {
			register_block_type( $path );
		}
	}
}