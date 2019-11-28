<?php


namespace OdReviewForm\Core\Views\Inputs;


use OdReviewForm\Core\Exceptions\ViewException;

/**
 * Class Inputs
 * @package ComposerPress\Core\Views\Inputs
 */
class Inputs
{

    protected static $instance;

    private $types;

    private $styleNamespace;

    public static function instance() : self
    {
        if( null === static::$instance )

            static::$instance = new static();

        return static::$instance;
    }

    protected function __construct()
    {
        $this->types = [];
    }

    /**
     * @param $type
     * @param Input $input
     * @return Inputs
     */
    public function addType( $type, Input $input ) : self
    {
        $this->types[$type] = $input;

        return $this;
    }

    /**
     * @param string $type
     * @param array $options
     * @return Input|null
     * @throws ViewException
     */
    public function factory( string $type, array $options ) : ?Input
    {
        $inputClass = $this->getClass( $type );

        if( empty( $inputClass ) )

            return null;

        $options += [
            'required' => false,
            'disabled' => false,
            'styleNamespace' => $this->styleNamespace
        ];

        return new $inputClass( $options );
    }

    public function namespacePrefix( string $class )
    {
        return empty( $this->styleNamespace ) ? $class : $this->styleNamespace .'-'. $class;
    }

    protected function assignStyleNamespace( string $namespace ) : self
    {
        $this->styleNamespace = $namespace;

        return $this;
    }

    /**
     * @param string $type
     * @return string|null
     * @throws ViewException
     */
    private function getClass( string $type ) : ?string
    {
        if( ! array_key_exists( $type, $this->types ) )

            $this->types[ $type ] = $this->getDefaultTypeClass( $type );

        return $this->types[ $type ];
    }

    /**
     * @param string $type
     * @return string|null
     * @throws ViewException
     */
    private function getDefaultTypeClass(string $type ) : ?string
    {
        $class = __NAMESPACE__ .'\\Types\\'. ucfirst( $type ) . 'Input';

        if( ! class_exists( $class ) )

            throw new ViewException( 'Input type does not exist' );

        return $class;
    }
}