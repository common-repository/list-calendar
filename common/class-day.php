<?php
/*
 * 曜日処理
 */
class LTCR_Day
{

	/**
	 * 曜日取得関数
	 *
	 * 年と月を指定して1日から末尾の曜日を取得
	 *
	 * @param number $year year
	 * @param number $month month
	 * @param bool $admin
	 * @return array day_of_week is day title array, days is day array
	 */
	public static function get_days( $year, $month, $admin = false )
	{
		$options = (array) json_decode( get_option( LTCR_POST_TYPE ) );
		if ( false === $admin ) {
			if ( false === isset( $options['ltcr-sun'] )
				|| '' === $options['ltcr-sun']
			) {
				$sun = null;
			} else {
				$sun = $options['ltcr-sun'];
			}
			$sun = ( false === empty( $sun ) ) ? $sun : 'Sun';
			if ( false === isset( $options['ltcr-mon'] )
				|| '' === $options['ltcr-mon']
			) {
				$mon = null;
			} else {
				$mon = $options['ltcr-mon'];
			}
			$mon = ( false === empty( $mon ) ) ? $mon : 'Mon';
			if ( false === isset( $options['ltcr-tue'] )
				|| '' === $options['ltcr-tue']
			) {
				$tue = null;
			} else {
				$tue = $options['ltcr-tue'];
			}
			$tue = ( false === empty( $tue ) ) ? $tue : 'Tue';
			if ( false === isset( $options['ltcr-wed'] )
				|| '' === $options['ltcr-wed']
			) {
				$wed = null;
			} else {
				$wed = $options['ltcr-wed'];
			}
			$wed = ( false === empty( $wed ) ) ? $wed : 'Wed';
			if ( false === isset( $options['ltcr-thu'] )
				|| '' === $options['ltcr-thu']
			) {
				$thu = null;
			} else {
				$thu = $options['ltcr-thu'];
			}
			$thu = ( false === empty( $thu ) ) ? $thu : 'Thu';
			if ( false === isset( $options['ltcr-fri'] )
				|| '' === $options['ltcr-fri']
			) {
				$fri = null;
			} else {
				$fri = $options['ltcr-fri'];
			}
			$fri = ( false === empty( $fri ) ) ? $fri : 'Fri';
			if ( false === isset( $options['ltcr-sat'] )
				|| '' === $options['ltcr-sat']
			) {
				$sat = null;
			} else {
				$sat = $options['ltcr-sat'];
			}
			$sat = ( false === empty( $sat ) ) ? $sat : 'Sat';
		} else {
			$sun = 'Sun';
			$mon = 'Mon';
			$tue = 'Tue';
			$wed = 'Wed';
			$thu = 'Thu';
			$fri = 'Fri';
			$sat = 'Sat';
		}

		$day_of_week = array(
			0 => esc_html( $sun ),
			1 => esc_html( $mon ),
			2 => esc_html( $tue ),
			3 => esc_html( $wed ),
			4 => esc_html( $thu ),
			5 => esc_html( $fri ),
			6 => esc_html( $sat )
		);

		$timestamp_1st = mktime( 0, 0, 0, $month, 1, $year ); // year年month月1日のUNIXTIME
		$day_1st       = date( 'w', $timestamp_1st );        // 1日の曜日（0:日～6:土）
		$total         = date( 't', $timestamp_1st );        // $y年$m月の日数

		$days = array();
		for ( $i = 1 - $day_1st; $i <= $total + 7; $i ++ ) {
			// 対象月の日付が有効な場合の処理
			if ( 0 < $i && $i <= $total ) {
				$timestamp = mktime( 0, 0, 0, $month, $i, $year );
				$day       = date( 'w', $timestamp ); // $i日の曜日（0:日～6:土）
				$days[$i]  = $day_of_week[$day];
			}
		}

		return array(
			'days'        => $days,
			'day_of_week' => $day_of_week,
		);

	}
}
