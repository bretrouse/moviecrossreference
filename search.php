<?php

if( empty( $_GET['q'] ) ) {
	echo '[]';
	exit;
}

require( 'inc/init.php' );

$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : 'imdb';

$MCRDb = MCRDatabase::getDatabase( $engine );

echo json_encode( $MCRDb->search( $_GET['q'] ) );
