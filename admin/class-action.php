<?php

/**
 * LTCR_Action
 *
 * List Calendarの(カスタム)投稿のCRUD処理を行います
 *
 * @property LTCR_Post_Data $post_data 投稿データ
 * @property LTCR_Form $form 投稿フォーム
 */
class LTCR_Action
{

	const post_type = LTCR_POST_TYPE;
	/** @var LTCR_POST_Data */
	public $post_data;

	/** @var LTCR_Form */
	public $form;

	function __construct()
	{
		$this->post_data = null;
		$this->form      = new LTCR_Form();
	}

	/**
	 * 投稿データ取得
	 */
	public function get_post_data()
	{
		return $this->post_data;
	}

	/**
	 * 投稿編集画面取得
	 *
	 * @param LTCR_Post_Data $post_data
	 * @return string マークアップ
	 */
	public function get_edit_page( $post_data )
	{
		$form = $this->form->get( $post_data );
		/*
		 * カスタムフィールド設定した投稿フォーム取得
		 */
		$form = LTCR_Field::set( $post_data, $form );
		return $form;
	}

	/**
	 * 新規投稿処理
	 * 
	 * List Calendar > Add Newメニューの処理です。
	 */
	public function manage_new_post() {
		$post_data = LTCR_Post_Factory::get_post_data();
		echo $this->get_edit_page($post_data);
	}

