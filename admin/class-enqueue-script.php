<?php

/**
 * LTCR_Enqueue_Script
 */
class LTCR_Enqueue_Script
{
	public function enqueue_scripts( $hook_suffix )
	{
		if ( false === strpos( $hook_suffix, 'ltcr' ) ) {
			return;
		}

		wp_enqueue_style(
			'ltcr-admin',
			LTCR_PLUGIN_URL . '/admin/css/styles.css',
			array( 'thickbox' ),
			LTCR_VERSION,
			'all'
		);

		wp_enqueue_script(
			'ltcr-admin-scripts',
			LTCR_PLUGIN_URL . '/admin/js/scripts.js',
			array(
				'jquery',
				'thickbox',
				'postbox'
			),
			LTCR_VERSION,
			true
		);
		wp_enqueue_script(
			'ltcr-admin',
			LTCR_PLUGIN_URL . '/admin/js/admin.js',
			array(),
			LTCR_VERSION,
			true
		);

		wp_enqueue_script(
			'ltcr-admin-custom-fields',
			LTCR_PLUGIN_URL . '/admin/js/custom_fields.js',
			array(),
			LTCR_VERSION,
			true
		);

		wp_enqueue_script(
			'ltcr-admin-custom-fields_handler',
			LTCR_PLUGIN_URL . '/admin/js/custom_fields_handler.js',
			array(),
			LTCR_VERSION,
			true
		);
	}
}
