<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 3/3/19
 * Time: 1:03 PM
 */

namespace OdReviewForm\Core\Database;

use OdReviewForm\Core\Database\Exceptions\InvalidQueryException;
use OdReviewForm\Core\Database\Tables\Rows\Row;
use OdReviewForm\Core\Database\Tables\Rows\Rows;
use OdReviewForm\Core\Database\Tables\Table;
use OdReviewForm\Core\Exceptions\InvalidCollectionConfiguation;

class Query
{

    /** @var Database */
    private $database;

    /** @var string */
    private $from;

    /** @var array  */
    private $select;

    /** @var array  */
    private $where = [];

	/** @var array  */
	private $whereOperators = [];

    /** @var array|null  */
    private $order;

    /** @var string|null */
    private $group;

    /** @var array|null  */
    private $limit;

    /** @var string|null  */
    private $returnType;

    /** @var Rows|null  */
    private $collection;

    /**
     * Query constructor.
     * @param Database $database
     */
    public function __construct( Database $database )
    {

        $this->database = $database;

    }

    /**
     * @param array|null $where
     * @return Rows|array|null
     */
    public function get( ?array $where = null )
    {
		if( null !== $where )

            $this->where( $where );

        if( $this->canInternalSearch() ) {

            $preSearch = $this->getCollectionResults();

            if( !$preSearch->isEmpty() && !empty( $this->where ) ) {

                $origialWhere = $this->where;

                $this->filterWhereByCollection( $preSearch );

                if( empty( $this->where ) ) {

                    $this->where = $origialWhere;

                    return $this->sortCollection( $preSearch );

                }
                
            }

        }

        $results = $this->filterResults(
            $this->database->wpdb()->get_results( new QuerySql( $this ), ARRAY_A ),
            $preSearch ?? null
        );

        if( !empty( $origialWhere ) )

            $this->where = $origialWhere;

        return $results;

    }

	public function getVar( $column, ?array $where = null )
	{
		$this->select( [ $column ] );

		$this->where( $where ?? [] );

		$result = $this->database->wpdb()->get_var( new QuerySql( $this ) );

		$this->selectDefault();

		return $result;
    }

    public function getCount( ?array $where = null ) : int
    {
    	if( null !== $where )

	        $this->where( $where );

    	$select = $this->getConfig('select');

    	$this->select( [ 'COUNT(*)' ] );

    	$count = (int)$this->database->wpdb()->get_var( new QuerySql( $this ) );

    	$this->select( $select );

    	return $count;
    }

    public function getConfig( string $optionName )
    {

	    return $this->{ $optionName };

    }

    public function getDatabase() : Database
    {

        return $this->database;

    }

    public function truncate() : void
    {

    	if( $this->hasCollection() )

    		$this->collection->clear();

    	$this->database->wpdb()->query( 'TRUNCATE TABLE '. $this->getFromTable()->getName() );

    }

    /**
     * @param string $from
     * @return Query
     * @throws InvalidQueryException
     */
    public function from( string $from ) : self
    {

        if( !$this->database->hasTable( $from ) )

            throw new InvalidQueryException( 'Cannot query unknown tables.' );

        $this->from = $from;

        if( empty( $this->select ) )

        	$this->selectDefault();

        return $this;

    }

    /**
     * @return Table
     */
    public function getFromTable() : Table
    {

        return $this->getDatabase()->getTable( $this->from );

    }

    public function select( array $select ) : self
    {

        $this->select = $select;

        return $this;

    }

	public function selectDefault() : self
	{
		$this->select = [];

		foreach ( $this->getFromTable()->getColumns()->all() as $column )

			if( $column->isVisible() )

				$this->select[] = $column->getName();

		return $this;
    }

    public function where( array $where ) : self
    {

        $this->where = $where;

        return $this;

    }

	public function whereOperators( array $whereOperators ) : self
	{

		$this->whereOperators = $whereOperators;

		return $this;

	}

    public function order( string $column, string $type = 'ASC' ) : self
    {

        $this->order = [ $column, $type ];

        return $this;

    }

    public function limit( int $limit, int $offset = 0 ) : self
    {

        $this->limit = [ $offset, $limit ];

        return $this;

    }

    public function setReturnType( ?string $returnType ) : self
    {

        $this->returnType = $returnType;

        return $this;

    }

