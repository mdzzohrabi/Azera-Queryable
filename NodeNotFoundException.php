<?php
namespace Azera\Component\Queryable;

class NodeNotFoundException extends QueryableException {

	public function __construct( $node , $part )
	{
		parent::__construct( sprintf( "`%s` not found in %s" , $part , gettype($node) ) , 500);
	}

}
?>