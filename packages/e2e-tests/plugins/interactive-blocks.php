<?php
/**
 * Plugin Name: Gutenberg Test Interactive Blocks
 * Plugin URI: https://github.com/WordPress/gutenberg
 * Author: Gutenberg Team
 *
 * @package gutenberg-test-interactive-blocks
 */

add_action(
	'init',
	function () {
		// Register all blocks found in the `interactive-blocks` folder.
		if ( file_exists( __DIR__ . '/interactive-blocks/' ) ) {
			$block_json_files = glob( __DIR__ . '/interactive-blocks/**/block.json' );

			// Auto register all blocks that were found.
			foreach ( $block_json_files as $filename ) {
				$block_folder = dirname( $filename );
				$name         = basename( $block_folder );

				$view_file = plugin_dir_url( $block_folder ) . $name . '/' . 'view.js';

				gutenberg_register_module(
					$name . '-view',
					$view_file,
					array( '@wordpress/interactivity' ),
					filemtime( $view_file ),
					true
				);

				register_block_type_from_metadata( $block_folder );
			}
		}

		// Temporary fix to disable SSR of directives during E2E testing. This
		// is required at this moment, as SSR for directives is not stabilized
		// yet and we need to ensure hydration works, even when the rendered
		// HTML is not correct or malformed.
		if ( 'true' === $_GET['disable_directives_ssr'] ) {
			remove_filter(
				'render_block_data',
				'gutenberg_interactivity_mark_root_blocks'
			);
		}
	}
);
