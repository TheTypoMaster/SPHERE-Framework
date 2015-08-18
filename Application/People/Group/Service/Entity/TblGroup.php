<?php
namespace SPHERE\Application\People\Group\Service\Entity;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use SPHERE\System\Database\Fitting\Element;

/**
 * @Entity
 * @Table(name="tblGroup")
 * @Cache(usage="READ_ONLY")
 */
class TblGroup extends Element
{

    const ATTR_NAME = 'Name';
    const ATTR_IS_LOCKED = 'IsLocked';
    const ATTR_META_TABLE = 'MetaTable';

    /**
     * @Column(type="string")
     */
    protected $Name;
    /**
     * @Column(type="string")
     */
    protected $Description;
    /**
     * @Column(type="text")
     */
    protected $Remark;
    /**
     * @Column(type="boolean")
     */
    protected $IsLocked;
    /**
     * @Column(type="string")
     */
    protected $MetaTable;

    /**
     * @param $Name
     */
    function __construct( $Name )
    {

        $this->setName( $Name );
    }

    /**
     * @return string
     */
    public function getDescription()
    {

        return $this->Description;
    }

    /**
     * @param string $Description
     */
    public function setDescription( $Description )
    {

        $this->Description = $Description;
    }

    /**
     * @return string
     */
    public function getRemark()
    {

        return $this->Remark;
    }

    /**
     * @param string $Remark
     */
    public function setRemark( $Remark )
    {

        $this->Remark = $Remark;
    }

    /**
     * @return bool
     */
    public function getIsLocked()
    {

        return (bool)$this->IsLocked;
    }

    /**
     * @param bool $IsLocked
     */
    public function setIsLocked( $IsLocked )
    {

        $this->IsLocked = (bool)$IsLocked;
    }

    /**
     * @return string
     */
    public function getMetaTable()
    {

        return $this->MetaTable;
    }

    /**
     * @param string $MetaTable
     */
    public function setMetaTable( $MetaTable )
    {

        $this->MetaTable = $MetaTable;
    }

    /**
     * @return string
     */
    public function getName()
    {

        return $this->Name;
    }

    /**
     * @param string $Name
     */
    public function setName( $Name )
    {

        $this->Name = $Name;
    }
}