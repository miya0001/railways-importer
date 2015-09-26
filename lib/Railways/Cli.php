<?php

namespace Railways;

use Railways;
use \WP_CLI;
use \WP_CLI_Command;

/**
* 路線、駅データをカスタムタクソノミーにインポートする
*/
class Cli extends WP_CLI_Command
{
	/**
	 * 路線、駅データを指定したタクソノミーにインポートする
	 *
	 * ## OPTIONS
 	 *
 	 * <taxonomy>
 	 * : インポート先のタクソノミーの名前
	 *
	 * ## EXAMPLES
	 *
	 *     wp railways import <taxonomy>
	 *
	 * @subcommand import
	 */
	public function import( $args )
	{
		if ( empty( $args[0] ) || ! taxonomy_exists( $args[0] ) ) {
			WP_CLI::error( "No such taxonomy." );
		} else {
			Util::import_prefs( $args[0] );
			self::import_stations( $args[0] );
		}
	}

	/**
	 * 路線、駅データをインポートする
	 *
	 * @param string $taxonomy
	 * @return none
	 */
	public static function import_stations( $taxonomy )
	{
		// いちいちキャッシュのフラッシュをしないでというおまじない
		global $_wp_suspend_cache_invalidation;
		$_wp_suspend_cache_invalidation = true;

		$lines = Util::get_lines_array( Util::get_line_csv() ); // hash of lines

		foreach ( Util::csv_to_array( Util::get_station_csv()) as $station ) {
			if ( empty( $station['station_cd'] ) ) {
				continue;
			}

			// import lines
			$line_slug = Util::create_line_slug( $station['pref_cd'], $station['line_cd'] );
			$pref_term_id = Util::get_pref_term_id( $station['pref_cd'], $taxonomy );
			if ( is_wp_error( $pref_term_id ) ) {
				WP_CLI::error( $pref_term_id->get_error_message() );
			}

			// continue;
			if ( $result = term_exists( $lines[ $station['line_cd'] ], $taxonomy, $pref_term_id ) ) {
				$parent = $result['term_id'];
			} else {
				WP_CLI::line( $lines[ $station['line_cd'] ] );
				$result = wp_insert_term( $lines[ $station['line_cd'] ], $taxonomy, $args = array(
					'slug' => $line_slug,
					'parent' => $pref_term_id,
				) );
				if ( is_wp_error( $result ) ) {
					WP_CLI::error( "NG" );
				} else {
					$parent = $result['term_id'];
					WP_CLI::success( 'OK' );
				}
			}

			if ( ! $parent ) {
				WP_CLI::error( "Line not found." );
			}

			// imports station
			WP_CLI::line( $lines[ $station['line_cd'] ] . '/' . $station['station_name'] );
			$station_result = wp_insert_term( $station['station_name'], $taxonomy, $args = array(
				'slug' => Util::create_station_slug( $station['station_cd'] ),
				'parent' => $parent,
			) );

			if ( is_wp_error( $station_result ) ) {
				WP_CLI::warning( 'NG' );
			} else {
				wp_update_term( $station_result['term_id'], $taxonomy, array( 'term_group' => $station['station_g_cd'] ) );
				WP_CLI::success( 'OK' );
			}

			// 以下の2行を実行しないとterm_exists()でないと言われてしまう
			delete_option( "{$taxonomy}_children" );
			wp_cache_flush();
		}
	}
}
