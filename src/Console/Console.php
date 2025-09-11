<?php

namespace Qce\CoreBundle\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpKernel\KernelInterface;

class Console {
	public function __construct( private readonly KernelInterface $kernel ) {
	}

	public function register(): void {
		if ( ! class_exists( \WP_CLI::class ) ) {
			return;
		}

		\WP_CLI::add_command( 'qce', $this->run( ... ) );
	}

	public function run(): int {
		$args          = $_SERVER['argv'] ?? [];
		$command_start = array_search( 'qce', $args, true );
		if ( false === $command_start ) {
			return 1;
		}

		$input  = new ArgvInput( array_slice( $args, $command_start ) );
		$output = new ConsoleOutput();

		$kernel = $this->recreate_kernel( $input );
		$app    = new Application( $kernel );

		return $app->run( $input, $output );
	}

	protected function recreate_kernel( InputInterface $input ): KernelInterface {
		$kernel_env   = $this->kernel->getEnvironment();
		$kernel_debug = $this->kernel->isDebug();

		$env   = $input->getParameterOption( [ '--env', '-e' ], $kernel_env, true );
		$debug = $kernel_debug && ! $input->hasParameterOption( '--no-debug', true );

		// Only recreate kernel if env parameters are different from existing one.
		if ( $env !== $kernel_env || $debug !== $kernel_debug ) {
			return new ( $this->kernel::class )( $env, $debug );
		}

		return $this->kernel;
	}
}