<?php
namespace SPHERE\Application\System\Gatekeeper\Consumer\Service;

use SPHERE\Application\System\Gatekeeper\Account\Account;
use SPHERE\Application\System\Gatekeeper\Consumer\Service\Entity\TblConsumer;
use SPHERE\Application\System\Information\Protocol\Protocol;
use SPHERE\System\Database\Fitting\Binding;

/**
 * Class Data
 *
 * @package SPHERE\Application\System\Gatekeeper\Consumer\Service
 */
class Data
{

    /** @var null|Binding $Connection */
    private $Connection = null;

    /**
     * @param Binding $Connection
     */
    function __construct( Binding $Connection )
    {

        $this->Connection = $Connection;
    }

    public function setupDatabaseContent()
    {

        $this->createConsumer( 'DEMO', 'Consumer' );
    }

    /**
     * @param string $Acronym
     * @param string $Name
     *
     * @return TblConsumer
     */
    public function createConsumer( $Acronym, $Name )
    {

        $Manager = $this->Connection->getEntityManager();
        $Entity = $Manager->getEntity( 'TblConsumer' )
            ->findOneBy( array( TblConsumer::ATTR_ACRONYM => $Acronym ) );
        if (null === $Entity) {
            $Entity = new TblConsumer( $Acronym );
            $Entity->setName( $Name );
            $Manager->saveEntity( $Entity );
            Protocol::useService()->createInsertEntry( $this->Connection->getDatabase(), $Entity );
        }
        return $Entity;
    }

    /**
     * @param string $Name
     *
     * @return bool|TblConsumer
     */
    public function getConsumerByName( $Name )
    {

        $Entity = $this->Connection->getEntityManager()->getEntity( 'TblConsumer' )
            ->findOneBy( array( TblConsumer::ATTR_NAME => $Name ) );
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @param string $Acronym
     *
     * @return bool|TblConsumer
     */
    public function getConsumerByAcronym( $Acronym )
    {

        $Entity = $this->Connection->getEntityManager()->getEntity( 'TblConsumer' )
            ->findOneBy( array( TblConsumer::ATTR_ACRONYM => $Acronym ) );
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @param integer $Id
     *
     * @return bool|TblConsumer
     */
    public function getConsumerById( $Id )
    {

        $Entity = $this->Connection->getEntityManager()->getEntityById( 'TblConsumer', $Id );
        return ( null === $Entity ? false : $Entity );
    }

    /**
     * @return TblConsumer[]|bool
     */
    public function getConsumerAll()
    {

        $EntityList = $this->Connection->getEntityManager()->getEntity( 'TblConsumer' )->findAll();
        return ( empty( $EntityList ) ? false : $EntityList );
    }

    /**
     * @param null|string $Session
     *
     * @return bool|TblConsumer
     */
    public function getConsumerBySession( $Session = null )
    {

        if (false !== ( $tblAccount = Account::useService()->getAccountBySession( $Session ) )) {
            return $tblAccount->getServiceTblConsumer();
        } else {
            return false;
        }
    }
}