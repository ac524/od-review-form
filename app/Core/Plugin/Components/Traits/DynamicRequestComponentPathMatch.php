<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 9:28 AM
 */

namespace OdReviewForm\Core\Plugin\Components\Traits;

/**
 * Trait DynamicRequestComponentPathMatch
 * @package ComposerPress\Plugin\Components\Traits
 *
 * @property string $requestPathRegex Regex to match against the request path.
 *
 */
trait DynamicRequestComponentPathMatch
{

    private $requestPathMatch;

    /** @var string Current request url path */
    private $requestPath;

    /**
     * @return string|null
     */
    protected function getRequestPathRegex() : ?string
    {

        return $this->requestPathRegex ?? null;

    }

    /**
     * @return array
     */
    protected function getRequestPathMatch() : array
    {

        if( null === $this->requestPathMatch  ) {

            $this->requestPathMatch = [];

            if( !empty( $this->getRequestPathRegex() ) )

                preg_match( $this->getRequestPathRegex(), $this->getRequestPath(), $this->requestPathMatch );

        }

        return $this->requestPathMatch;

    }

    /**
     * @return bool
     */
    protected function isRequestPathMatch() : bool
    {

        return !empty( $this->getRequestPathMatch() );

    }

    /**
     * @return string
     */
    protected function getRequestPath(): string
    {

        if( null === $this->requestPath )

            $this->requestPath = filter_input( INPUT_SERVER, 'REQUEST_URI', FILTER_DEFAULT );

        return $this->requestPath;

    }

}