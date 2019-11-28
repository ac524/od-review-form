<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-04
 * Time: 17:07
 */

namespace OdReviewForm\Core\Database;


use OdReviewForm\Core\Collections\MapCollection;

class QuerySql
{

    /** @var Query $query */
    private $query;

    public function __construct( Query $query )
    {

        $this->query = $query;

    }


    public function __toString()
    {

        return $this->get();

    }

    public function get() : string
    {

        return $this->getSelect()

            . $this->getFrom()

            . $this->getJoin()

            . $this->getWhere()

            . $this->getGroupBy()

            . $this->getOrderBy()

            . $this->getLimit() .';';

    }

    private function getSelect() : string
    {

        return 'SELECT '. implode( ',', $this->query->getConfig( 'select' ) );

    }

    private function getFrom() : string
    {

        return "\n".'FROM '. $this->query->getFromTable()->getName();

    }

    private function getJoin() : ?string
    {

        // TODO Build Join logic
        return null;

    }

    /**
     * Compile WHERE configuration into SQL
     *
     * @return string|null
     */
    private function getWhere() : ?string
    {

        $where = $this->query->getConfig( 'where' );

        if( empty( $where ) )

            return null;

        $columns = $this->query->getFromTable()->getColumns();

        $conditions = [];
        $conditionValues = [];

        foreach( $where as $columnName => $value ) {

            if( !$columns->containsKey( $columnName ) )

                continue;

            $column = $columns->get( $columnName );

            $condition = $columnName;

            if( is_array( $value ) ) {

                $condition .= ' IN ('. implode( ',', array_fill( 0, count( $value ), $column->getInjectionType() ) ) .')';

                foreach ( $value as $arrayValue )

                    $conditionValues[] = $arrayValue;

            } else {

                $condition .= ' '. $this->getColumnOperator( $columnName ) .' '. $column->getInjectionType();

                $conditionValues[] = $value;

            }

            $conditions[] = $condition;

        }

        return $this->query->getDatabase()->wpdb()->prepare( "\n".'WHERE '. implode( ' AND ', $conditions ), $conditionValues );

    }

    private function getColumnOperator( string $column )
    {
	    $operators = $this->query->getConfig( 'whereOperators' );


	    if( empty( $operators ) || empty( $operators[ $column ] ) )

	    	return '=';

	    return $operators[ $column ];
    }

    private function getGroupBy() : ?string
    {

        $group = $this->query->getConfig( 'group' );

        if( empty( $group ) )

            return null;

        return "\n".'GROUP BY '.$group;

    }

    private function getOrderBy() : ?string
    {

        $order = $this->query->getConfig( 'order' );

        if( empty( $order ) )

            return null;

        return "\n".'ORDER BY '. $order[0] . ' '. $order[1];

    }

    private function getLimit() : ?string
    {

        $limit = $this->query->getConfig( 'limit' );

        if( empty( $limit ) )

            return null;

        return "\n".'LIMIT '. $limit[0] . ', '. $limit[1];

    }

}