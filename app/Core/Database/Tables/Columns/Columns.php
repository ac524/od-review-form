<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 3/2/19
 * Time: 4:54 PM
 */

namespace OdReviewForm\Core\Database\Tables\Columns;


use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Collections\MapCollection;
use OdReviewForm\Core\Database\Tables\Columns\Column;

/**
 * Class Columns
 * @package ComposerPress\Core\Database
 *
 * @method Column get($key)
 * @method Column first()
 * @method Column last()
 * @method Column[] all()
 * @method Columns filter($callable)
 * @method Columns filterNot($callable)
 *
 */
class Columns extends MapClassCollection
{

    public function getUpdateColumns() : Columns
    {
        return $this->filter( function( Column $column ) {

            return !$column->isPrimaryKey() && $column->isVisible();

        });
    }

    public function getInsertColumns() : Columns
    {
        return $this->filter( function( Column $column ) {

            return !$column->isAutoInc();

        });
    }

    public function getInsertId() : Column
    {

        return $this->filter( function( Column $column ) {

            return $column->isPrimaryKey() && $column->isAutoInc();

        })->first();

    }

    public function getInjectionTypes(array $columnNames ) : array
    {

        $types = [];

        foreach ( $columnNames as $name )

            $types[ $name ] = $this->get( $name )->getInjectionType();

        return $types;

    }

}