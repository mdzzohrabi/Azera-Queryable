<?php
namespace Azera\Component\Queryable;

use Closure;
use Iterator,
	Traversable,
	ArrayAccess;
/**
 * Queryable component like as Microsoft System.Linq.Queryable
 * 
 * @author Masoud Zohrabi <mdzzohrabi@gmail.com>
 * @version 1.1
 */
class Queryable implements Iterator, ArrayAccess
{
	
    use QuickAccessTrait;
    
	/**
	 * Repository
	 * @var array
	 */
	protected $repository;

	public function __construct( array $data = array() )
	{
		$this->repository = $data;
	}

    /**
     * Set repository reference
     *
     * @param mixed &$reference Reference property
     */
    protected function setReference( &$reference ) {
        $this->repository = $reference;
    }

	//public function __clone()
	//{
	//	$this->repository = array();
	//}

	/**
	 * Set repository items
	 *
	 * @param  array $data Items
	 * @return $this Queryable
	 */
	public function setRepository( array $data )
	{
		$this->repository = $data;
		return $this;
	}

    /**
     * Get repository items
     *
     * @return array
     */
	public function &getRepository()
	{
		return $this->repository;
	}

	/**
	 * Retreive all items with or without condition
	 * 
	 * @param string|Closure $Func 
	 * @return bool
	 */
	public function All( $Func )
	{

		$result = true;

		$Func = new Expression( $Func );

		foreach ( $this->repository as $item )
			$result &= $Func->execute( $item );

		return (boolean)$result;

	}

	/**
	 * Filters a sequence of values based on a predicate.
	 * 
	 * @param string|Closure $Func
	 * @return Queryable
	 */
	public function Where( $Func )
	{

		if ( $Func != null ){

			$repo = array();

			$Func = new Expression( $Func );

			foreach ( $this->repository as $item )
				if ( $Func->execute( $item ) )
					$repo[] = $item;

		} else {

			$repo = $this->repository;

		}

		return $this->getClone()->setRepository( $repo );

	}

	/**
	 * Determines whether a sequence contains any elements.
     *
	 * @param string|Closure $Func
	 * @return bool
	 */
	public function Any( $Func )
	{

		$result = false;

		$Func = new Expression( $Func );

		foreach ( $this->repository as $item ) $result |= $Func->execute( $item );

		return (boolean)$result;

	}

	/**
	 * Count repository with or without condition
     *
	 * @param string|Closure $Func
	 * @return int
	 */
	public function Count( $Func = null )
	{

		return $Func ? $this->Where( $Func )->Count() : count( $this->repository );

	}

	/**
	 * Get Average of sequence
     *
	 * @param string|Closure $Func
	 * @return double
	 */
	public function Average( $Func = null )
	{

		$items  = $this->Select( $Func );

		$count  = $items->Count();

		$sum    = 0;

		foreach ( $items as $item ) $sum += $item;

		return $sum / $count;

	}

	/**
	 * Get sum of sequence
     *
	 * @param string|Closure $Func
	 * @return double
	 */
	public function Sum( $Func = null )
	{
		$items = $this->Select( $Func );
		$sum   = 0;

		foreach ( $items as $item ) $sum += $item;

		return $sum;
	}

	/**
	 * Determines whether a sequence contains a specified element by using the default equality comparer.
     *
	 * @param mixed $value Value
	 * @return boolean
	 */
	public function Contains( $value )
	{
		return in_array( $value , $this->repository );
	}

	/**
	 * Concatenates two sequences.
     *
	 * @param array|Queryable|Traversable $source 
	 * @return Queryable
	 */
	public function Concat( $source )
	{

		$source = is_array($source) ? $source : ( $source instanceof Queryable ? $source->toList() : iterator_to_array($source) );

		return $this
					->getClone()
					->setRepository( array_merge( $this->repository , $source ) );

	}

	/**
	 * Returns distinct elements from a sequence by using the default equality comparer to compare values
     *
	 * @return Queryable
	 */
	public function Distinct()
	{

		return $this
					->getClone()
					->setRepository( array_unique($this->repository) );

	}

	/**
	 * Produces the set difference of two sequences by using the default equality comparer to compare values.
     *
	 * @param array|Queryable $items Items
	 * @return Queryable
	 */
	public function Except( $items )
	{

		return $this
					->getClone()
					->setRepository( array_diff( $this->repository , $items instanceof Queryable ? $items->toArray() : $items ) );

	}

    /**
     * Get first element of sequence with or without condition
     *
     * @param  string|Closure $Func Condition
     * @param null $default
     * @return mixed
     * @internal param mixed $defaul Default
     */
	public function First( $Func = null , $default = null )
	{
		$bag = $Func ? $this->Select($Func) : $this->repository;
		return current($bag) ?: $default;
	}

	/**
	 * Get first element of sequence or return default
     *
	 * @param  mixed $default Default
	 * @return mixed
	 */
	public function FirstOrDefault( $default )
	{
		return $this->First( null , $default );
	}

	/**
	 * Get last element of sequence with or without condition
     *
	 * @param string|Closure $Func Condition
	 * @return mixed
	 */
	public function Last( $Func = null , $default = null )
	{
		return end($this->Select($Func)) ?: $default;
	}

