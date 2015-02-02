<?php
namespace Azera\Component\Queryable;

class BadTypeException extends QueryableException {

	public function __construct( $object , $types )
	{
		parent::__construct( sprintf( "Given object must be `%s` , `%s` given" , implode( ' or ' , $types ) , gettype($object) ) , 500);
	}

}
?>