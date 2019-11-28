<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-04
 * Time: 13:52
 */

namespace OdReviewForm\Core\Database;


use OdReviewForm\Core\Database\Tables\Columns\Column;
use OdReviewForm\Core\Database\Tables\Table;

class DatabaseTableSchema
{

    /** @var Table */
    private $table;

    public function __construct( Table $table )
    {

        $this->table = $table;

    }

    public function get() : string
    {

        return 'CREATE TABLE '. $this->table->getName() ." (\n"

            . "\t". implode( ",\n\t", $this->getModifiers() ). "\n"

        . ') '. $this->table->getDatabase()->wpdb()->get_charset_collate() .';';

    }

    public function getModifiers() : array
    {

        $modifiers = array_map( function( Column $column ) {

            return $column->getSchema();

        }, $this->table->getColumns()->all() );

        $primaryKeys = $this->table->getPrimaryKeys();

        if( !$primaryKeys->isEmpty() )

            $modifiers[] = 'PRIMARY KEY ('. implode( ',', $primaryKeys->keys() ) .')';

        $uniqueKeys = $this->table->getUniqueKeys();

        if( !$uniqueKeys->isEmpty() )

            $modifiers[] = 'UNIQUE KEY ('. implode( ',', $uniqueKeys->keys() ) .')';

        $indexKeys = $this->table->getIndexKeys();

        if( !$indexKeys->isEmpty() )

            $modifiers[] = 'KEY ('. implode( ',', $indexKeys->keys() ) .')';

        return $modifiers;

    }

}