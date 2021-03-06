<?php
namespace MOC\V\Component\Documentation\Component\Exception;

use MOC\V\Component\Documentation\Exception\DocumentationException;

/**
 * Class ComponentException
 *
 * @package MOC\V\Component\Documentation\Component\Exception
 */
class ComponentException extends DocumentationException
{

    /**
     * @param string $Message
     * @param int    $Code
     * @param null   $Previous
     */
    public function __construct( $Message = "", $Code = 0, $Previous = null )
    {

        parent::__construct( $Message, $Code, $Previous );
    }
}