    /**
     * Get maximum value
     *
     * @param null|Closure $Func Selector
     * @return mixed
     */
	public function Max( $Func = null )
	{
		return max( $this->Select( $Func )->toArray() );
	}

    /**
     * Get minimum value
     *
     * @param null|Closure $Func Selector
     * @return mixed
     */
	public function Min( $Func = null )
	{
		return min( $this->Select( $Func )->toArray() );
	}

    /**
     * Select
     *
     * @param null|Closure $Func Selector
     * @return $this|Queryable
     */
	public function Select( $Func = null )
	{

		if ( $Func == null ) return $this;

		$Func = new Expression( $Func );

		$obj = $this->getClone();

        foreach ( $obj->repository as &$item )
            $item = $Func->execute( $item );

		return $obj;

	}

    /**
     * Select key value
     *
     * @param Closure $Key
     * @param Closure $Value
     * @return Queryable
     */
    public function SelectKeyValue( $Key , $Value ) {

        $Key = new Expression( $Key );
        $Value = new Expression( $Value );

        $items = array();

        foreach ( $this->repository as $item ) {
            $_k = $Key->execute( $item );
            $_v = $Value->execute( $item );
            $items[$_k] = $_v;
        }

        return new Queryable( $items );

    }

    /**
     * Skip n items
     *
     * @param int $skip
     * @return Queryable
     */
	public function Skip( $skip )
	{

		return $this
			->getClone()
			->setRepository( array_slice( $this->repository , $skip ) );

	}


	/**
	 * Get Intersect of two sequence
     *
	 * @param array|Queryable $items 
	 * @return Queryable
	 */
	public function Intersect( $items )
	{

		return $this
					->getClone()
					->setRepository( array_intersect( $this->repository , $items instanceof Queryable ? $items->toArray() : $items ) );

	}

	/**
	 * Casts the elements to the specified type.
     *
	 * @param string $type Type
	 * @return Queryable
	 */
	public function Cast( $type )
	{

		$obj = $this->getClone();

		foreach ($obj->repository as &$value) {
			settype($value, $type);
		}

		return $obj;

	}

    /**
     * Index of element in repository
     *
     * @param $element
     * @return mixed
     */
    public function indexOf( $element ) {
        return array_search( $element , $this->repository );
    }

	/**
	 * Returns the element at a specified index in a sequence.
     *
	 * @param int|string $key 
	 * @return mixed
	 */
	public function ElementAt( $key )
	{
		return $this->repository[ $key ];
	}

	/**
	 * Returns the element at a specified index in a sequence or a default value if the index is out of range
     *
	 * @param int|string $key 
	 * @param mixed $default 
	 * @return mixed
	 */
	public function ElementAtOrDefault( $key , $default )
	{
		return $this->repository[ $key ] ?: $default;
	}

	/**
	 * Retrieve items as array
     *
	 * @return array
	 */
	public function toArray()
	{
		return $this->repository;
	}

	/**
	 * Retrieve items as array
     *
	 * @return array
	 */
	public function toList()
	{
		return $this->repository;
	}

	public function getClone()
	{
		return clone $this;
	}

	// Iterator Interface Implements
	public function current()
	{
		return current( $this->repository );
	}

	public function next()
	{
		return next( $this->repository );
	}

	public function rewind()
	{
		return reset( $this->repository );
	}

	public function key()
	{
		return key( $this->repository );
	}

	public function valid()
	{
		return key( $this->repository ) !== null;
	}

	// ArrayAccess Interface Implements
	public function &offsetGet( $offset )
	{
		return $this->repository[ $offset ];
	}

	public function offsetSet( $offset , $value )
	{
		$this->repository[$offset] = $value;
	}

	public function offsetExists( $offset )
	{
		return isset( $this->repository[ $offset ] );
	}

	public function offsetUnset( $offset )
	{
		unset( $this->repository[$offset] );
	}

    /**
     * Insert item to sequence
     *
     * @param mixed $value Value
     * @return $this
     */
	public function insert( $value )
	{
		$this->repository[] = $value;
		return $this;
	}

    /**
     * Insert many items to sequence
     *
     * @param array $values Values
     * @return $this
     */
	public function insertMany( $values )
	{
		$this->repository = array_merge( $this->repository , $values );
		return $this;
	}

    /**
     * Delete key from sequence
     *
     * @param mixed $offset Key name
     * @return $this
     */
	public function deleteElementAt( $offset )
	{
		unset( $this->repository[ $offset ] );
		return $this;
	}

    /**
     * Delete element
     *
     * @param $element
     * @return $this
     */
    public function deleteElement( $element ) {
        $this->deleteElementAt( array_search( $element , $this->repository ) );
        return $this;
    }

	/**
	 * Sort sequence by given comparator
     *
	 * @param Expression $Func Comparator
	 * @return Queryable
	 */
	public function Sort( $Func = null )
	{
		$sorted = $this->repository;

		$Func = new Expression( $Func );

		usort( $sorted , [ $Func , 'execute' ] );

		return $this
					->getClone()
					->setRepository( $sorted );
	}


}
?>
