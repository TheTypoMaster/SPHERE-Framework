<?php
namespace SPHERE\Common\Frontend\Icon\Repository;

use SPHERE\Common\Frontend\Icon\IIconInterface;

/**
 * Class Briefcase
 *
 * @package SPHERE\Common\Frontend\Icon\Repository
 */
class Briefcase implements IIconInterface
{

    /** @var string $Value */
    private $Value = 'glyphicon glyphicon-briefcase';

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

        return '<span class="'.$this->getValue().'"></span>';
    }

    /**
     * @return string
     */
    public function getValue()
    {

        return $this->Value;
    }
}