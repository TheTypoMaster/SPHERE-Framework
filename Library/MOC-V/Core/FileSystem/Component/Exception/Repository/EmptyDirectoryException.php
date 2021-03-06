<?php
namespace MOC\V\Core\FileSystem\Component\Exception\Repository;

use MOC\V\Core\FileSystem\Component\Exception\ComponentException;

/**
 * Class EmptyDirectoryException
 *
 * @package MOC\V\Core\FileSystem\Component\Exception
 */
class EmptyDirectoryException extends ComponentException
{

    /**
     * @param string $Message
     * @param int    $Code
     * @param null   $Previous
     */
    public function __construct( $Message = "", $Code = 0, $Previous = null )
    {

        $Message = 'Directory location must not be empty!';

        parent::__construct( $Message, $Code, $Previous );
    }

}
