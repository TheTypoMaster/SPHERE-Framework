<?php
namespace SPHERE\Application\People;

use SPHERE\Application\IClusterInterface;
use SPHERE\Application\People\Group\Group;
use SPHERE\Application\People\Group\Service\Entity\TblGroup;
use SPHERE\Application\People\Meta\Meta;
use SPHERE\Application\People\Person\Person;
use SPHERE\Application\People\Relationship\Relationship;
use SPHERE\Application\People\Search\Search;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Main;
use SPHERE\Common\Window\Navigation\Link;
use SPHERE\Common\Window\Stage;

/**
 * Class People
 *
 * @package SPHERE\Application\People
 */
class People implements IClusterInterface
{

    public static function registerCluster()
    {

        Search::registerApplication();
        Person::registerApplication();
        Group::registerApplication();
        Meta::registerApplication();
        Relationship::registerApplication();

        Main::getDisplay()->addClusterNavigation(
            new Link( new Link\Route( __NAMESPACE__ ), new Link\Name( 'Personen' ) )
        );
        Main::getDispatcher()->registerRoute( Main::getDispatcher()->createRoute(
            __NAMESPACE__, __CLASS__.'::frontendDashboard'
        ) );

        $tblPersonAll = Person::useService()->getPersonAll();
        Main::getDispatcher()->registerWidget( 'Personen', new Panel( 'Anzahl an Personen', 'Insgesamt: '.count( $tblPersonAll ) ) );

        $tblGroupAll = Group::useService()->getGroupAll();
        if( $tblGroupAll ) {
            /** @var TblGroup $tblGroup */
            foreach ((array)$tblGroupAll as $Index => $tblGroup) {
                $tblGroupAll[$tblGroup->getName()] = $tblGroup->getName().': '.count( Group::useService()->getPersonAllByGroup( $tblGroup ) );
                $tblGroupAll[$Index] = false;
            }
            $tblGroupAll = array_filter( $tblGroupAll );
            Main::getDispatcher()->registerWidget( 'Personen', new Panel( 'Personen in Gruppen', $tblGroupAll ), 2, 2 );
        }
    }

    /**
     * @return Stage
     */
    public function frontendDashboard()
    {

        $Stage = new Stage( 'Dashboard', 'Personen' );

        $Stage->setContent( Main::getDispatcher()->fetchDashboard( 'Personen' ) );

        return $Stage;
    }
}
