<?php
namespace SPHERE\Common\Frontend\Icon\Repository;

use SPHERE\Common\Frontend\Icon\IIconInterface;

/**
 * Class Stethoscope
 *
 * @package SPHERE\Common\Frontend\Icon\Repository
 */
class Stethoscope implements IIconInterface
{

    /** @var string $Value */
    private $Value = 'glyphicons glyphicons-stethoscope';

    /**
     * @return string
     */
    function __toString()
    {

        return $this->getContent();
    }

    /**
     * @return string
     */
    public function getContent()
    {

        return '<span class="'.$this->getValue().'" aria-hidden="true"></span>';
    }

    /**
     * @return string
     */
    public function getValue()
    {

        return $this->Value;
    }
}