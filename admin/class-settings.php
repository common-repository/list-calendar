<?php

/**
 * LTCR_Settings
 *
 * オプション設定
 *
 * List Calendar > Settingsの設定を管理します。  
 * 全カレンダーで共通の設定です。
 *
 */
class LTCR_Settings
{
	/**
	 * @var array $options LTCR_OPTION_NAMEをキーとしてwp_optionsへ設定する配列
	 */
	private $options = array();
	/**
	 * @var array $errors 正しくない入力が行われたキーの配列
	 */
	private $errors = array();
	/**
	 * @var string $css CSS文字列
	 */
	private $css = '';

	/**
	 * options配列アップデート
	 *
	 * wp_optionsのltcr-optionにoptions配列をjson形式に変換して値を設定
	 *
	 * @param string $key ltcr-option
	 * @param string $referer update-settings
	 * @return bool
	 */
	private function update( $key = LTCR_OPTION_NAME, $referer = 'update-settings' )
	{
		if ( true === empty( $this->options ) ) {
			return false;
		}
		check_admin_referer( $referer );
		update_option( $key, json_encode( $this->options ) );
	}

	/**
	 * options連想配列にkey/value pairを追加
	 *
	 * @param $key   optionsのキー
	 * @param $value 設定値
	 */
	private function add_options( $key, $value )
	{
		if ( true === array_key_exists( $key, $this->options ) ) {
			$this->options[ $key ] = $value;
		} else {
			$this->options += array(
				$key => $value
			);
		}
	}

	/**
	 * フォームの入力値を検査しadd_option関数へ渡す
	 *
	 * @param string $key 更新対象のキー
	 * @param string $value 更新する値
	 * @return mix          キーがあれば検査済み値、無いときはfalse
	 */
	private function prepare( $key, $value = null )
	{
		if ( false === isset( $_POST[ $key ] ) ) {
			return false;
		}
		if ( true === is_null( $value ) ) {
			$value = $_POST[ $key ];
			$this->add_options( $key, $value );
		} else {
			$this->add_options( $key, $value );
		}
		return $value;
	}

	/**
	 * CSS幅・高さの検査・設定
	 *
	 * @param string $key options配列キー
	 * @param string $selector CSSセレクタ名
	 * @param string $property CSS設定値
	 * @return bool 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
	 */
	private function prepare_size( $key, $selector, $property )
	{
		if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
			return false;
		}

		$value = $_POST[ $key ];

		if ( true === LTCR_Validation::is_size( $value ) ) {
			$value = LTCR_Validation::normalize_size( $value );
			$value = $this->prepare( $key, $value );
			$this->css .= $selector . ' { ' . $property . ': ' . $value . '; }' . PHP_EOL;
			return true;
		}

