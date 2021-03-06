<?php
namespace SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service;

use SPHERE\Application\Platform\Gatekeeper\Authorization\Account\Account;
use SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service\Entity\TblConsumer;
use SPHERE\Application\Platform\System\Protocol\Protocol;
use SPHERE\System\Database\Fitting\Binding;

/**
 * Class Data
 *
 * @package SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service
 */
class Data
{

    /** @var null|Binding $Connection */
    private $Connection = null;

    /**
     * @param Binding $Connection
     */
    function __construct(Binding $Connection)
    {

        $this->Connection = $Connection;
    }

    public function setupDatabaseContent()
    {

        $this->createConsumer('DEMO', 'Mandant');
    }

    /**
     * @param string $Acronym
     * @param string $Name
     *
     * @return \SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service\Entity\TblConsumer
     */
    public function createConsumer($Acronym, $Name)
    {

        $Manager = $this->Connection->getEntityManager();
        $Entity = $Manager->getEntity('TblConsumer')
            ->findOneBy(array(TblConsumer::ATTR_ACRONYM => $Acronym));
        if (null === $Entity) {
            $Entity = new TblConsumer($Acronym);
            $Entity->setName($Name);
            $Manager->saveEntity($Entity);
            Protocol::useService()->createInsertEntry($this->Connection->getDatabase(), $Entity);
        }
        return $Entity;
    }

    /**
     * @param string $Name
     *
     * @return bool|TblConsumer
     */
    public function getConsumerByName($Name)
    {

        $Entity = $this->Connection->getEntityManager()->getEntity('TblConsumer')
            ->findOneBy(array(TblConsumer::ATTR_NAME => $Name));
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @param string $Acronym
     *
     * @return bool|TblConsumer
     */
    public function getConsumerByAcronym($Acronym)
    {

        $Entity = $this->Connection->getEntityManager()->getEntity('TblConsumer')
            ->findOneBy(array(TblConsumer::ATTR_ACRONYM => $Acronym));
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @param integer $Id
     *
     * @return bool|\SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service\Entity\TblConsumer
     */
    public function getConsumerById($Id)
    {

        $Entity = $this->Connection->getEntityManager()->getEntityById('TblConsumer', $Id);
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @return \SPHERE\Application\Platform\Gatekeeper\Authorization\Consumer\Service\Entity\TblConsumer[]|bool
     */
    public function getConsumerAll()
    {

        $EntityList = $this->Connection->getEntityManager()->getEntity('TblConsumer')->findAll();
        return ( empty( $EntityList ) ? false : $EntityList );
    }

    /**
     * @param null|string $Session
     *
     * @return bool|TblConsumer
     */
    public function getConsumerBySession($Session = null)
    {

        if (false !== ( $tblAccount = Account::useService()->getAccountBySession($Session) )) {
            return $tblAccount->getServiceTblConsumer();
        } else {
            return false;
        }
    }
}
