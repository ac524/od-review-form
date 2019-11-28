<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-05
 * Time: 10:46
 */

namespace OdReviewForm\Core\Database\Tables\Rows;

use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\Core\Database\Database;
use OdReviewForm\Core\Database\Exceptions\InvalidDatabaseConfig;
use OdReviewForm\Core\Database\Query;
use OdReviewForm\Core\Database\Tables\Columns\Column;
use OdReviewForm\Core\Database\Tables\Columns\Columns;
use OdReviewForm\Core\Database\Tables\Table;
use OdReviewForm\Core\Traits\ObjectProperties;

abstract class Row extends CollectionClass
{

    /**
     * @var string|Database Required. Class name of the target Database instance
     * @see Database
     */
    protected $database;

    /** @var string|null */
    protected $tableName;

    /**
     * Row constructor.
     * @param array|null $properties
     * @throws InvalidDatabaseConfig
     */
    public function __construct( ?array $properties = null )
    {

        if( empty( $this->database ) )

            throw new InvalidDatabaseConfig( 'Instances of Row must define a database' );

        if( !empty( $properties ) )

            $this->updateProperties( $properties );

    }

    /**
     * @return Row
     */
    public function save() : self
    {

        $primaryKeyColumns = $this->getTable()->getPrimaryKeys();

        if( $primaryKeyColumns->count() > 1 ) {

            // TODO Multiple Primary Key Handling

            return $this;

        }

        $primaryKeyColumn = $primaryKeyColumns->first();
        $primaryKeyPropertyName = $this->getColumnPropertyName( $primaryKeyColumn );

        if( empty( $this->getPropertyValue( $primaryKeyPropertyName ) ) ) {

            if( !$primaryKeyColumn->isAutoInc() )

                $this->updateProperties([
                    $primaryKeyPropertyName => $this->generateId()
                ]);

            $this->getTable()->insert( $this );

            return $this;

        }

        $this->getTable()->update( $this );

        return $this;

    }

	public function delete(  ) : self
	{
		$this->getTable()->delete( $this );

		return $this;
    }

	public function query()
	{
		try {

			return (new Query( $this->getDatabase() ))->from( $this->getTableName() );

		} catch ( \Exception $e ) {

			return null;

		}
    }

    /**
     * @return string
     */
    public function getTable() : Table
    {

        return $this
            ->getDatabase()
            ->getTable( $this->getTableName() );

    }

	/**
	 * @return string
	 */
	public function getTableName() : string
	{

		try {

			return $this->tableName ?? ( new \ReflectionClass( $this ) )->getShortName() .'s';

		} catch ( \Exception $e ) {

			return '';

		}

	}

    /**
     * @return string
     */
    public function getColumns() : Columns
    {

        return $this
            ->getTable()
            ->getColumns();

    }

    public function getCollectionKey() : string
    {

        return implode(':', $this->getPropertyValues( $this->getTable()->getPrimaryKeys()->keys() ) );

    }

    protected function generateId() : string
    {

        // TODO Add unique string ID generation for non auto inc ids
        return '';

    }

    public function getColumnValues( ?Columns $columns = null ) : array
    {

        $values = [];

        if( null === $columns )

            $columns = $this->getColumns();

        foreach ( $columns->all() as $column )

            $values[ $column->getName() ] = $this->getPropertyValue( $this->getColumnPropertyName( $column ) );

        return $values;

    }

    /**
     * @return Database
     */
    private function getDatabase() : Database
    {

        return $this->database::getInstance();

    }

    /**
     * Convert DB column naming standard to class property standard (underscore snake case to camel)
     *
     * @param Column $column
     * @return string
     */
    private function getColumnPropertyName( Column $column ) : string
    {

        return lcfirst(str_replace( '_', '', ucwords( $column->getName(), '_' )));

    }

}