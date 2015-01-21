<?php
namespace Azera\Component\Queryable;

/**
 * @coversDefaultClass Azera\Component\Queryable
 */
class QueryableTest extends \PHPUnit_Framework_TestCase
{

	function Repository()
	{
		return array(

			array([

				'0' => (object)[
					'name'	=> 'Masoud',
					'age'	=> 21
				],
				'1' => (object)[
					'name'	=> 'Alireza',
					'age'	=> 20
				]

			])

		);
	}

	/**
	 * @dataProvider Repository
	 */
	function testTraversable( array $data )
	{
		$this->assertEquals( 2 , (new Queryable($data))->Count() );
	}

	
	/**
	 * @dataProvider Repository
	 * @covers ::All()
	 */
	function testAll( array $data )
	{
		$this->assertFalse( (new Queryable( $data ))->All('x => x->name == "B"') );
	}

	/**
	 * @dataProvider Repository
	 * @covers ::Count
	 */
	function testCount( array $data )
	{
		$this->assertEquals( 0 , (new Queryable( $data ))->Count('x => x->name == "A" || x->name == "B"') );
	}

	/**
	 * @dataProvider Repository
	 * @covers ::First()
	 */
	function testFirst( array $data )
	{
		$queryable = new Queryable( $data );
		$this->assertEquals( $this->Repository()[0][0]['0'] , $queryable->First() );
	}

	/**
	 * @dataProvider Repository
	 * @covers ::Where()
	 * @depends testFirst
	 */
	function testWhere( array $data )
	{
		$this->assertEquals( 20 , (new Queryable( $data ))->Where('user => user->name == "Alireza"')->First()->age );
	}

	function testQueryable()
	{

		$query = new Queryable( [

				[ 'name' => 'Masoud' , 'id' => 23 ],
				[ 'name' => 'Alireza', 'id'	=> 10 ]

			] );

		//$this->assertEquals( $this->Repository() , $query->toList() );

		$this->assertInternalType( 'object' , $query->Cast('object')->First() );

		$this->assertEquals( 'Masoud' , $query->Cast('object')->Select('u => u->name')->First() );

		$this->assertEquals( 33 , $query->Cast('object')->Sum( 'user => user->id' ) );

		$this->assertEquals( 23 , $query->Cast('object')->First()->id );

		$this->assertCount( 10 , new Queryable([1,2,3,4,5,6,6,7,8,9]) );

		$this->assertCount( 9 , (new Queryable([1,2,3,4,5,6,6,7,8,9]))->Distinct() );

		$this->assertCount( 2 , (new Queryable([1,2,3,4,5,6,6,7,8,9]))->Intersect([6]) );

		$this->assertCount( 100 , new Queryable( range(1,100) ) );

		$this->assertEquals( 75 , (new Queryable( range(1,100) ))->ElementAt(74) );

		$this->assertEquals( 10 , (new Queryable( range(10,100) ))->Min() );

		$this->assertEquals( 100 , (new Queryable( range(10,100) ))->Max() );

		$this->assertEquals( 75 , (new Queryable( [100,50] ))->Average() );

		$this->assertEquals( 90 , (new Queryable( [ 10, 30, 50 ] ))->Sum() );

		$this->assertEquals( 12 , (new Queryable( [ 2 , 2 , 2 ] ))->Select('x => x * x')->Sum() );

		$this->assertEquals( 15 , (new Queryable)->insert(10)->insert(5)->Sum() );

		$this->assertEquals( 25 , (new Queryable)->insertMany([ 10 , 5 ])->insert(10)->Sum() );

		$this->assertEquals( 2 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Count() );

		$this->assertEquals( 100 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Skip(1)->First() );

		$this->assertEquals( 1 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->Skip(2)->Count() );		

		$this->assertEquals( 1 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Skip(1)->Count() );

		$this->assertEquals( 2500 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->Select(function($x){ return $x * $x; })->First() );

	}

}
?>