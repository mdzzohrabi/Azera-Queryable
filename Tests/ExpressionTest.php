<?php
namespace Azera\Component\Queryable;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * InvlidExcepression Exception
	 * @expectedException Azera\Component\Queryable\InvalidExpressionException
	 * @expectedExceptionMessage Invalid variable name "9name" in expression "9name => reza"
	 */
	function testInvalidExpression()
	{
		new Expression('9name => reza');
	}

	/**
	 * Valid Expression
	 */
	function testValidExpression()
	{
		new Expression('name => name > 10');
	}


	function testExpression()
	{

		// Expression 1

		$exp = new Expression('x => x > 10');

		$this->assertTrue( $exp->execute(20) );

		$this->assertFalse( $exp->execute(5) );

		// Expression 2 ( Lambda Expression )

		$exp = new Expression(function( $x ){ return is_string($x); });

		$this->assertFalse( $exp->execute(10) );

		$this->assertTrue( $exp->execute('Hello') );

		// Expression 3

		$exp = new Expression('user => user->name == "Alireza" AND user->id == 12');

		$this->assertTrue( $exp->execute( (object)[ 'name' => 'Alireza' , 'id' => 12 ] ) );

	}

	function testMultiInput()
	{

		$this->assertTrue( (new Expression('a,b => a > b'))->execute( 30 , 20 ) );

	}

}
?>