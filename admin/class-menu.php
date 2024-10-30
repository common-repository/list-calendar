<?php

/**
 * LTCR_Menu
 *
 * @property LTCR_Form $form
 * @property LTCR_Action $action
 * @property LTCR_List $list
 * @property LTCR_Settings $settings
 */
class LTCR_Menu
{
	/** @var LTCR_Form */
	public $form;
	/** @var LTCR_Action */
	public $action;
	/** @var LTCR_List */
	public $list;
	/** @var LTCR_Enqueue_Script */
	public $enqueue_script;
	/** @var LTCR_Settings */
	public $settings;

	function __construct()
	{
		$this->form           = new LTCR_Form();
		$this->action         = new LTCR_Action();
		$this->enqueue_script = new LTCR_Enqueue_Script();
		$this->settings       = new LTCR_Settings();
	}

	/**
	 * 管理画面作成
	 */
	public function create()
	{
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_enqueue_scripts', array( $this->enqueue_script, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'my_admin_notices' ) );
	}

	/**
	 * 管理画面追加
	 */
	public function admin_menu()
	{
		if ( current_user_can( 'subscriber' ) ) {
			return;
		}
		/*
		 * メニュー追加
		 */
		add_object_page(
			'List Calendar',
			'List Calendar',
			'read',
			'ltcr',
			array( &$this, 'manage_menu' )
		);
		/*
		 * 一覧ページ追加
		 */
		$list_page = add_submenu_page(
			'ltcr',
			__( 'List Calendar', 'ltcr' ),
			__( 'List', 'ltcr' ),
			'edit_others_posts',
			'ltcr',
			array( $this, 'manage_menu' )
		);
		// 一覧ページの各項目(新規作成、削除、コピー)のコールバックへLTCR_Action->manage_postを設定
		// 一覧で新規作成、削除、コピーを実行したときは下記順序で関数が実行されます。
		// 1. LTCR_Action->manage_post
		// 2. manage_menu
		add_action( 'load-' . $list_page, array( $this->action, 'manage_post' ) );
		/*
		 * 新規追加
		 */
		add_submenu_page(
			'ltcr',
			__( 'Add New', 'ltcr' ),
			__( 'Add New', 'ltcr' ),
			'edit_others_posts',
			'ltcr-new',
			array( $this->action, 'manage_new_post' )
		);
		/*
		 * 外観(カレンダーオプション)追加
		 */
		add_submenu_page(
			'ltcr',
			__( 'Edit Settings', 'ltcr' ),
			__( 'Settings', 'ltcr' ),
			'edit_others_posts',
			'ltcr-settings',
			array( $this->settings, 'manage_settings' )
		);
	}

	/**
	 * List
	 *
	 * 既存の投稿は編集画面表示しその他はリストを表示します。
	 */
	public function manage_menu()
	{
		$post_data = $this->action->get_post_data();
		if ( $post_data ) {
			// 編集画面
			echo $this->action->get_edit_page( $post_data );
		} else {
			// 一覧画面
			echo $this->create_list();
		}
	}

	/**
	 * 一覧画面マークアップ取得
	 *
	 * @return string マークアップ
	 */
	public function create_list()
	{

		$this->list = new LTCR_List();
		$this->list->prepare_items();

		ob_start();

		$html = '<div class="wrap">' . PHP_EOL
			. '<h1>' . PHP_EOL . 'List Calendar';
		if ( current_user_can( 'administrator') || current_user_can( 'editor') ) {
			$html .= ' <a href="admin.php?page=ltcr&action=new" class="page-title-action">' 
				. esc_html( __( 'Add New', 'ltcr' ) ) . '</a>';
		}

		// 検索結果テキスト表示
		if ( ! empty( $_REQUEST['s'] ) ) {
			$html .= sprintf(
				'<span class="subtitle">'
				. __( 'Search results for &#8220;%s&#8221;', 'ltcr' )
				. '</span>',
				esc_html( $_REQUEST['s'] )
			);
		}
		$html .= '</h1>' . PHP_EOL;
			
		// 投稿検索フォーム
		$html .= '<form method="get" action="">'
			. '<input type="hidden" name="page" value="' . esc_attr( $_REQUEST['page'] ) . '" />';
		$this->list->search_box( __( 'Search Calendar', 'ltcr' ), 'ltcr' );
		$this->list->display();
		
		$html .= ob_get_contents();
		
		ob_clean();

		$html .= '</form></div>';
		echo $html;
	}


	/**
	 * エラー制御
	 */
	public function my_admin_notices() {
		?>
		<?php if ( $messages = get_transient( 'ltcr-custom-admin-errors' ) ): ?>
			<div class="error">
				<ul>
					<?php foreach( $messages as $message ): ?>
						<li><?php echo esc_html($message); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
		<?php
	}
}


