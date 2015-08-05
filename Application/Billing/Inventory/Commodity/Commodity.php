<?php

namespace SPHERE\Application\Billing\Inventory\Commodity;

use SPHERE\Application\IModuleInterface;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\System\Database\Link\Identifier;

class Commodity implements IModuleInterface
{

    public static function registerModule()
    {

        /**
         * Register Module
         */
//        Support::registerModule();
        /**
         * Register Navigation
         */
//        Main::getDisplay()->addApplicationNavigation(
//            new Link( new Link\Route( __NAMESPACE__ ), new Link\Name( 'Leistungen' ), new Link\Icon( new \SPHERE\Common\Frontend\Icon\Repository\Commodity() ) ),
//            new Link\Route( '/BillingSystem/Billing' )
//        );
        /**
         * Register Route
         */

        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__,
                __NAMESPACE__.'\Frontend::frontendStatus'
            )->setParameterDefault( 'Commodity', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Create',
                __NAMESPACE__.'\Frontend::frontendCreate'
            )->setParameterDefault( 'Commodity', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Delete',
                __NAMESPACE__.'\Frontend::frontendDelete'
            )->setParameterDefault( 'Id', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Edit',
                __NAMESPACE__.'\Frontend::frontendEdit'
            )->setParameterDefault( 'Id', null )
                ->setParameterDefault( 'Commodity', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Account/Select',
                __NAMESPACE__.'\Frontend::frontendItemAccountSelect'
            )->setParameterDefault( 'Id', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Account/Add',
                __NAMESPACE__.'\Frontend::frontendItemAccountAdd'
            )->setParameterDefault( 'tblAccountId', null )
                ->setParameterDefault( 'tblItemId', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Account/Remove',
                __NAMESPACE__.'\Frontend::frontendItemAccountRemove'
            )->setParameterDefault( 'Id', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Add',
                __NAMESPACE__.'\Frontend::frontendItemAdd'
            )->setParameterDefault( 'tblCommodityId', null )
                ->setParameterDefault( 'tblItemId', null )
                ->setParameterDefault( 'Item', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Select',
                __NAMESPACE__.'\Frontend::frontendItemSelect'
            )->setParameterDefault( 'Id', null )
        );
        Main::getDispatcher()->registerRoute(
            Main::getDispatcher()->createRoute( __NAMESPACE__.'/Item/Remove',
                __NAMESPACE__.'\Frontend::frontendItemRemove'
            )->setParameterDefault( 'Id', null )
        );
    }

    /**
     * @return Service
     */
    public static function useService()
    {

        return new Service( new Identifier( 'Billing', 'Inventory', 'Commodity' ),
            __DIR__.'/Service/Entity', __NAMESPACE__.'\Service\Entity'
        );
    }

    /**
     * @return IFrontendInterface
     */
    public static function useFrontend()
    {
        // TODO: Implement useFrontend() method.
    }

}