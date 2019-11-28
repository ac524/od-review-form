<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/21/2019
 * Time: 12:33 PM
 */

namespace OdReviewForm\Core\Plugin;

use OdReviewForm\Core\Collections\MapCollection;
use OdReviewForm\Core\Plugin\Components\Component;
use OdReviewForm\Core\Plugin\Exceptions\InvalidPluginConfigurationException;
use OdReviewForm\Core\Plugin\Interfaces\ComponentInterface;

abstract class Plugin
{

    /** @var string Absolute path of WP Plugin file. */
    private $file;

    /** @var string Readable name for the plugin. */
    protected $name;

    /** @var string Plugin version identifier. */
    protected $version;

    /** @var string URL friendly unique identifier. */
    protected $slug;

    /** @var array|null */
    protected $componentClasses;

    /** @var MapCollection Collection of registered components. */
    private $components;

    /**
     * Plugin constructor.
     * @param string $pluginFile
     * @throws InvalidPluginConfigurationException
     */
    public function __construct( string $pluginFile )
    {

        $this->file = $pluginFile;

        $this->processPluginFileData();

        if( !empty( $this->componentClasses ) )

            $this->loadComponents();

    }

    /**
     * @return string
     */
    public function getName() : string
    {

        return $this->name;

    }

    public function getVersion() : string
    {

        return $this->version;

    }

    /**
     * @return string
     */
    public function getSlug() : string
    {

        return $this->slug;

    }

    public function getUrl( string $path = '' ) : string
    {

        return plugins_url( $path, $this->file );

    }

    public function getPath( string $path = '' )
    {
    	return plugin_dir_path( $this->file ) . ltrim( $path, '/' );
    }

    /**
     * Collection of registered Components
     * @return MapCollection
     */
    public function getComponents() : MapCollection
    {

        return $this->components;

    }

    /**
     * @param string $id
     * @return ComponentInterface
     */
    public function getComponent( string $id ) : ComponentInterface
    {

        return $this->components->get( $id );

    }

    /**
     * Process comment header data from the WP Plugin file for attribute defaults.
     */
    protected function processPluginFileData() : void
    {

        $headers = [
            'name' => 'Plugin Name',
            'version' => 'Version',
            'slug' => 'ComposerPress Slug',
        ];

        $pluginData = \get_file_data( $this->file, $headers, 'plugin' );

        foreach ( [ 'name', 'version', 'slug' ] as $propertyName )

            if( empty( $this->{ $propertyName } ) && !empty( $pluginData[ $propertyName ] ) )

                $this->{ $propertyName } = $pluginData[ $propertyName ];

    }

    /**
     * @return Plugin
     * @throws InvalidPluginConfigurationException
     */
    public function loadComponents() : self
    {

        if( null === $this->components && !empty( $this->componentClasses ) ) {

            $this->components = new MapCollection();

            foreach( $this->componentClasses as $componentClassName )

                $this->registerComponent(

                    $this->componentFactory( $componentClassName )

                );

        }

        return $this;

    }

	/**
	 * @param Component|ComponentInterface $component
	 * @return Plugin
	 * @throws InvalidPluginConfigurationException
	 */
	public function registerComponent( ComponentInterface $component ) : self
	{

		if( $this->components->containsKey( $component->getId() ) )

			throw new InvalidPluginConfigurationException( 'A Plugin cannot contain two components with the same id.' );

		$this->components->set( $component->getId(), $component );

		$component->registerHooks();

		return $this;

	}

    /**
     * @param string $componentClassName The name of the component class.
     * @return Component|ComponentInterface
     *
     * @see Component
     */
    private function componentFactory( string $componentClassName ) : ComponentInterface
    {

        return new $componentClassName( $this );

    }

}