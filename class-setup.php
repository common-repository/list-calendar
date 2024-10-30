<?php

/**
 * List Calendar(LTCR)セットアップ
 *
 */
class LTCR_Setup
{
	/*
	 * @var LTCR_Activation $activation
	 */
	public $activation;

	function __construct()
	{
		require_once LTCR_PLUGIN_DIR . '/common/class-day.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-activation.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-enqueue-script.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-menu.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-field.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-list.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-form.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-action.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-settings.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-validation.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-post-data.php';
		require_once LTCR_PLUGIN_DIR . '/admin/class-post-factory.php';
		require_once LTCR_PLUGIN_DIR . '/reader/class-calendar-drawing.php';
		require_once LTCR_PLUGIN_DIR . '/reader/class-calendar-data.php';
		$this->activation = new LTCR_Activation();
	}

	/**
	 * List Calendarセットアップ
	 */
	public function on()
	{
		// 権限はデフォルトを採用
		if ( is_admin() ) {
			// 管理側処理
			$menu= new LTCR_Menu();
			$menu->create();
			add_action( 'activate_' . LTCR_PLUGIN_BASENAME, array( &$this->activation, 'on' ) );
		} else {
			// 閲覧側処理
			$drawing = new LTCR_Calendar_Drawing();
			$drawing->run();
		}
	}

}
