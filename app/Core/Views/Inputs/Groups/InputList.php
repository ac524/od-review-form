<?php


namespace OdReviewForm\Core\Views\Inputs\Groups;


use OdReviewForm\Core\Views\Inputs\Input;
use OdReviewForm\Core\Views\Inputs\Inputs;
use OdReviewForm\Core\Views\HtmlOutput;;

class InputList extends HtmlOutput
{

    /** @var Inputs */
    protected static $INPUTS_CLASS = Inputs::class;

    /** @var array */
    private $inputs;

    /** @var Inputs */
    private $inputFactory;

    private $baseName;

    public function __construct()
    {
        $this->inputs = [];

        $this->inputFactory = static::$INPUTS_CLASS::instance();
    }

    public function setBaseName( ?string $baseName ) : self
    {
        $this->baseName = $baseName;

        return $this;
    }

    public function hasBaseName() : bool
    {
        return ! empty( $this->baseName );
    }

    public function addInput( string $type, array $options = []) : self
    {
        if( $this->hasBaseName() )

            $options['name'] = $this->baseName .'.'. $options['name'];

        $this->inputs[] = $this->inputFactory->factory( $type, $options );

        return $this;
    }

    public function getHtml(): string
    {
        return
            '<ul class="'. $this->inputFactory->namespacePrefix( 'list-input' ) .'">'.
                implode( '', array_map( [ $this, 'itemHtml' ], $this->inputs ) ).
            '</ul>';
    }

    private function itemHtml( Input $input )
    {
        $classes = [];

        $sizeClass = $input->sizeClass();

        if( ! empty( $sizeClass ) )

            $classes[] = $sizeClass;

        $classAttr = empty( $classes ) ? '' : sprintf(' class="%s"', implode( ' ', $classes));

        return sprintf( '<li%s>%s</li>', $classAttr, $input );
    }

}