<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 3/2/19
 * Time: 3:44 PM
 */

namespace OdReviewForm\Core\Database\Tables;

use OdReviewForm\Core\Collections\CollectionClass;
use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Database\Database;
use OdReviewForm\Core\Database\Tables\Columns\Column;
use OdReviewForm\Core\Database\Tables\Columns\Columns;
use OdReviewForm\Core\Database\Tables\Rows\Row;
use OdReviewForm\Core\Database\Tables\Rows\Rows;
use PhpCollection\Map;

class Table extends CollectionClass
{

    /** @var Database */
    private $db;

    /** @var string */
    private $id;

    /** @var Columns  */
    private $columns;

    public function __construct( Database $db, $id, ?array $columns = null )
    {

	    $this->db = $db;

        $this->id = $id;

        $this->columns = new Columns();

        if( !empty( $columns ) )

            foreach ( $columns as $columnName => $columnConfig )

                $this->addColumn( $columnName, $columnConfig  );

    }

    /**
     * @param Row|Row[]|Map $toInsert
     * @return Table
     */
    public function insert( $toInsert ) : self
    {

        $insertColumns = $this->columns->getInsertColumns();

        if( is_array( $toInsert ) || is_a( $toInsert, Map::class ) ) {

            // TODO Multi Insert Handling

            return $this;

        }

        $result = $this->getDatabase()->wpdb()->insert(
            $this->getName(),
            $this->filterColumnValues( $toInsert->getColumnValues( $insertColumns ) ),
            $this->columns->getInjectionTypes( $insertColumns->keys() )
        );

        if( $result ) {

            $insertId = $this->getDatabase()->wpdb()->insert_id;

            if( !empty( $insertId ) ) {

                $autoIncProperty = $this->columns->getInsertId()->getName();

                $toInsert->updateProperties([
                    $autoIncProperty => $insertId
                ]);

            }

        }

        // TODO Result Handling / Insert Error detection

        return $this;

    }

    /**
     * @param Row $toUpdate
     * @return Table
     */
    public function update( Row $toUpdate ) : self
    {

        $updateColumns = $this->columns->getUpdateColumns();
        $keyColumns = $this->getPrimaryKeys();

        $result = $this->getDatabase()->wpdb()->update(
            $this->getName(),
	        $this->filterColumnValues( $toUpdate->getColumnValues( $updateColumns ) ),
            $toUpdate->getColumnValues( $keyColumns ),
            $this->columns->getInjectionTypes( $updateColumns->keys() ),
            $this->columns->getInjectionTypes( $keyColumns->keys() )
        );

        // TODO Result Handling

        return $this;

    }

	public function delete( Row $toDelete ) : self
	{
		$keyColumns = $this->getPrimaryKeys();

		$this->getDatabase()->wpdb()->delete(
			$this->getName(),
			$toDelete->getColumnValues( $keyColumns ),
			$this->columns->getInjectionTypes( $keyColumns->keys() )
		);

		return $this;
    }

    public function getId() : string
    {

        return $this->id;

    }

    public function getName() : string
    {

        return $this->db->wpdb()->prefix . $this->db->getTablePrefix() . $this->getId();

    }

    public function getColumns() : Columns
    {

        return $this->columns;

    }

    public function addColumn( string $name, array $config )
    {

        $config[ 'name' ] = $name;

        $this->columns->set( $name, new Column( $config ) );

    }

    public function getPrimaryKeys() : Columns
    {

        return $this->columns->filter( function( Column $column ) {

            return 0 === $column->getKey();

        } );

    }

    public function getUniqueKeys() : Columns
    {

        return $this->columns->filter( function( Column $column ) {

            return 1 === $column->getKey();

        } );

    }

    public function getIndexKeys() : Columns
    {

        return $this->columns->filter( function( Column $column ) {

            return 2 === $column->getKey();

        } );

    }

    public function getDatabase() : Database
    {

        return $this->db;

    }

    protected function filterColumnValues( array $values )
    {

        $filteredValues = [];

        foreach ( $values as $key => $value ) {

        	$column = $this->getColumns()->get( $key );

            if( is_a( $value, Rows::class ) ) {

                $filteredValues[ $key ] = implode( ',', $value->keys()  );

                continue;

            }

            if( $this->getColumns()->get( $key )->isJsonEncoded() ) {

	            $filteredValues[ $key ] = json_encode( $value );

            	continue;

            }

            if( is_array( $value ) ) {

                $filteredValues[ $key ] = implode( ',', $value );

                continue;

            }

            $filteredValues[ $key ] = $value;

        }

        return $filteredValues;

    }

}