<?php
namespace SPHERE\Common\Frontend\Layout\Repository;

use MOC\V\Component\Template\Component\IBridgeInterface;
use SPHERE\Common\Frontend\ITemplateInterface;
use SPHERE\System\Extension\Extension;

/**
 * Class Panel
 *
 * @package SPHERE\Common\Frontend\Layout\Repository
 */
class Panel extends Extension implements ITemplateInterface
{

    const PANEL_TYPE_DEFAULT = 'panel-default';
    const PANEL_TYPE_PRIMARY = 'panel-primary';
    const PANEL_TYPE_SUCCESS = 'panel-success';
    const PANEL_TYPE_WARNING = 'panel-warning';
    const PANEL_TYPE_INFO = 'panel-info';
    const PANEL_TYPE_DANGER = 'panel-danger';
    /** @var string $Hash */
    protected $Hash = '';
    /** @var IBridgeInterface $Template */
    private $Template = null;
    /** @var string|array $Content */
    private $Content = '';
    /** @var bool|int $Filter */
    private $Filter = false;

    /**
     * @param string       $Title
     * @param string|array $Content
     * @param string       $Type
     * @param null|string  $Footer
     * @param bool         $Filter
     */
    public function __construct($Title, $Content, $Type = Panel::PANEL_TYPE_DEFAULT, $Footer = null, $Filter = false)
    {

        $this->Template = $this->getTemplate(__DIR__.'/Panel.twig');
        $this->Template->setVariable('Title', $Title);
        $this->Filter = $Filter;
        $this->Content = ( is_array($Content) ? array_filter($Content) : $Content );
        $this->Template->setVariable('Filter', $Filter);
        $this->Template->setVariable('Footer', $Footer);
        $this->Template->setVariable('Type', $Type);
    }

    /**
     * @return array
     */
    public function getElementList()
    {

        if (!is_array($this->Content) && is_string($this->Content)) {
            return (array)$this->Content;
        } else {
            return $this->Content;
        }
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

        if (is_array($this->Content)) {
            if ($this->Filter) {
                $this->Template->setVariable('Hash', $this->getHash());
                if (!is_numeric($this->Filter)) {
                    $this->Filter = 50;
                }
                $this->Template->setVariable('FilterSize', $this->Filter);
                array_unshift($this->Content,
                    '<input type="text" class="form-control search" name="PanelSearch" placeholder="Filtern">'
                    .( $this->Filter < count($this->Content)
                        ? new PullRight(new Label($this->Filter.' von '.count($this->Content).' Einträgen'))
                        : new PullRight(new Label(count($this->Content).' Einträge'))
                    )
                );
            }
            $this->Template->setVariable('Content', array_shift($this->Content));
            $this->Template->setVariable('ContentList', $this->Content);
        } else {
            $this->Template->setVariable('Content', $this->Content);
            $this->Template->setVariable('ContentList', array());
        }
        return $this->Template->getContent();
    }

    /**
     * @return string
     */
    public function getHash()
    {

        if (empty( $this->Hash )) {
            $Content = $this->Content;
            array_walk($Content, function (&$G) {

                if (is_object($G)) {
                    $G = serialize($G);
                }
            });
            $this->Hash = sha1(json_encode($Content));
        }
        return $this->Hash;
    }

    /**
     * @return string
     */
    public function getName()
    {

        return sha1(print_r($this->Content, true));
    }
}
