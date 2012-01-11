<?php
require('inc/init.php');


if( ( isset( $_REQUEST['short_code'] ) && $_REQUEST['short_code'] != '')
		|| ( isset( $_REQUEST['first'] ) && $_REQUEST['first'] != ''
 				 && isset( $_REQUEST['second'] ) && $_REQUEST['second'] != '' ) ) {
	
	$engine = isset( $_REQUEST['engine'] ) ? $_REQUEST['engine'] : 'imdb';
	
	$MCRDb = MCRDatabase::getDatabase( $engine );

	if( isset( $_REQUEST['short_code'] ) && $_REQUEST['short_code'] != '' ) {
		$short_codes = explode( 'x', $_REQUEST['short_code'] );
		$item1 = $MCRDb->getROFromShortCode( $short_codes[0] );
		$item2 = $MCRDb->getROFromShortCode( $short_codes[1] );
	} else {
		if( $_REQUEST['first-id'] ) {
			$first_id = $_REQUEST['first-id'];
			$first_type = $_REQUEST['first-type'];
		} else {
			$results = $MCRDb->search( $_REQUEST['first'] );
			$first_id = $results[0]['id'];
			$first_type = $results[0]['type'];
		}
		if( $_REQUEST['second-id'] ) {
			$second_id = $_REQUEST['second-id'];
			$second_type = $_REQUEST['second-type'];
		} else {
			$results = $MCRDb->search( $_REQUEST['second'] );
			$second_id = $results[0]['id'];
			$second_type = $results[0]['type'];
		}	
		$item1 = $MCRDb->getReferenceObject( $first_id, $first_type );
		$item2 = $MCRDb->getReferenceObject( $second_id, $second_type );
	}
	
	if( $item1->getType() == $item2->getType() )
		$matches = array_keys( $item1->crossWith( $item2 ) );
	else
		$matches = $item1->getType() == MCRReferenceObject::MOVIE ? $item1->crossWith( $item2 ) : $item2->crossWith( $item1 );
		
	$ref1 = $item1->getReferences();
	$ref2 = $item2->getReferences();
	
	$short_code = $item1->getShortCode() . 'x' . $item2->getShortCode();
	
	$title = $item1->getName() . " x " . $item2->getName() . " - Movie Cross Reference";
	
}
include('templates/header.html.php');
	
if( isset($matches) )
	include('templates/crossref.html.php');

include('templates/form.html.php');
	
include('templates/footer.html.php');
