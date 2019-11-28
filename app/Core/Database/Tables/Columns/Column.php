<?php
/**
 * Created by PhpStorm.
 * User: anthonybrown
 * Date: 3/2/19
 * Time: 4:54 PM
 */

namespace OdReviewForm\Core\Database\Tables\Columns;
use OdReviewForm\Core\Collections\CollectionClass;

/**
 * Class Column
 * @package ComposerPress\Core\Database
 *
 */

class Column extends CollectionClass
{

    private $name;

    private $type;

    private $typeLength;

    private $nullable;

    private $default;

    private $autoInc;

    private $unsigned;

    private $key;

    private $injectionType;

	private $jsonEncode;

	private $visible = true;

    public function __construct( ?array $options = null )
    {

        if( !empty( $options ) )

            $this->updateProperties( $options );

    }

    public function getSchema() : string
    {

        $sql = $this->name .' '. $this->type;

        if( null !== $this->typeLength )

            $sql .= '('. $this->typeLength .')';

        if( $this->unsigned )

            $sql .= ' unsigned';

        if( !$this->nullable )

            $sql .= ' NOT NULL';

        if( $this->autoInc )

            $sql .= ' auto_increment';

        if( null !== $this->default )

            $sql .= " DEFAULT '". $this->default ."'";

        return $sql;

    }

    public function setName( string $name ) : self
    {

        $this->name = $name;

        return $this;

    }

    public function getName() : string
    {

        return $this->name;

    }

    public function setType( string $type ) : self
    {

        $this->type = strtolower( $type );

        return $this->setInjectionType();

    }

    public function setTypeLength( ?int $length ) : self
    {

        $this->typeLength = $length;

        return $this;

    }

    public function setNotNull( bool $isNotNull ) : self
    {

        $this->notNull = $isNotNull;

        return $this;

    }

    public function setAutoInc( bool $isAutoInc ) : self
    {

        $this->autoInc = $isAutoInc;

        return $this;

    }

	public function setNullable( bool $nullable ) : self
	{

		$this->nullable = $nullable;

		return $this;

	}

    /**
     * @return bool
     */
    public function isAutoInc() : bool
    {

        return $this->autoInc ?? false;

    }

    public function setDefault( $value ) : self
    {

        $this->default = $value;

        return $this;

    }

    public function getKey() : ?int
    {

        return $this->key;

    }

    public function isPrimaryKey() : bool
    {

        return $this->key === 0;

    }

    public function isUniqueKey() : bool
    {

        return $this->key === 1;

    }

    public function isIndexKey() : bool
    {
        return $this->key === 2;
    }

    /**
     * @param int $keyType 1 for Primary, 0 for Index
     * @return Column
     */
    public function setKey( int $keyType ) : self
    {
        $this->key = $keyType;

        return $this;
    }

    /**
     * @param bool $isUnsigned
     * @return Column
     */
    public function setUnsigned( bool $isUnsigned ) : self
    {
        $this->unsigned = $isUnsigned;

        return $this;
    }

    public function getInjectionType() : string
    {
        return $this->injectionType;
    }

    protected function setJsonEncode( ?bool $encode ) : self
    {
    	$this->jsonEncode = $encode;

    	return $this;
    }

    public function isJsonEncoded() : bool
    {
    	return $this->jsonEncode ?? false;
    }

    private function setInjectionType() : self
    {
        static $numericTypesList = [
            'bit',
            'tinyint',
            'smallint',
            'bigint',
            'decimal',
            'numeric',
            'float',
            'real'
        ];

        $this->injectionType =

            in_array( $this->type, $numericTypesList )

                ? '%d'

                : '%s';

        return $this;
    }

	public function isVisible() : bool
	{
		return $this->visible;
    }

	/**
	 * @param bool $visible
	 *
	 * @return Column
	 */
	public function setVisible( bool $visible ) : self
	{
		$this->visible = $visible;

		return $this;
	}

}