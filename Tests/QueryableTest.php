<?php
namespace Azera\Component\Queryable;

use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Azera\Component\Queryable
 */
class QueryableTest extends PHPUnit_Framework_TestCase
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
	 * @covers Where()
	 * @depends testFirst
	 */
	function testWhere( array $data )
	{
		$this->assertEquals( 20 , (new Queryable( $data ))->Where('user => user->name == "Alireza"')->First()->age );
	}

	/**
	 * @covers ::Cast()
	 * @covers ::Select()
	 * @covers ::Contains()
	 * @covers ::Sum()
	 * @covers ::First()
	 * @covers ::Last()
	 * @covers ::Distinct()
	 * @covers ::Intersect()
	 * @covers ::ElementAt()
	 * @covers ::Min()
	 * @covers ::Max()
	 * @covers ::Average()
	 * @covers ::insert()
	 * @covers ::insertMany()
	 * @covers ::Any()
	 * @covers ::Concat()
	 * @covers ::deleteElementAt()
	 * @covers ::Skip()
	 * @covers ::Count()
	 * @covers ::Except()
	 */
	function testQueryable()
	{

		$query = new Queryable( [

				[ 'name' => 'Masoud' , 'id' => 23 ],
				[ 'name' => 'Alireza', 'id'	=> 10 ]

			] );

		$this->assertInternalType( 'object' , $query->Cast('object')->First() );

		$this->assertTrue( $query->Cast('object')->Select('user => user->name')->Contains('Masoud') );

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

		$this->assertEquals( 150 , (new Queryable( [100,50] ))->Average('x => x * 2') );

		$this->assertEquals( 90 , (new Queryable( [ 10, 30, 50 ] ))->Sum() );

		$this->assertEquals( 12 , (new Queryable( [ 2 , 2 , 2 ] ))->Select('x => x * x')->Sum() );

		$this->assertEquals( 15 , (new Queryable)->insert(10)->insert(5)->Sum() );

		$this->assertEquals( 25 , (new Queryable)->insertMany([ 10 , 5 ])->insert(10)->Sum() );

		$this->assertEquals( 2 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Count() );

		$this->assertEquals( 100 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Skip(1)->First() );

		$this->assertEquals( 1 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->Skip(2)->Count() );		

		$this->assertEquals( 1 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->deleteElementAt(1)->Skip(1)->Count() );

		$this->assertEquals( 2500 , (new Queryable)->insertMany([ 50 , 90 , 100 ])->Select(function($x){ return $x * $x; })->First() );

		$this->assertTrue( (new Queryable)->insert( [ 'Red' , 'Green' ] )->insert( [ 'Pink' ] )->Any('colors => in_array( "Pink" , colors )') );

		$this->assertTrue( (new Queryable)->insert( [ 'Red' , 'Green' ] )->insert( [ 'Pink' ] )->Any('colors => in_array( "Red" , colors )') );

		$this->assertFalse( (new Queryable)->insert( [ 'Red' , 'Green' ] )->insert( [ 'Pink' ] )->Any('colors => in_array( "White" , colors )') );

		$this->assertTrue( (new Queryable( [ 10 , 20 ] ))->Concat([ 40 , 50 ])->Contains( 40 ) );

		$this->assertCount( 4 , (new Queryable( [ 10 , 20 ] ))->Concat([ 40 , 50 ]) );

		$this->assertCount( 50 , (new Queryable)->insertMany( range( 1 , 100 ) )->Except( range( 51 , 100 ) ) );

	}

	/**
	 * @covers ::Sort()
	 */
	public function testSort()
	{
		$this->assertEquals( 1000 , (new Queryable( range(1,1000) ))->Sort('a,b => a < b')->First() );

		$this->assertEquals( "Alireza" , 
				(new Queryable)
					->insert( [ 'id' => 30 , 'name' => 'Masoud' ] )
					->insert( [ 'id' => 10 , 'name' => 'Alireza' ] )
					->Cast('object')
					->Sort('a,b => a->id > b->id')
					->Select('u => u->name')
					->First()
			);

	}
    
    public function testQuickAccess() {
        
        $users = array(
            
            array (
                'name' => 'Alireza',
                'id'   => 10
            ),
            array(
                'name' => 'Masoud',
                'id'   => 20
            )
            
        );
        
        $users = new Queryable( $users );
        
        $this->assertEquals( 20 , $users->get('1.id') );
        
        $this->assertInternalType( 'array' , $users->get('0') );
        
        $users = $users->Cast('object');
        
        $this->assertEquals( 'Masoud' , $users->get('1.name') );
        
        $this->assertInternalType( 'object' , $users->get('1') );
        
    }

    /**
     * @covers ::indexOf()
     */
    public function testIndexOf() {

        $object_1 = (object)[ 'name' => 'Masoud' ];
        $object_2 = (object)[ 'name' => 'Alireza' ];

        $q = new Queryable( [ $object_2 , $object_1 ] );

        $this->assertEquals( 1 , $q->indexOf( $object_1 ) );
        $this->assertEquals( 0 , $q->indexOf( $object_2 ) );

    }

    /**
     * @covert ::deleteElement()
     */
    public function testDeleteElement() {

        $object_1 = (object)[ 'name' => 'Masoud' ];
        $object_2 = (object)[ 'name' => 'Alireza' ];

        $q = new Queryable( [ $object_2 , $object_1 ] );

        $this->assertCount( 2 ,  $q );
        $this->assertTrue( $q->Contains( $object_2 ) );

        $q->deleteElement( $object_2 );

        $this->assertFalse( $q->Contains( $object_2 ) );
        $this->assertCount( 1 , $q );


    }

    /**
     * @covers ::SelectKeyValue
     */
    public function testSelectKeyValue() {

        $users = array(

            array (
                'name' => 'Alireza',
                'id'   => 10
            ),
            array(
                'name' => 'Masoud',
                'id'   => 20
            )

        );

        $users = new Queryable( $users );

        $this->assertEquals(
            [
                10  => 'Alireza',
                20  => 'Masoud'
            ],
            $users->Cast('object')->SelectKeyValue('u => u->id','u => u->name')->toArray()
        );

    }
    
    /**
     * @dataProvider Repository
     * @expectedException Azera\Component\Queryable\NodeNotFoundException
     */
    public function testQuickAccessNodeNotFoundException( array $users ) {
        
        $users = new Queryable( $users );
        
        $users->getWithException( '3.age' );
        
    }

}
?>