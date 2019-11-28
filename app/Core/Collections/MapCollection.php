<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-05
 * Time: 13:35
 */

namespace OdReviewForm\Core\Collections;


use PhpCollection\Map;

class MapCollection extends Map
{

    /**
     * @return mixed
     */
    public function get($key)
    {

        if( !$this->containsKey( $key ) )

            new \RuntimeException('Item does not exist');

        return $this->elements[ $key ];

    }

    /**
     * @return mixed
     */
    public function first()
    {

        if( empty($this->elements) )

            new \RuntimeException('There are no items');

        return reset($this->elements);

    }

    /**
     * @return mixed
     */
    public function last()
    {

        if (empty($this->elements))

            new \RuntimeException('There are no items');

        return end($this->elements);

    }

    /**
     * @return mixed|null
     */
    public function find($callable)
    {

        foreach ($this->elements as $k => $v)

            if (call_user_func($callable, $k, $v) === true)

                return $v;

        return null;

    }

    public function newCollection( bool $includeElements = false ) : MapCollection
    {

        return $this->createNew( $includeElements ? $this->elements : [] );

    }

    public function sort( $sort = SORT_REGULAR ) : self
    {

        sort( $this->elements, $sort );

        return $this;

    }

    public function usort( callable $sort ) : self
    {

        usort( $this->elements, $sort );

        return $this;

    }

    public function ksort( $sort = SORT_REGULAR ) : self
    {

        ksort( $this->elements, $sort );

        return $this;

    }

}