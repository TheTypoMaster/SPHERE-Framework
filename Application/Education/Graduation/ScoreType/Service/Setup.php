<?php

namespace SPHERE\Application\Education\Graduation\ScoreType\Service;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use SPHERE\System\Database\Fitting\Structure;

class Setup
{

    /** @var null|Structure $Connection */
    private $Connection = null;

    /**
     * @param Structure $Connection
     */
    function __construct(Structure $Connection)
    {

        $this->Connection = $Connection;
    }

    /**
     * @param bool $Simulate
     *
     * @return string
     */
    public function setupDatabaseSchema($Simulate = true)
    {

        /**
         * Table
         */
        $Schema = clone $this->Connection->getSchema();
        $tblScoreTypes = $this->setTableScoreTypes($Schema);
        /**
         * Migration & Protocol
         */
        $this->Connection->addProtocol(__CLASS__);
        $this->Connection->setMigration($Schema, $Simulate);
        return $this->Connection->getProtocol($Simulate);
    }


    /**
     * @param Schema $Schema
     *
     * @return Table
     */
    private function setTableScoreTypes(Schema &$Schema)
    {

        $Table = $this->Connection->createTable($Schema, 'tblScoreType');
        if (!$this->Connection->hasColumn('tblScoreType', 'Name')) {
            $Table->addColumn('Name', 'string');
        }
        if (!$this->Connection->hasColumn('tblScoreType', 'Short')) {
            $Table->addColumn('Short', 'string');
        }
        return $Table;
    }
}
