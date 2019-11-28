<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 3/2/19
 * Time: 3:25 PM
 */

namespace OdReviewForm\Core\Database;

use OdReviewForm\Core\Database\Tables\Table;
use OdReviewForm\Core\Database\Tables\Tables;

/**
 * Class Database
 * @package ComposerPress\Core\Database
 *
 */
abstract class Database
{

    /** @var string */
    protected $version = '1.0';

    /** @var array */
    protected $tableConfig;

    /** @var null|string  */
    protected $tablePrefix;

    /** @var Tables  */
    private $tables;

    abstract public static function getInstance() : self;

    /**
     * Database constructor.
     */
    protected function __construct()
    {

        $this->tables = new Tables();

        $this
            ->loadTableConfig()
            ->versionCheck();

    }

    /**
     * @param string $tableId
     * @param array $columns
     * @return Database
     */
    public function createTable(string $tableId, array $columns ) : self
    {

        $this->addTable( new Table( $this, $this->sanitizeTableId( $tableId ), $columns ) );

        return $this;

    }

    /**
     * @param $tableId
     * @return bool
     */
    public function hasTable( $tableId ) : bool
    {

        return $this->tables->containsKey( $this->sanitizeTableId( $tableId ) );

    }

    /**
     * @param Table $table
     * @return Database
     */
    public function addTable( Table $table ) : self
    {

        $this->tables->set( $table->getId(), $table );

        return $this;

    }

    /**
     * @param string $tableId
     * @return Table
     */
    public function getTable( string $tableId ) : Table
    {

        return $this->tables->get( $this->sanitizeTableId( $tableId ) );

    }

    /**
     * @return string|null
     */
    public function getTablePrefix() : ?string
    {

        return $this->tablePrefix ? $this->tablePrefix. '_' : null;

    }

    /**
     * @param string $id
     * @return string
     */
    public function sanitizeTableId( string $id ) : string
    {

        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $id)), '_');

    }

    /**
     * @return \wpdb
     */
    public function wpdb() : \wpdb
    {

        return $GLOBALS['wpdb'];

    }

    /**
     * @return string
     */
    public function getVersionOptionName() : string
    {

        try {

            return strtolower( (new \ReflectionClass($this))->getShortName()  ) . '_ver';

        } catch(  \Exception $e ) {

            return '';

        }

    }

    /**
     * @return string
     */
    public function getPreviousVersion() : string
    {

        return get_option( $this->getVersionOptionName(), '0' );

    }

    /**
     * @param string $version
     * @return Database
     */
    public function setPreviousVersion( string $version ) : self
    {

        update_option( $this->getVersionOptionName(), $version );

        return $this;

    }

    /**
     * @return Database
     */
    private function loadTableConfig() : self
    {

        if( empty( $this->tableConfig ) )

            return $this;

        foreach ( $this->tableConfig as $tableName => $tableColumns )

            $this->createTable( $tableName, $tableColumns );

        return $this;

    }

    /**
     * @return Database
     */
    private function versionCheck() : self
    {

        if( $this->getPreviousVersion() === $this->version )

            return $this;

        $this->setPreviousVersion( $this->version );

        $this->runDbDelta();

        return $this;

    }

    /**
     * @return Database
     */
    private function runDbDelta() : self
    {

        if( !function_exists( 'dbDelta' ) )

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        \dbDelta( $this->getSchema() );

        return $this;

    }

    /**
     * @return string
     */
    private function getSchema() : string
    {

        return implode( "\n", array_map( function( Table $table ) {

            return ( new DatabaseTableSchema( $table ) )->get();

        }, $this->tables->all() ) );

    }

}