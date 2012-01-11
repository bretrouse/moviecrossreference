<?php

require_once('imdbphp2/imdbsearch.class.php');
require_once('imdbphp2/imdb_person.class.php');
require_once('imdbphp2/imdb.class.php');
require_once('MCRDatabase.php');
require_once('MCRReferenceObject.php');

class IMDbDatabase extends MCRDatabase
{
	
	private function compareScore( $a, $b ) {
		if( is_object($a) && is_object($b) ) {
			if ( $a->score == $b->score ) {
			    return 0;
			}
			return ( $a->score < $b->score ) ? -1 : 1;
		}
		return 0;
	}
	
	public function search ( $name )
	{
		$psearch = new imdbpsearch();
		$psearch->setsearchname( $name );
		$psearch->maxresults = 20;
		$people = $psearch->results();
		
		$msearch = new imdbsearch();
		$msearch->setsearchname( $name );
		$msearch->maxresults = 20;
		$movies = $msearch->results();

		foreach( $people as $p ) {
			if( is_object( $p ) ) {
				$p->type = MCRReferenceObject::PERSON;
			 	$p->score = levenshtein( strtolower($name), strtolower($p->name()), 1, 10, 10 ) + 1;
				if( isset($p->pop) )
					$p->score -= 20;
				$i = $p->getSearchDetails();
				$p->name = $p->name() . ( isset($i['moviename']) ? " (" . $i['moviename'] . ")" : "" );
			}
		}
		
		foreach( $movies as $m ) {
			if( is_object( $m ) ) {
				$m->type = MCRReferenceObject::MOVIE;
			 	$m->score = levenshtein( strtolower($name), strtolower($m->title()), 1, 10, 10 ) + 1;
				if( isset($m->pop) )
					$m->score -= 20;
				$m->name = $m->title() . ' (' . $m->year() . ')';
			}
		}
		
		$results = array_merge( $people, $movies );
		usort( $results, array( $this, 'compareScore' ) );
		if( count($results) > 20 )
			$results = array_slice( $results, 0, 20 );

		$formatted_results = array();
		foreach( $results as $r ) {
			if( is_object($r) )
				array_push( $formatted_results, array( 'id' => $r->imdbid(), 'type' => $r->type, 'name' => $r->name ) );
		}
		return $formatted_results;
	}

	public function getReferenceObject( $id, $type )
	{	
		if( $type == MCRReferenceObject::MOVIE ) {
			$obj = new imdb( $id );
			$people = array();
			$cast_crew = array("director","producer","cast");
			foreach( $cast_crew as $c ) {
				foreach( $obj->$c() as $p ) {
					$people[$p['imdb']] = array( $p['name'], $p['role'] );
				}
			}
			return new MCRReferenceObject( $id, $obj->title(), MCRReferenceObject::MOVIE, $people );
		} else {
			$obj = new imdb_person( $id );
			$movies = array();
			$role_types = array("actor","actress","producer","director");
			foreach( $role_types as $r ) {
				$filmography = $obj->{"movies_$r"}();
				//$filmography = $obj->movies_all();
				if( $filmography ) {
					foreach( $filmography as $f ) {
						$movies[$f['mid']] = array( $f['name'], $f['chname'] ? $f['chname'] : "(unknown)" );
					}
				}
			}
			return new MCRReferenceObject( $id, $obj->name(), MCRReferenceObject::PERSON, $movies );
		}
	}
	
}
