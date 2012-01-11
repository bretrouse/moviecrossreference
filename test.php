<?php

require('inc/init.php');

$people = json_decode( $tmdb->searchPerson($_GET['q']) );
$movies = json_decode( $tmdb->searchMovie($_GET['q']) );

function setMovieType ($tmdb_result)
{
	$tmdb_result->type = TMDbObjectType::MOVIE;
	return $tmdb_result;
};
function setPersonType($tmdb_result)
{
	$tmdb_result->type = TMDbObjectType::PERSON;
	return $tmdb_result;
};
function compareScore($a, $b)
{
	if ($a->score == $b->score) {
	    return 0;
	}
	return ($a->score > $b->score) ? -1 : 1;
}
function printTMDb($t)
{
	echo $t->type . '-' . $t->name . '-' . $t->id . '<br>';
}
function createCastReferences( $cast )
{
	$tmp = array();
	foreach( $cast as $c ) {
		$tmp[$c->id] = array( $c->name, $c->job == 'Actor' ? $c->character : $c->job );
	}
	return $tmp;
}
function createMovieReferences( $cast )
{
	$tmp = array();
	foreach( $cast as $c ) {
		$tmp[$c->id] = array( $c->name, $c->job == 'Actor' ? $c->character : $c->job );
	}
	return $tmp;
}

$people = array_map('setPersonType', $people);
$movies = array_map('setMovieType', $movies);
$results = array_merge($people,$movies);
usort($results, 'compareScore');
$results = array_slice($results,0,20);
array_map('printTMDb',$results);

$movie1 = json_decode($tmdb->getMovie(562));
$movie2 = json_decode($tmdb->getMovie(1573));
$person1 = json_decode($tmdb->getPerson(62));
$person2 = json_decode($tmdb->getPerson(4566));

$m1 = new TMDbReferenceObject( $movie1[0]->id, $movie1[0]->name, TMDbObjectType::MOVIE, createCastReferences( $movie1[0]->cast ) );
$m2 = new TMDbReferenceObject( $movie2[0]->id, $movie2[0]->name, TMDbObjectType::MOVIE, createCastReferences( $movie2[0]->cast ) );

$matches = array_keys( $m1->crossWith( $m2 ) );
$m1ref = $m1->getReferences();
$m2ref = $m2->getReferences();

echo "<b>Die Hard x Die Hard 2</b><br>";
foreach( $matches as $m ) {
	echo $m1ref[$m][1] . ' - ' . $m1ref[$m][0] . ' - ' . $m2ref[$m][1] . '<br>';
}

$p1 = new TMDbReferenceObject( $person1[0]->id, $person1[0]->name, TMDbObjectType::PERSON, createMovieReferences( $person1[0]->filmography ) );
$p2 = new TMDbReferenceObject( $person2[0]->id, $person2[0]->name, TMDbObjectType::PERSON, createMovieReferences( $person2[0]->filmography ) );

$matches = array_keys( $p1->crossWith( $p2 ) );
$p1ref = $p1->getReferences();
$p2ref = $p2->getReferences();

echo "<br><b>Bruce Willis x Alan Rickman</b><br>";
foreach( $matches as $m ) {
	echo $p1ref[$m][1] . ' - ' . $p1ref[$m][0] . ' - ' . $p2ref[$m][1] . '<br>';
}

echo "<br><b>Die Hard x Bruce Willis</b><br>";
if( $match = $m1->crossWith($p1) ) {
	echo $p1->getName() . ' - ' . $match[1] . ' - ' . $m1->getName() . '<br>';
}


