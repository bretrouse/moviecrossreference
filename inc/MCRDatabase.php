<?php

require_once('TMDbDatabase.php');
require_once('IMDbDatabase.php');

abstract class MCRDatabase
{
	
	public function getDatabase( $engine )
	{
		if( $engine == 'tmdb' ) {
			global $tmdb_api_key;
			return new TMDbDatabase( $tmdb_api_key );
		} else {
			return new IMDbDatabase();
		}
	}
	
	public function toShortCode( $num )
	{
	    $index = "0123456789abcdefghijklmnopqrstuvwyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $out = "";
	    for ( $t = floor( log10( $num ) / log10( 61 ) ); $t >= 0; $t-- ) {
	        $a = floor( $num / pow( 61, $t ) );
	        $out = $out . substr( $index, $a, 1 );
	        $num = $num - ( $a * pow( 61, $t ) );
	    }
	    return $out;
	}
	
	public function fromShortCode( $num )
	{
	    $index = "0123456789abcdefghijklmnopqrstuvwyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $out = 0;
	    $len = strlen( $num ) - 1;
	    for ( $t = 0; $t <= $len; $t++ ) {
	        $out = $out + strpos( $index, substr( $num, $t, 1 ) ) * pow( 61, $len - $t );
	    }
	    return $out;
	}
	
	public function getROFromShortCode( $short )
	{
		$concat = self::fromShortCode( $short );
		$type = substr($concat, -1);
		$id = substr($concat, 0, -1);
		return $this->getReferenceObject( str_pad($id,7,"0",STR_PAD_LEFT), $type );
	}
	
	abstract public function search( $name );
	abstract public function getReferenceObject( $id, $type );
	
}

