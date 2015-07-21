<?php
namespace SPHERE\Application\System\Gatekeeper\Authentication\Identification;

use SPHERE\Application\IModuleInterface;
use SPHERE\Application\IServiceInterface;
use SPHERE\Common\Frontend\Icon\Repository\Lock;
use SPHERE\Common\Frontend\Icon\Repository\Off;
use SPHERE\Common\Frontend\Link\Repository\Primary;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\Common\Window\Stage;

/**
 * Class Identification
 *
 * @package SPHERE\Application\System\Gatekeeper\Authentication\Identification
 */
class Identification implements IModuleInterface
{

    public static function registerModule()
    {

        /**
         * Register Navigation
         */
        Main::getDisplay()->addServiceNavigation(
            new Link( new Link\Route( __NAMESPACE__ ), new Link\Name( 'Anmelden' ), new Link\Icon( new Lock() ) )
        );
        Main::getDisplay()->addServiceNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/SignOut' ), new Link\Name( 'Abmelden' ),
                new Link\Icon( new Off() ) )
        );

        Main::getDisplay()->addApplicationNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Student' ), new Link\Name( 'Schüler' ),
                new Link\Icon( new Lock() ) )
        );
        Main::getDisplay()->addApplicationNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Teacher' ), new Link\Name( 'Lehrer' ),
                new Link\Icon( new Lock() ) )
        );
        Main::getDisplay()->addApplicationNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/Management' ), new Link\Name( 'Verwaltung' ),
                new Link\Icon( new Lock() ) )
        );
        Main::getDisplay()->addApplicationNavigation(
            new Link( new Link\Route( __NAMESPACE__.'/System' ), new Link\Name( 'System' ),
                new Link\Icon( new Lock() ) )
        );

        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__, 'Identification::frontendAuthenticationSwitch'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
            ->setParameterDefault( 'CredentialKey', null )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__.'/SignOut', __NAMESPACE__.'\SignOut::stageSignOut'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
            ->setParameterDefault( 'CredentialKey', null )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__.'/Student', __NAMESPACE__.'\SignIn::stageStudent'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__.'/Teacher', __NAMESPACE__.'\SignIn::stageTeacher'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
            ->setParameterDefault( 'CredentialKey', null )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__.'/Management', __NAMESPACE__.'\SignIn::stageManagement'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
            ->setParameterDefault( 'CredentialKey', null )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__.'/System', __NAMESPACE__.'\SignIn::stageSystem'
        )
            ->setParameterDefault( 'CredentialName', null )
            ->setParameterDefault( 'CredentialLock', null )
            ->setParameterDefault( 'CredentialKey', null )
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
     * @return Stage
     */
    public static function frontendAuthenticationSwitch()
    {

        $View = new Stage();
        $View->setTitle( 'Anmeldung' );
        $View->setMessage( 'Bitte wählen Sie den Typ der Anmeldung' );
        $View->setContent(
            new Primary(
                'Schüler', '/System/Gatekeeper/Authentication/Identification/Student', new Lock()
            ).
            new Primary(
                'Lehrer', '/System/Gatekeeper/Authentication/Identification/Teacher', new Lock()
            ).
            new Primary(
                'Verwaltung', '/System/Gatekeeper/Authentication/Identification/Management', new Lock()
            ).
            new Primary(
                'System', '/System/Gatekeeper/Authentication/Identification/System', new Lock()
            )
        );
        return $View;
    }
}