<?php
namespace SPHERE\Common\Frontend\Layout\Repository;

use MOC\V\Core\FileSystem\Component\IBridgeInterface;
use SPHERE\Common\Frontend\ITemplateInterface;
use SPHERE\System\Extension\Configuration;

/**
 * Class Thumbnail
 *
 * @package SPHERE\Common\Frontend\Layout\Repository
 */
class Thumbnail extends Configuration implements ITemplateInterface
{

    const THUMBNAIL_TYPE_DEFAULT = '';
    const THUMBNAIL_TYPE_CIRCLE = 'img-circle';

    /** @var \MOC\V\Component\Template\Component\IBridgeInterface $Template */
    private $Template = null;
    /** @var IBridgeInterface $File */
    private $File;

    /**
     * @param IBridgeInterface $File
     * @param string           $Title
     * @param string           $Description
     * @param array            $ButtonList
     * @param string           $Type THUMBNAIL_TYPE_DEFAULT
     */
    public function __construct(
        IBridgeInterface $File,
        $Title,
        $Description = '',
        $ButtonList = array(),
        $Type = self::THUMBNAIL_TYPE_DEFAULT
    ) {

        if (!is_array( $ButtonList )) {
            $ButtonList = array( $ButtonList );
        }

        $this->Template = $this->getTemplate( __DIR__.'/Thumbnail.twig' );
        $this->Template->setVariable( 'File', $File->getLocation() );

        $Size = getimagesize( $File->getRealPath() );

        $this->Template->setVariable( 'Height', $Size[1] );

        $this->Template->setVariable( 'Type', $Type );
        $this->Template->setVariable( 'Title', $Title );
        $this->Template->setVariable( 'Description', $Description );
        $this->Template->setVariable( 'ButtonList', $ButtonList );
        $this->File = $File;
    }

    /**
     * @return string
     */
    public function __toString()
    {

        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {

        return $this->Template->getContent();
    }
}