    public function setCollection( Rows $collection ) : self
    {

        $this->collection = $collection;

        return $this;

    }

    private function filterWhereByCollection( Rows $collection ) : self
    {

        /** @var Row $result */
        foreach ( $collection->all() as $result ) {

            foreach ( $this->where as $columnName => $value ) {

                $propertyName = $this->getPropertyNameByColumnName( $columnName );

                if( is_array( $value ) ) {

                    $itemIndex = array_search( $result->getPropertyValue( $propertyName ), $value );

                    if( false !== $itemIndex )

                        array_splice( $this->where[ $columnName ], $itemIndex, 1 );

                    if( empty( $this->where[ $columnName ] ) )

                        unset( $this->where[ $columnName ] );

                    continue;

                }

                if( $value === $result->getPropertyValue( $propertyName ) )

                    unset( $this->where[ $columnName ] );

            }

        }

        return $this;

    }

    private function getCollectionResults() : Rows
    {

    	if( empty( $this->where ) )

    		return $this->collection->newCollection( true );

        $results = $this->collection->newCollection();

        foreach ( $this->where as $columnName => $value )
        {

            $propertyName = $this->getPropertyNameByColumnName( $columnName );

            if( is_array( $value ) ) {

                foreach ( $value as $singleValue ) {

                    /** @var Row $result */
                    $result = $this->collection->findBy( $propertyName, $singleValue );

                    if( !empty( $result ) )

                        $results->set( $result->getCollectionKey(), $result );

                }


                continue;

            }

            $result = $this->collection->findBy( $propertyName, $value );

            if( !empty( $result ) )

                $results->set( $result->getCollectionKey(), $result );

        }

        return $results;

    }

    private function canInternalSearch()
    {

        if( !$this->hasCollection() )

            return false;

        // Only internal search on single property queries.
        if( count( $this->where ) !== 1 )

            return false;

        return true;

    }

    private function hasCollection()
    {
        return null !== $this->collection;
    }

    private function filterResults( ?array $results, ?Rows $cachedResults = null )
    {

        if( null === $results && empty( $cachedResults ) )

            return $results;

        // Add any shared filtering between the two return types here.

        if( $this->hasCollection() )

            return $this->createCollectionReturn( $results, $cachedResults );

        return $this->createListReturn( $results );

    }

    private function createListReturn( array $results ) : array
    {

        return $results;

    }

    private function createCollectionReturn( array $results, ?Rows $cachedResults = null ) : Rows
    {

        $mergeCachedResults = null !== $cachedResults && !$cachedResults->isEmpty();

        $collectionReturn = $mergeCachedResults ? $cachedResults : $this->collection->newCollection();

        try {

            foreach ( $results as $result ) {

                $item = $collectionReturn->itemFactory( $this->getCollectionValues( $result ) );

                $collectionKey = $item->getCollectionKey();

                // Fold in new results, but preserve already loaded objects.
                if( !$this->collection->containsKey( $collectionKey ) )

                    $this->collection->set( $collectionKey, $item );

                $collectionReturn->set( $collectionKey, $this->collection->get( $collectionKey ) );

            }

        } catch ( InvalidCollectionConfiguation $exception ) {

            // TODO Error logging
            die( $exception->getMessage() );

        }

        if( $mergeCachedResults )

            $this->sortCollection( $collectionReturn );

        return $collectionReturn;

    }

    /**
     * Converts array keys from table column format to object property format.
     *
     * @param array $tableValues
     * @return array
     */
    private function getCollectionValues( array $tableValues ) : array
    {

        return array_combine( array_map( [ $this, 'getPropertyNameByColumnName' ], array_keys( $tableValues ) ), array_values( $tableValues ) );

    }

    private function getPropertyNameByColumnName( string $columnName ) : string
    {

        return  lcfirst(str_replace( '_', '', ucwords( $columnName, '_' )));

    }

    /**
     * @param Rows $collection
     * @return Rows
     */
    private function sortCollection( Rows $collection ) : Rows
    {

        if( empty( $this->order ) )

            return $collection;

        $collection->usort( function( Row $resultOne, Row $resultTwo ) {

            $compare = substr_compare( $resultOne->getPropertyValue( $this->order[0] ), $resultTwo->getPropertyValue( $this->order[0] ), 0 );

            if( 'DESC' === $this->order[1] )

                return -$compare;

            return $compare;

        } );

        return $collection;

    }

}