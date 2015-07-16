<?php
namespace SPHERE\Common;

use MOC\V\Component\Router\Component\Bridge\Repository\UniversalRouter;
use SPHERE\Application\Dispatcher;
use SPHERE\Application\System\System;
use SPHERE\Common\Window\Display;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\System\Authenticator\Type\Get;
use SPHERE\System\Authenticator\Type\Post;
use SPHERE\System\Extension\Configuration;

/**
 * Class Main
 *
 * @package SPHERE\Common
 */
class Main extends Configuration
{

    /** @var Display $Display */
    private static $Display = null;
    /** @var Dispatcher $Dispatcher */
    private static $Dispatcher = null;

    /**
     *
     */
    public function __construct()
    {

        if (self::getDisplay() === null) {
            self::$Display = new Display();
        }
        if (self::getDispatcher() === null) {
            self::$Dispatcher = new Dispatcher( new UniversalRouter() );
        }
    }

    /**
     * @return Display
     */
    public static function getDisplay()
    {

        return self::$Display;
    }

    /**
     * @return Dispatcher
     */
    public static function getDispatcher()
    {

        return self::$Dispatcher;
    }

    public function runRestApi()
    {

    }

    public function runPlatform()
    {

        $this->getDebugger();
        $this->setErrorHandler();
        $this->setShutdownHandler();

        try {
            /**
             * Register Cluster
             */
            System::registerCluster();
            /**
             * Execute Request
             */
            if ($this->runAuthenticator()) {
                self::getDisplay()->setContent(
                    self::getDispatcher()->fetchRoute(
                        $this->getRequest()->getPathInfo()
                    )
                );
            }
        } catch( \ErrorException $Exception ) {
            self::getDisplay()->setException( $Exception );
        } catch( \Exception $Exception ) {
            self::getDisplay()->setException( $Exception );
        }
        echo self::getDisplay()->getContent();
    }

    /**
     *
     */
    public function setErrorHandler()
    {

        set_error_handler(
            function ( $Code, $Message, $File, $Line ) {

                if (!preg_match( '!apc_store.*?was.*?on.*?gc-list.*?for!is', $Message )) {
                    throw new \ErrorException( $Message, 0, $Code, $File, $Line );
                }
            }, E_ALL
        );
    }

    /**
     *
     */
    public function setShutdownHandler()
    {

        register_shutdown_function(
            function ( Display $Display ) {

                $Error = error_get_last();
                if (!$Error) {
                    return;
                }
                $Display->addServiceNavigation(
                    new Link( new Link\Route( '/' ), new Link\Name( 'Zurück zur Anwendung' ) )
                );
                print $Display->getContent();
            }, self::getDisplay()
        );
    }

    /**
     * @return bool
     */
    public function runAuthenticator()
    {

        if (!array_key_exists( 'REST', $this->getRequest()->getParameterArray() )) {
            $Get = ( new \SPHERE\System\Authenticator\Configuration( new Get() ) )->getAuthenticator();
            $Post = ( new \SPHERE\System\Authenticator\Configuration( new Post() ) )->getAuthenticator();
            if (!( $Get->validateSignature() && $Post->validateSignature() )) {
                self::getDisplay()->setClusterNavigation();
                self::getDisplay()->setApplicationNavigation();
                self::getDisplay()->setModuleNavigation();
                self::getDisplay()->setServiceNavigation( new Link(
                    new Link\Route( '/' ),
                    new Link\Name( 'Zurück zur Anwendung' )
                ) );
                self::getDisplay()->setContent( Dispatcher::fetchRoute( 'System/Assistance/Error/Authenticator' ) );
                return false;
            }
        }
        return true;
    }
}