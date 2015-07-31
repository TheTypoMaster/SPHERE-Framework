<?php
namespace SPHERE\Application\Platform\Assistance\Error;

use SPHERE\Application\IModuleInterface;
use SPHERE\Application\IServiceInterface;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\Common\Window\Stage;

/**
 * Class Error
 *
 * @package SPHERE\Application\System\Assistance\Error
 */
class Error implements IModuleInterface
{

    public static function registerModule()
    {

        /**
         * Register Navigation
         */
        Main::getDisplay()->addApplicationNavigation(
            new Link( new Link\Route( __NAMESPACE__ ), new Link\Name( 'Fehlermeldungen' ) )
        );
        Main::getDisplay()->addModuleNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Authenticator' ), new Link\Name( 'Authentifikator' ) )
        );
        Main::getDisplay()->addModuleNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Authorization' ), new Link\Name( 'Berechtigung' ) )
        );
        Main::getDisplay()->addModuleNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Shutdown' ), new Link\Name( 'Betriebsstörung' ) )
        );

        /**
         * Register Route
         */
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__,
                'Error::frontendError'
            )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Authenticator',
                'Frontend::frontendAuthenticator'
            )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Authorization',
                'Frontend::frontendRoute'
            )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Shutdown',
                'Frontend::frontendShutdown'
            )
        );
    }

    /**
     * @return IServiceInterface
     */
    public static function useService()
    {
        // TODO: Implement useService() method.
    }

    /**
     * @return IFrontendInterface
     */
    public static function useFrontend()
    {
        // TODO: Implement useFrontend() method.
    }

    /**
     * @return Stage
     */
    public function frontendError()
    {

        $Stage = new Stage( 'Fehlermeldungen', 'Bitte wählen Sie ein Thema' );

        return $Stage;
    }
}