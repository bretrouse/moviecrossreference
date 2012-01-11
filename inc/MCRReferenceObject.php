<?php

require_once('MCRDatabase.php');

class MCRReferenceObject
{
	
	const MOVIE = 1;
	const PERSON = 2;
	
	private $name;
	private $id;
	private $references;
	private $type;
	
	public function __construct( $id, $val, $type, $references ) {
		self::setId( $id );
		self::setName( $val );
		self::setType( $type );
		self::setReferences( $references );
	}
	
	function getShortCode() {
		return MCRDatabase::toShortCode( $this->getId() . $this->getType() );
	}
	
	function setName( $val )
	{
		if( is_string( $val ) && $val != "" ) {
			$this->name = $val;
		} else {
			throw new Exception('Name must be non-empty and of type "string"');
		}
	}
	
	function getName()
	{
		return $this->name;
	}

	function setId( $id )
	{
		//if( is_int( $id ) && $id > 0 ) {
			$this->id = $id;
		//} else {
		//	throw new Exception('Id must be non-negative integer');
		//}
	}

	function getId()
	{
		return $this->id;
	}

	function setType( $val )
	{
		if( !is_int( $val ) ) {
			throw new Exception('Type must evaluate to an integer');
		}
		$this->type = $val;
	}

	function getType()
	{
		return $this->type;
	}
	
	function setReferences( $arr )
	{
		if( is_array( $arr ) ) {
			$this->references = $arr;
		} else {
			throw new Exception('References must be of type "array"');
		}
	}
	
	function getReferences( $key = null )
	{
		if( isset($key) && is_int($key) ) {
			return $this->references[$key];
		}
		return $this->references;
	}
	
	function crossWith( $obj )
	{
		if( is_object( $obj ) && get_class( $obj ) == 'MCRReferenceObject' ) {
			if( $this->type == $obj->getType() ) {
				return array_intersect_key( $this->getReferences(), $obj->getReferences() );
			} else {
				return $this->type == MCRReferenceObject::MOVIE ? $this->references[$obj->getId()] : $obj->getReferences( $this->id );
			}
		} else {
			throw new Exception('Must cross with a MCRReferenceObject');
		}
	}
	
}