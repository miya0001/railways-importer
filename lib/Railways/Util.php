<?php

namespace Railways;

use Railways;
use League\Csv\Reader;

class Util
{
	const station_csv = '/station20150414free.csv';
	const line_csv = '/line20150414free.csv';
	const pref_csv = '/pref.csv';

	/**
	 * 都道府県データをインポートする。
	 *
	 * @param string $taxonomy
	 * @retrun none
	 */
	public static function import_prefs( $taxonomy )
	{
		foreach ( self::csv_to_array( self::get_pref_csv() ) as $pref ) {
			$result = wp_insert_term( $pref['pref_name'], $taxonomy, $args = array(
				'slug' => self::create_pref_slug( $pref['pref_cd'] )
			) );
		}
	}

	/**
	 * CSVデータを連想配列に変換
	 *
	 * @param string $csv_file
	 * @return array $csv
	 */
	public static function csv_to_array( $csv_file )
	{
		$reader = Reader::createFromPath( $csv_file );
		return $reader->fetchAssoc();
	}

	/**
	 * 路線データを連想配列で取得
	 *
	 * @param none
	 * @return array $lines
	 */
	public static function get_lines_array( $csv )
	{
		$lines = array();
		foreach ( self::csv_to_array( $csv ) as $line ) {
			$lines[ $line['line_cd'] ] = $line['line_name'];
		}

		return $lines;
	}

	/**
	 * 都道府県コードから都道府県タームのIDを取得
	 *
	 * @param int $pref_cd
	 * @param string $taxonomy
	 * @return int $term_id
	 */
	public static function get_pref_term_id( $pref_cd, $taxonomy )
	{
		$term = get_term_by( 'slug', self::create_pref_slug( $pref_cd ), $taxonomy );
		if ( ! $term ) {
			return new WP_Error( "", "No such prefecture at ".$pref_cd."." );
		}
		return $term->term_id;
	}

	/**
	 * 都道府県ターム用のslugを生成
	 *
	 * @param int $pref_cd
	 * @return string $slug
	 */
	public static function create_pref_slug( $pref_cd )
	{
		return sprintf( '%02d', $pref_cd );
	}

	/**
	 * 路線タームのslugを生成
	 *
	 * @param int $pref_cd
	 * @param int $line_cd
	 * @return string $slug
	 */
	public static function create_line_slug( $pref_cd, $line_cd )
	{
		return sprintf( '%06d', $line_cd ) . '-' .sprintf( '%02d', $pref_cd ) ;
	}

	/**
	 * 駅タームのslugを生成
	 *
	 * @param int $station_cd
	 * @return string $slug
	 */
	public static function create_station_slug( $station_cd )
	{
		return sprintf( '%09d', $station_cd );
	}

	public static function get_pref_csv()
	{
		if ( defined( 'RAILWAYS_CSV_DIR' ) && RAILWAYS_CSV_DIR && is_dir( RAILWAYS_CSV_DIR ) ) {
			if ( is_file( RAILWAYS_CSV_DIR . self::pref_csv ) ) {
				return RAILWAYS_CSV_DIR . self::pref_csv;
			}
		}

		die( 'CSV file for prefecture is not found.' );
		exit( 1 );
	}

	public static function get_line_csv()
	{
		if ( defined( 'RAILWAYS_CSV_DIR' ) && RAILWAYS_CSV_DIR && is_dir( RAILWAYS_CSV_DIR ) ) {
			if ( is_file( RAILWAYS_CSV_DIR . self::line_csv ) ) {
				return RAILWAYS_CSV_DIR . self::line_csv;
			}
		}

		die( 'CSV file for line is not found.' );
		exit( 1 );
	}

	public static function get_station_csv()
	{
		if ( defined( 'RAILWAYS_CSV_DIR' ) && RAILWAYS_CSV_DIR && is_dir( RAILWAYS_CSV_DIR ) ) {
			if ( is_file( RAILWAYS_CSV_DIR . self::station_csv ) ) {
				return RAILWAYS_CSV_DIR . self::station_csv;
			}
		}

		die( 'CSV file for station is not found.' );
		exit( 1 );
	}
}