		$this->errors[ $key ] = __( 'The inputted value is not right.', 'ltcr' );
		return false;
	}

	/**
	 * CSSボーダーの検査・設定
	 *
	 * @param string $key options配列キー
	 * @param string $selector CSSセレクタ
	 * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
	 */
	private function prepare_border( $key, $selector )
	{
		if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
			return false;
		}

		$value = $_POST[ $key ];

		if ( true === LTCR_Validation::is_color( $value ) ) {
			$value = LTCR_Validation::normalize_color( $value );
			$value = $this->prepare( $key, $value );
			$this->css .= $selector . '{ ' . 'border: 1px solid ' . $value . '; }' . PHP_EOL;
			return true;
		}

		$this->errors[ $key ] = __( 'The inputted value is not right.', 'ltcr' );
		return false;
	}


	/**
	 * CSSカラーの検査・設定
	 *
	 * @param string $key options配列キー
	 * @param string $selector CSSセレクタ名
	 * @param string $property CSS設定値
	 * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
	 */
	private function prepare_color( $key, $selector, $property )
	{
		if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
			return false;
		}

		$value = $_POST[ $key ];
		if ( true === LTCR_Validation::is_color( $value ) ) {
			$value = LTCR_Validation::normalize_color( $value );
			$value = $this->prepare( $key, $value );
			$this->css .= $selector . '{ ' . $property . ': ' . $value . '; }' . PHP_EOL;
			return true;
		}
		$this->errors[ $key ] = __( 'The inputted value is not right.', 'ltcr' );
		return false;
	}


	/**
	 * 配置を設定
	 *
	 * @param string $key options配列キー
	 * @param string $selector CSSセレクタ名
	 * @param string $property CSS設定値
	 * @return boolean 入力値が正しければtrue, 正しくない場合はerror配列に追加してfalseを返す
	 */
	private function prepare_align( $key, $selector, $property )
	{
		if ( false === isset( $_POST[ $key ] ) || '' === $_POST[ $key ] ) {
			return false;
		}

		$value = $_POST[ $key ];

		if ( true === LTCR_Validation::is_align( $value ) ) {
			$value = $this->prepare( $key, $value );
			$this->css .= $selector . '{ ' . $property . ': ' . $value . '; }' . PHP_EOL;
			return true;
		}

		$this->errors[ $key ] = __( 'The inputted value is not right.', 'ltcr' );
		return false;
	}


	/**
	 * 入力用コントロール作成
	 *
	 * @param string $name 表示名
	 * @param string $key name属性値
	 * @param string $example 入力例
	 * @return string 作成コントロールのマークアップ
	 */
	private function create_field( $name, $key, $example = '' )
	{
		$value = esc_attr( isset( $this->options[ $key ] ) ? $this->options[ $key ] : '' );
		$error = esc_attr( isset( $this->errors[ $key ] ) ? $this->errors[ $key ] : '' );
		$markup = '<tr>' . PHP_EOL;
		$markup .= '<th>' . $name . '</th>' . PHP_EOL;
		if ( 'ltcr-tag' === $key ) {
			$markup .= '<td><input type="text" name="' . $key . '" value="' . $value . '" size="40">';
		} else {
			$markup .= '<td><input type="text" name="' . $key . '" value="' . $value . '" size="12">';
		}
		$markup .= '&nbsp;<span class="ltcr-example">' . $example . '</span>'
			. '&nbsp;<span class="ltcr-error">' . $error . '</span></td>' . PHP_EOL;
		$markup .= '</tr>' . PHP_EOL;
		return $markup;
	}


	/**
	 * カレンダーのsettings設定画面
	 */
	public function manage_settings()
	{
		wp_enqueue_style(
			'ltcr-admin-css',
			LTCR_PLUGIN_URL . '/admin/css/style.css'
		);
		wp_enqueue_style(
			'ltcr-settings',
			LTCR_PLUGIN_URL . '/admin/css/settings.css'
		);

		if ( ! current_user_can( 'edit_others_posts' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page' ) );
			return false;
		}

		/*
		 * 更新
		 */
		// 日付に関連づける記事のタグ
		$this->prepare( 'ltcr-tag' );
		// 曜日見出し
		$this->prepare( 'ltcr-sun' );
		$this->prepare( 'ltcr-mon' );
		$this->prepare( 'ltcr-tue' );
		$this->prepare( 'ltcr-wed' );
		$this->prepare( 'ltcr-thu' );
		$this->prepare( 'ltcr-fri' );
		$this->prepare( 'ltcr-sat' );
		// 表示テーブルに設定するクラス
		$this->prepare( 'ltcr-table-class');
		// wp_options更新
		$this->update( LTCR_OPTION_NAME, 'update-settings' );

		/*
		 * 設定値取得
		 */
		$this->options = (array ) json_decode( get_option( LTCR_OPTION_NAME ) );

		/*
		 * 表示
		 */
		?>
		<div class="wrap">
			<h2>List Calendar Settings</h2>

			<form method="post" action="">
				<?php wp_nonce_field( 'update-settings' ); ?>
				<table class="form-table">
					<?php echo $this->create_field(
						'tag attached for search post',
						'ltcr-tag',
						'(tag name not tag id, tag slug. delimiter is comma.)'
					); ?>
					<?php echo $this->create_field( 'sunday', 'ltcr-sun', '(sunday label. e.g Sun)' ); ?>
					<?php echo $this->create_field( 'monday', 'ltcr-mon', '(monday label. e.g Mon)' ); ?>
					<?php echo $this->create_field( 'tuesday', 'ltcr-tue', '(tuesday label. e.g Tue)' ); ?>
					<?php echo $this->create_field( 'wednesday', 'ltcr-wed', '(wednesday label. e.g Wed)' ); ?>
					<?php echo $this->create_field( 'thursday', 'ltcr-thu', '(thursday label. e.g Tue)' ); ?>
					<?php echo $this->create_field( 'friday', 'ltcr-fri', '(friday label. e.g Fri)' ); ?>
					<?php echo $this->create_field( 'saturday', 'ltcr-sat', '(saturday label. Sat)' ); ?>
					<?php echo $this->create_field( 
						'table class', 
						'ltcr-table-class',
						'if you set class to table tag, input class name.' );
					?>
				</table>
				<p class="submit"><input type="submit" class="button-primary"
										 value="<?php echo esc_attr( __( 'Save', 'ltcr' ) ); ?>"/></p>
			</form>
		</div><!-- wrap -->
		<?php
	}
}
