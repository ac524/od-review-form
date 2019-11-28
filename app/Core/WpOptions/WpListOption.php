<?php
/**
 * Created by PhpStorm.
 * User: ac524
 * Date: 2/22/2019
 * Time: 2:20 PM
 */

namespace OdReviewForm\Core\WpOptions;

use OdReviewForm\Core\Traits\ObjectProperties;
use PhpCollection\Map;
use PhpCollection\Sequence;

abstract class WpListOption extends WpOption
{

    use ObjectProperties;

    /** @var Map|Sequence */
    protected $list;

    protected $listType = Sequence::class;

    /**
     * @return WpJsonOption
     */
    public function load() : WpOption
    {

        if( null !== $this->list )

            return $this;

        $values = json_decode( get_option( $this->getOptionName(), '[]' ), true );

        $listType = $this->listType;

        $this->list = new $listType( $values );

        return $this;

    }

    /**
     * @return Map|Sequence
     */
    public function getList()
    {

        return $this->list;

    }

    /**
     * @return WpJsonOption
     */
    public function save() : WpOption
    {
        update_option( $this->getOptionName(), json_encode( $this->list->all() ) );

		return $this;
    }

	public function has( $item ) : bool
	{
		return in_array( $item, $this->getList()->all() );
    }

}