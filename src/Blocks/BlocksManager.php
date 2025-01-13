<?php

namespace Qce\CoreBundle\Blocks;

use Symfony\Component\Config\ConfigCacheFactoryInterface;
use Symfony\Component\Config\ConfigCacheInterface;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\Config\Resource\GlobResource;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class BlocksManager implements CacheWarmerInterface {
	private ConfigCacheInterface $cache;

	public function __construct(
		private readonly string $blocks_dir,
		private readonly string $blocks_cache_file,
		private readonly ConfigCacheFactoryInterface $cache_factory,
	) {
	}

	public function register_blocks(): void {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		array_map( register_block_type( ... ), $this->get_blocks() );
	}

	private function get_blocks(): array {
		return ( include $this->compute_cache()->getPath() ) ?? [];
	}

	private function compute_cache(): ConfigCacheInterface {
		return $this->cache ??= $this->cache_factory->cache(
			$this->blocks_cache_file,
			function ( ConfigCacheInterface $cache ) {
				if ( is_dir( $this->blocks_dir ) ) {
					$resource = new GlobResource( $this->blocks_dir, "/**/block.json", true );
					$paths    = array_keys( iterator_to_array( $resource->getIterator() ) );
				} else {
					$resource = new FileExistenceResource( $this->blocks_dir );
					$paths    = [];
				}

				$cache->write( sprintf( '<?php return %s;', var_export( $paths, true ) ), [ $resource ] );
			}
		);
	}

	public function warmUp( string $cacheDir, ?string $buildDir = null ): array {
		return [ $this->compute_cache()->getPath() ];
	}

	public function isOptional(): bool {
		return true;
	}
}