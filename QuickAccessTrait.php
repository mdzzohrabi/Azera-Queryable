<?php
namespace Azera\Component\Queryable;

trait QuickAccessTrait {
    
    function get( $route , $default = false ) {
        
        $route = explode('.', $route);
        
        $value = $this->repository;
        
        foreach ( $route as $part )
            if ( is_object( $value ) )
                if ( property_exists( $value , $part ) )
                    $value = $value->{$part};
                else
                    return $default;
            elseif ( is_array( $value ) )
                if ( isset( $value[ $part ] ) )
                    $value = $value[ $part ];
                else
                    return $default;
            else
                return $default;
        
        return $value;
        
    }
    
    function getWithException( $route ) {
        
        $route = explode('.', $route);
        
        $value = $this->repository;
        
        foreach ( $route as $part )
            if ( is_object( $value ) )
                if ( property_exists( $value , $part ) )
                    $value = $value->{$part};
                else
                    throw new NodeNotFoundException( $value , $part );
            elseif ( is_array( $value ) )
                if ( isset( $value[ $part ] ) )
                    $value = $value[ $part ];
                else
                    throw new NodeNotFoundException( $value , $part );
            else
                throw new BadTypeException( $value , [ 'array' , 'object' ] );
        
        return $value;
        
    }
    
}
?>