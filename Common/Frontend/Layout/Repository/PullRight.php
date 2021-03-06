<?php
namespace SPHERE\Common\Frontend\Layout\Repository;

use SPHERE\Common\Frontend\ITemplateInterface;
use SPHERE\System\Extension\Extension;

/**
 * Class PullRight
 *
 * @package SPHERE\Common\Frontend\Layout\Repository
 */
class PullRight extends Extension implements ITemplateInterface
{

    /** @var string $Content */
    private $Content = '';

    /**
     * @param string $Content
     */
    public function __construct($Content)
    {

        $this->Content = $Content;
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

        return '<div class="pull-right">'.$this->Content.'</div>';
        return '<div class="clearfix"><div class="pull-right">'.$this->Content.'</div></div>';
    }
}
