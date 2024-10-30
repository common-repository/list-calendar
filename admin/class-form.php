<?php

/**
 * LTCR_Form
 *
 * 投稿フォームを管理します。
 */
class LTCR_Form
{
	/**
	 * @param LTCR_Post_Data $post_data
	 * @return string エスケープ済処済み投稿フォームのマークアップ
	 */
	public function get( $post_data )
	{
		return $this->form( $post_data );
	}

	/**
	 * 投稿フォームのマークアップ取得
	 * 
	 * @param LTCR_Post_Data $post_data
	 * @return string markup
	 */
	private function form( $post_data )
	{
		// new post_id is -1. Other post_id is existing id.
		$post_id = ( is_null( $post_data->id ) ) ? - 1 : $post_data->id;

		$html = '<div class="wrap">' . PHP_EOL
			. '<h2>' . esc_html( __( 'List Calendar Post', 'ltcr' ) ) . '</h2>';

		if ( false === empty( $post_data ) ) {
			if ( current_user_can( 'edit_others_posts' ) ) {
				$disabled = '';
			} else {
				$disabled = ' disabled="disabled"';
			}
		}
		$action_url = esc_url(
			add_query_arg(
				array( 'postid' => $post_data->id ),
				menu_page_url( 'ltcr', false )
			)
		);
		// form markup
		$html .= '<form method="post" action="' . $action_url . '" id="ltcr-admin-form-element">' . PHP_EOL;

		// security check (_wpnonce is wp number used once)
		$nonce = wp_nonce_field( 'save', '_wpnonce', true, false );
		$html .= $nonce . PHP_EOL;

		$html .= '<input type="hidden" id="post_id" name="post_id" value="' . (int) $post_id . '" />'
			. '<input type="hidden" id="ltcr-id" name="ltcr-id" value="'
			. (int) get_post_meta( $post_data->id, '_old_LTCR_unit_id', true )
			. '" />' . PHP_EOL
			. '<input type="hidden" id="hiddenaction" name="action" value="save" />' . PHP_EOL
			. '<div id="poststuff" class="metabox-holder">' . PHP_EOL
			. '<div id="titlediv">' . PHP_EOL
			. '<input type="text" id="ltcr-title" name="ltcr-title" size="40" value="'
			. esc_attr( $post_data->title ) . '"' . $disabled . ' />';

		if ( false === $post_data->is_new ) {
			$html .= '<p class="tagcode">' . PHP_EOL
				. esc_html(
					__( "Copy this code and paste it into your post, page or text widget content.", 'ltcr' )
				)
				. '<br>' . PHP_EOL
				. '<input type="text" id="ltcr-anchor-text" onfocus="this.select();" readonly="readonly">' . PHP_EOL
				. '</p>' . PHP_EOL;
		}
		if ( current_user_can( 'edit_others_posts' ) ) {
			$html .= '<div class="save-ltcr">' . PHP_EOL
				. '<input type="submit" class="button-primary" name="ltcr-save" value="' . esc_attr(
					__( 'Save', 'ltcr' )
				) . '" />' . PHP_EOL
				. '</div>';
		}
		$html .= '</div><!-- titlediv -->';
		$html .= '</form>';

		return $html;

	}
}
