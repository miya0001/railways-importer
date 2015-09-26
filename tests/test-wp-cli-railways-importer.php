<?php

namespace Railways;

class Railways_Importer_Test extends \WP_UnitTestCase
{
	/**
	 * @test
	 */
	public function csv_to_array()
	{
		$prefs = Util::csv_to_array( Util::get_pref_csv() );
		$this->assertSame( 48, count( $prefs ) );
		$this->assertSame( "27", $prefs[26]['pref_cd'] );
		$this->assertSame( "大阪府", $prefs[26]['pref_name'] );
	}

	/**
	 * @test
	 */
	public function insert_pref_term()
	{
		register_taxonomy( 'railways', 'post', array(
			'hierarchical' => true,
		) );

		Util::import_prefs( 'railways' );

		$terms = get_terms( 'railways', array( 'get' => 'all' ) );

		$this->assertSame( 48, count( $terms ) ); // その他が含まれるので48都道府県

		foreach ( $terms as $term ) {
			$this->assertTrue( !! preg_match( '/^[0-9]{2}$/', $term->slug ) );
		}
	}
}
