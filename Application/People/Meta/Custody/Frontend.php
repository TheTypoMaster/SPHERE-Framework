<?php
namespace SPHERE\Application\People\Meta\Custody;

use SPHERE\Application\People\Person\Service\Entity\TblPerson;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\AutoCompleter;
use SPHERE\Common\Frontend\Form\Repository\Field\TextArea;
use SPHERE\Common\Frontend\Form\Repository\Title;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\MapMarker;
use SPHERE\Common\Frontend\Icon\Repository\Nameplate;
use SPHERE\Common\Frontend\Icon\Repository\Pencil;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Window\Stage;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\People\Meta\Custody
 */
class Frontend implements IFrontendInterface
{

    /**
     * @param TblPerson $tblPerson
     * @param array     $Meta
     *
     * @return Stage
     */
    public function frontendMeta( TblPerson $tblPerson = null, $Meta = array() )
    {

        $Stage = new Stage();

        $Stage->setContent( ( new Form( array(
            new FormGroup( array(
                new FormRow( array(
                    new FormColumn(
                        new AutoCompleter( 'Meta[Occupation]', 'Beruf', 'Beruf',
                            array(), new MapMarker()
                        ) ),
                ) ),
                new FormRow( array(
                    new FormColumn(
                        new AutoCompleter( 'Meta[Employment]', 'Arbeitsstelle', 'Arbeitsstelle',
                            array(), new Nameplate()
                        ) ),
                ) ),
            ), new Title( 'Berufliches' ) ),
            new FormGroup( array(
                new FormRow( array(
                    new FormColumn(
                        new TextArea( 'Meta[Remark]', 'Bemerkungen', 'Bemerkungen', new Pencil() )
                    ),
                ) ),
            ), new Title( 'Sonstiges' ) ),
        ), new Primary( 'Informationen speichern' ) )
        )->setConfirm( 'Eventuelle Änderungen wurden noch nicht gespeichert.' ) );

        return $Stage;
    }
}