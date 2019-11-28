<?php


namespace OdReviewForm\Core\Views\Inputs;


use OdReviewForm\Core\Traits\ObjectProperties;
use OdReviewForm\Core\Views\Inputs\Traits\InputHtmlAttrs;
use OdReviewForm\Core\Views\HtmlOutput;;

abstract class Input extends HtmlOutput
{
    use ObjectProperties;
    use InputHtmlAttrs;

    protected $label;

    protected $id;

    protected $sublabel;

    protected $name;

    protected $value;

    protected $required;

    protected $disabled;

    protected $size;

    protected $styleNamespace;

    protected $inline;

    public function __construct( array $options )
    {
        $this->updateProperties( $options );
    }

    /**
     * @return string
     */
    public function  label() : string
    {
        return $this->label;
    }

    public function getHtml(): string
    {
        $htmlTemplate =
            '<div class="%s">'.
                '%s'.
                '<div class="'. $this->namespacePrefix( 'field-input' ) .'">%s</div>'.
                '%s'.
            '</div>';

        return sprintf( $htmlTemplate, $this->containerClass(), $this->labelHtml(), $this->inputHtml(), $this->subLabelHtml() );
    }

    public function containerClass() : string
    {
        $classes = [ $this->namespacePrefix('field' ) ];

        if( $this->inline )

            $classes[] = $this->namespacePrefix('field-inline' );

        return implode( ' ', $classes );
    }

    public function sizeClass() : ?string
    {
        return empty( $this->size ) ? null : $this->namespacePrefix('field-'.$this->size );
    }

    protected function labelHtml() : string
    {
        return $this->label ? sprintf('<label>%s</label>', $this->label ) : '';
    }

    abstract protected function inputHtml() : string;

    /**
     * @return string
     */
    protected function inputName() : string
    {
        if( false === strpos( $this->name, '.' ) )

            return $this->name;

        $parts = explode( '.', $this->name );

        return $parts[0] . implode( '', array_map( function ( $key ) { return '['. $key .']'; }, array_slice( $parts, 1 ) ) );
    }

    protected function subLabelHtml() : string
    {
        if( empty( $this->sublabel ) )

            return '';

        return sprintf( '<div class="%s">%s</div>', $this->namespacePrefix( 'field-sub-label' ), $this->sublabel );
    }

    protected function namespacePrefix( string $class )
    {
        return empty( $this->styleNamespace ) ? $class : $this->styleNamespace .'-'. $class;
    }

}