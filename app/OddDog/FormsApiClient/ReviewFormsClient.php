<?php


namespace OdReviewForm\OddDog\FormsApiClient;


class ReviewFormsClient {

    /** @var ReviewFormsClient[] */
    private static $instances = [];

    private $accountCode;

    /** @var Request|null */
    private $lastRequest;

    private $apiToken;

    /**
     * @return ReviewFormsClient
     */
    public static function instance( string $accountCode, ?string $token = null ) : self
    {
        if( empty( self::$instances[ $accountCode ] ) )

            self::$instances[ $accountCode ] = new self( $accountCode );

        if( ! empty( $token ) && empty( self::$instances[ $accountCode ]->apiToken ) )

            self::$instances[ $accountCode ]->apiToken = $token;

        return self::$instances[ $accountCode ];
    }

    private function __construct( string $accountCode )
    {
        $this->accountCode = $accountCode;
    }

    /**
     * @param array $data
     * @return Request|null
     */
    public function validate( array $data = [] ) : ?Request
    {
        $request = $this->request( 'validate/'. $this->accountCode, $data, "POST" );

        return $request;
    }

    /**
     * @return Request|null
     * @throws ApiClientException
     */
    public function locations() : ?Request
    {
        return $this->requestAuthenticated( 'account/'. $this->accountCode .'/locations' );
    }

    /**
     * @param array $locations
     * @return Request|null
     * @throws ApiClientException
     */
    public function updateLocationDetails( array $locations ) : ?Request
    {
//        var_dump( $locations );
//        die();


        return $this->requestAuthenticated( 'account/'. $this->accountCode .'/locations/update', [
            'locations' => $locations
        ], 'POST' );
    }

    /**
     * @param string $name
     * @param string $url
     * @param string $location
     * @return Request|null
     * @throws ApiClientException
     */
    public function addLocationLink( string $name, string $url, string $location = 'Default' ) : ?Request
    {
        return $this->requestAuthenticated( 'account/'. $this->accountCode .'/links', [
            'name' => $name,
            'url' => $url,
            'location' => $location
        ], 'POST' );
    }

    /**
     * @return Request|null
     */
    public function lastRequest() : ?Request
    {
        return $this->lastRequest;
    }

    /**
     * @param string $endpoint
     * @param array|null $data
     * @param string $method
     *
     * @return Request|Traits\RequestCurl|null
     */
    protected function request( string $endpoint, ?array $data = [], string $method = "GET" )
    {
        $this->lastRequest = ( new Request( $endpoint, $data, $method ) )
//				->setToken( $this->api_token )
            ->execute();

        return $this->lastRequest;
    }

    protected function requestAuthenticated( string $endpoint, ?array $data = [], string $method = "GET" )
    {
        if( ! isset( $this->apiToken ) )

            throw new ApiClientException( 'Authenticated requests require an api token to be registered first.' );

        $this->lastRequest = ( new Request( $endpoint, $data, $method ) )
            ->setToken( $this->apiToken )
            ->execute();

        return $this->lastRequest;
    }

}