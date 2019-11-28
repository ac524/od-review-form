<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 2019-03-05
 * Time: 10:46
 */

namespace OdReviewForm\Core\Database\Tables\Rows;

use OdReviewForm\Core\Collections\MapClassCollection;
use OdReviewForm\Core\Database\Database;
use OdReviewForm\Core\Database\Exceptions\InvalidDatabaseConfig;
use OdReviewForm\Core\Database\Exceptions\InvalidQueryException;
use OdReviewForm\Core\Database\Query;
use OdReviewForm\Core\Database\Tables\Table;
use OdReviewForm\Minify\Database\MinifyDatabase;

/**
 * Class Rows
 * @package ComposerPress\Core\Database
 *
 * @method Rows newCollection(bool $includeElements = false)
 * @method Row itemFactory(...$classArgs)
 *
 */
abstract class Rows extends MapClassCollection
{

    /** @var string|MinifyDatabase Name of the Database class instance containing the target table registration. */
    protected $database;

    /**
     * @return Query
     * @throws InvalidQueryException
     */
    public function query() : Query
    {
        try {

            return
                ( new Query( $this->getDatabase() ) )
                    ->setCollection( $this->getQueryCache() ?? $this )
                    ->from( $this->getTableName() );

        } catch( InvalidDatabaseConfig $exception ) {

            die( $exception->getMessage() );

        }
    }

	/**
	 * @return \OdReviewForm\Core\Database\Tables\Table
	 * @throws InvalidDatabaseConfig
	 */
	public function getTable() : Table
	{
		return $this->getDatabase()->getTable( $this->getTableName() );
    }

	/**
	 * @return string
	 */
	protected function getTableName() : string
	{
		try {

			return ( new \ReflectionClass( $this ) )->getShortName();

		} catch ( \Exception $e ) {

			return '';

		}
	}

    /**
     * Override and return a shared instance of the collection for persistent instance caching.
     *
     * @return Rows|null
     */
    protected function getQueryCache() : ?Rows
    {
        return null;
    }

    /**
     * @return Database
     * @throws InvalidDatabaseConfig
     */
    protected function getDatabase() : Database
    {
        if( empty( $this->database ) )

            throw new InvalidDatabaseConfig( 'Instances of Row must define a database' );

        return $this->database::getInstance();
    }

}