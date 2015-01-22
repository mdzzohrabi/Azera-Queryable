# Azera Queryable ![Travis](https://travis-ci.org/mdzzohrabi/Azera-Queryable.svg?branch=master)
---
**Samples :**
```php
// Sum
(new Queryable( [ 20 , 30 , 60 ] ))->Sum(); // Return 110
(new Queryable( [ 20 , 30 , 60 ] ))->Sum('x => x + 10'); // Return 140

// Average
(new Queryable( [ 50 , 100 ] ))->Average(); // Return 75
(new Queryable( [ 50 , 100 ] ))->Average('x => x * 2'); // Return 150

// Max
(new Queryable( range(20,100) ))->Max(); // Return 100

// Min
(new Queryable( range(20,100) ))->Min(); // Return 20

// Any
(new Queryable( [ [ 'Red','Blue' ] , [ 'Green' ] ] ))->Any('colors => in_array( "Red" , colors )'); // Return true
(new Queryable( [ [ 'Red','Blue' ] , [ 'Green' ] ] ))->Any('colors => in_array( "White" , colors )'); // Return false

// Where
(new Queryable( [ 10 , 30 , 50 ] ))->Where('x => x > 10'); // Return Queryable( [ 30 , 50 ] )

$users = array(
	[
		'id' => 10,
		'name' => 'Masoud',
		'rule' => 'Admin'
	],
	[
		'id' => 15,
		'name' => 'Alireza',
		'rule' => 'User'
	]
);

// Select
(new Queryable( $users ))->Select('user => user["name"]'); // Return Queryable( [ 'Masoud' , 'Alireza' ] )

// Last
(new Queryable( $users ))->Select('user => user["name"]')->Last(); // Return "Alireza"

// First
(new Queryable( $users ))->Select('user => user["id"]')->First(); // Return 10

// Cast
(new Queryable( $users ))->Cast('object')->Select('user => user->name')->Last(); // Return "Alireza"

// Contains
(new Queryable( [20 , 30 , 40] ))->Contains(30); // Return true

// Except
(new Queryable( [20 , 30 , 40] ))->Except([20]); // Return Queryable([ 30 , 40 ])

// Distinct
(new Queryable([ 20 , 20 , 40 , 50 ]))->Distinct(); // Return Queryable([ 20 , 40 , 50 ])

// Concat
(new Queryable([ 2 , 3 ]))->Concat([4 , 5]); // Return Queryable([ 2 , 3 , 4 , 5 ])

// Skip
(new Queryable( range( 1 , 100 ) ))->Skip(50)->First(); // Return 51

// Intersect
(new Queryable( range( 1 , 10 ) ))->Intersect( range(5,8) ); // Return Queryable( [1,2,3,4,9,10] )

// ElementAt
(new Queryable( range(1,100) ))->ElementAt(90); // Return 91

// toArray
(new Queryable( range(1,10) ))->toArray(); // Return [ 1,2,3,4,5,6,7,8,9,10]

// insert
(new Queryable)->insert( 80 );

// insertBulk
(new Queryable)->insertBulk( [ 80 , 90 , 100 ] );

// deleteElementAt
(new Queryable)->insertBulk( [ 80 , 90 , 100 ] )->deleteElementAt(1); // Return Queryable( [ 80 , 100 ] )
```

# Class Abstract
```php
namespace Azera\Component;

class Queryable implements Iterator, ArrayAccess
{
	// Methods...
}
```