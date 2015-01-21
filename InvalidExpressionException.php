<?php
namespace Azera\Component\Queryable;

class InvalidExpressionException extends QueryableException
{

	public function __construct( $expression , $message = 'Invalid "%s" queryable expression' )
	{
		parent::__construct( sprintf( $message , $expression ) ,500);
	}

}
?>