<?php

require_once('TMDb.php');
require_once('MCRDatabase.php');
require_once('MCRReferenceObject.php');

class TMDbDatabase extends MCRDatabase
{
	
	private $tmdb;
	
	public function __construct( $key )
	{
		$this->tmdb = new TMDb( $key );
	}

	private function compareScore( $a, $b ) {
		if( is_object($a) && is_object($b) ) {
			if ( $a->score == $b->score ) {
			    return 0;
			}
			return ( $a->score > $b->score ) ? -1 : 1;
		}
		return 0;
	}
	
	public function search( $name )
	{
		$people = json_decode( $this->tmdb->searchPerson( $name ) );
		$movies = json_decode( $this->tmdb->searchMovie( $name ) );

		foreach( $people as $p ) {
			if( is_object( $p ) )
				$p->type = MCRReferenceObject::PERSON;
		}
		
		foreach( $movies as $m ) {
			if( is_object( $m ) )
				$m->type = MCRReferenceObject::MOVIE;
		}

		$results = array_merge( $people, $movies );
		usort( $results, array( $this, 'compareScore' ) );
		if( count($results) > 20 )
			$results = array_slice( $results, 0, 20 );

		$formatted_results = array();
		foreach( $results as $r ) {
			if( is_object($r) )
				array_push( $formatted_results, array( 'id' => $r->id, 'type' => $r->type, 'name' => $r->name ) );
		}
		return $formatted_results;
	}
	
	public function getReferenceObject( $id, $type )
	{	
		if( $type == MCRReferenceObject::MOVIE ) {
			$obj = json_decode( $this->tmdb->getMovie( $id ) );
			$tmp = array();
			foreach( $cast as $c ) {
				$tmp[$c->id] = array( $c->name, $c->job == 'Actor' ? $c->character : $c->job );
			}
			return new MCRReferenceObject( $id, $obj[0]->name, MCRReferenceObject::MOVIE, self::createCastReferences( $obj[0]->cast ) );
		} else {
			$obj = json_decode( $this->tmdb->getPerson( $id ) );
			$movies = array();
			foreach( $obj[0]->filmography as $m ) {
				$movies[$m->id] = array( $m->name, $m->job == 'Actor' ? $m->character : $m->job );
			}
			return new MCRReferenceObject( $id, $obj[0]->name, MCRReferenceObject::PERSON, $movies );
		}
	}
	
}