	/**
	 * List CalendarのCURD処理
	 *
	 * 編集
	 * wp-admin/admin.php?page=ltcr&postid=<postid>&action=edit&_wpnonce=<token>
	 * コピー
	 * wp-admin/admin.php?page=ltcr&postid=<postid>&action=copy&_wpnonce=<token>
	 * 削除
	 * wp-admin/admin.php?page=ltcr&postid=<postid&action=delete&_wpnonce=<token>
	 *
	 * List Calendarページ表示処理順序
	 *
	 * 1. 当メソッド(manage_post)
	 * 2. LTCR_Menu->manage_page
	 *
	 * LTCR_Admin_Controller->admin_management_pageより先に処理される。
	 */
	public function manage_post()
	{
		$action    = false;
		$post_data = null;
		$post_id   = isset( $_GET['postid'] ) ? (int) $_GET['postid'] : '';
		if ( isset( $_REQUEST['action'] ) ) {
			$action = $_REQUEST['action'];
		}
		/*
		 * 投稿の処理はAdministratorとEditorのみ行うことができます。
		 * 投稿に関する権限はAdministratorとEditorで違いはありません。
		 */
		$e = new WP_Error();
		// new
		if ( 'new' === $action ) {
			if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
				$post_data = LTCR_Post_Factory::get_post_data();
			} else {
				$e->add(
					'error',
					__(
						'You are not allowed to add post.',
						'my-custom-admin'
					)
				);
				set_transient( 'ltcr-custom-admin-errors', $e->get_error_messages(), 10 );
				$redirect_to = add_query_arg( array(), menu_page_url( 'ltcr', false ) );
				wp_safe_redirect( $redirect_to );
			}
		}
		// save
		if ( 'save' === $action ) {
			if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
				$this->save();
			} else {
				wp_die( __( 'You are not allowed to add post.', 'ltcr' ) );
			}
		}
		// edit
		if ( 'edit' === $action ) {
			if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
				$post_data = LTCR_Post_Factory::get_post_data( $post_id );
			} else {
				$e->add(
					'error',
					__(
						'You are not allowed to edit this post.',
						'my-custom-admin'
					)
				);
				set_transient( 'ltcr-custom-admin-errors', $e->get_error_messages(), 10 );
				$redirect_to = add_query_arg( array(), menu_page_url( 'ltcr', false ) );
				wp_safe_redirect( $redirect_to );
			}
		}
		// copy
		if ( 'copy' === $action ) {
			if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
				$this->copy();
			} else {
				$e->add(
					'error',
					__(
						'You are not allowed to copy this post.',
						'my-custom-admin'
					)
				);
				set_transient( 'ltcr-custom-admin-errors', $e->get_error_messages(), 10 );
				$redirect_to = add_query_arg( array(), menu_page_url( 'ltcr', false ) );
				wp_safe_redirect( $redirect_to );
			}
		}
		// delete
		if ( 'delete' == $action ) {
			if ( current_user_can( 'administrator' ) || current_user_can( 'editor' ) ) {
				$this->delete();
			} else {
				$e->add(
					'error',
					__(
						'You are not allowed to delete this post.',
						'my-custom-admin'
					)
				);
				set_transient( 'ltcr-custom-admin-errors', $e->get_error_messages(), 10 );
				$redirect_to = add_query_arg( array(), menu_page_url( 'ltcr', false ) );
				wp_safe_redirect( $redirect_to );
			}
		}
		if ( $post_data ) {
			// new, save, edit, copy
			$this->post_data = $post_data;
		} else {
			// 一覧ページ表示
			$current_screen = get_current_screen();
			add_filter(
				'manage_' . $current_screen->id . '_columns',
				array( 'LTCR_List', 'define_columns' )
			);
		}
	}

	/**
	 * 投稿保存
	 *
	 * 投稿の識別ID{$id}は新規投稿は-1, 既存投稿はpost->IDになる
	 */
	private function save()
	{
		// new post id that is not saved is -1
		$id = (int) $_POST['post_id'];
		// check nonce
		check_admin_referer( 'save' );

		// check capability
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_die( __( 'You are not allowed to edit this post.', 'ltcr' ) );
		}
		
		/*
		 * get post_data
		 */
		$this->post_data = LTCR_Post_Factory::get_post_data( $id );
		if ( false === $this->post_data ) {
			$this->post_data         = LTCR_Post_Factory::get_post_data();
			$this->post_data->is_new = true;
		}
		$this->post_data->title = trim( $_POST['ltcr-title'] );

		/*
		 * 投稿データ保存
		 */
		$this->post_save( $this->post_data );
		/*
		 * カスタムフィールド更新
		 */
		LTCR_Field::update( $this->post_data );
		$query                  = array();
		$query['action']        = 'edit';
		$query['postid'] = $this->post_data->id;
		$redirect_to     = add_query_arg( $query, menu_page_url( 'ltcr', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}

	/**
	 * 保存
	 */
	private function post_save( $post_data )
	{
		if ( $post_data->is_new ) {
			// 新規
			$post_id = wp_insert_post(
				array(
					'post_type'   => self::post_type,
					'post_status' => 'publish',
					'post_title'  => $post_data->title
				)
			);
		} else {
			// 更新
			$post_id = wp_update_post(
				array(
					'ID'          => (int) $post_data->id,
					'post_status' => 'publish',
					'post_title'  => $post_data->title
				)
			);
		}
		// 新規フラグをfalseへ設定
		if ( $post_id ) {
			if ( $post_data->is_new ) {
				$post_data->is_new = false;
				$post_data->id     = $post_id;
			}
		}
		return $post_id;
	}

	/**
	 * 投稿コピー
	 */
	private function copy()
	{
		// get post id
		$id = empty( $_POST['post_id'] ) ? absint( $_REQUEST['postid'] ) : absint( $_POST['post_id'] );
		// check nonce
		check_admin_referer( 'copy' );
		// check capability
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_die( __( 'You are not allowed to edit this post.', 'ltcr' ) );
		}
		$query = array();
		if ( $this->post_data = LTCR_Post_Factory::get_post_data( $id ) ) {
			$new_post_data = $this->post_copy( $this->post_data );
			$this->post_save( $new_post_data );
			$query['postid'] = $new_post_data->id;
		} else {
			$query['postid'] = $this->post_data->id;
		}
		$redirect_to = add_query_arg( $query, menu_page_url( 'ltcr', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}

	/**
	 * コピー
	 */
	private function post_copy( $post_data )
	{
		$new_post              = LTCR_Post_Factory::get_post_data();
		$new_post->is_new_post = true;
		$new_post->title       = $post_data->title . '_copy';
		return $new_post;
	}

	/**
	 * 投稿削除
	 */
	private function delete()
	{
		if ( ! empty( $_POST['post_id'] ) ) {
			check_admin_referer( 'delete' );
		} elseif ( ! is_array( $_REQUEST['postid'] ) ) {
			check_admin_referer( 'delete' );
		} else {
			// bulk-postidsのpostidsはLTCR_Listでpluralに指定した値
			check_admin_referer( 'bulk-postids' );
		}

		$post_ids = empty( $_POST['post_id'] )
			? (array) $_REQUEST['postid']
			: (array) $_POST['post_id'];

		$deleted = 0;

		foreach ( $post_ids as $post_id ) {
			$post_data = LTCR_Post_Factory::get_post_data( $post_id );

			if ( empty( $post_data ) ) {
				continue;
			}

			if ( ! current_user_can( 'delete_posts' ) ) {
				wp_die( __( 'You are not allowed to delete this post.', 'ltcr' ) );
			}

			if ( ! $this->post_delete( $post_data ) ) {
				wp_die( __( 'Error in deleting.', 'ltcr' ) );
			}

			$deleted += 1;
		}
		$query       = array();
		$redirect_to = add_query_arg( $query, menu_page_url( 'ltcr', false ) );
		wp_safe_redirect( $redirect_to );
		exit();
	}

	/**
	 * 削除
	 */
	private function post_delete( $post_data )
	{
		if ( $post_data->is_new ) {
			return true;
		}
		if ( wp_delete_post( $post_data->id, true ) ) {
			unset( $post_data );
			return true;
		}
		return false;
	}

	
}
