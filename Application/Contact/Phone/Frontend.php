<?php
namespace SPHERE\Application\Contact\Phone;

use SPHERE\Application\Contact\Phone\Service\Entity\TblToPerson;
use SPHERE\Application\People\Person\Person;
use SPHERE\Application\People\Person\Service\Entity\TblPerson;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\AutoCompleter;
use SPHERE\Common\Frontend\Form\Repository\Field\SelectBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextArea;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\ChevronLeft;
use SPHERE\Common\Frontend\Icon\Repository\Pencil;
use SPHERE\Common\Frontend\Icon\Repository\Person as PersonIcon;
use SPHERE\Common\Frontend\Icon\Repository\Phone as PhoneIcon;
use SPHERE\Common\Frontend\Icon\Repository\PhoneMobil;
use SPHERE\Common\Frontend\Icon\Repository\Remove;
use SPHERE\Common\Frontend\Icon\Repository\TileBig;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Layout\Structure\Layout;
use SPHERE\Common\Frontend\Layout\Structure\LayoutColumn;
use SPHERE\Common\Frontend\Layout\Structure\LayoutGroup;
use SPHERE\Common\Frontend\Layout\Structure\LayoutRow;
use SPHERE\Common\Frontend\Link\Repository\Standard;
use SPHERE\Common\Frontend\Message\Repository\Warning;
use SPHERE\Common\Frontend\Text\Repository\Muted;
use SPHERE\Common\Frontend\Text\Repository\Small;
use SPHERE\Common\Window\Stage;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\Contact\Phone
 */
class Frontend implements IFrontendInterface
{

    /**
     * @param int    $Id
     * @param string $Number
     * @param array  $Type
     *
     * @return Stage
     */
    public function frontendCreateToPerson( $Id, $Number, $Type )
    {

        $Stage = new Stage( 'Telefonnummer', 'Hinzufügen' );
        $Stage->setMessage( 'Eine Telefonnummer zur gewählten Person hinzufügen' );

        $tblPerson = Person::useService()->getPersonById( $Id );

        $Stage->setContent(
            new Layout( array(
                new LayoutGroup( array(
                    new LayoutRow(
                        new LayoutColumn(
                            new Panel( new PersonIcon().' Person',
                                $tblPerson->getFullName(),
                                Panel::PANEL_TYPE_SUCCESS,
                                new Standard( 'Zurück zur Person', '/People/Person', new ChevronLeft(),
                                    array( 'Id' => $tblPerson->getId() )
                                )
                            )
                        )
                    ),
                ) ),
                new LayoutGroup( array(
                    new LayoutRow(
                        new LayoutColumn(
                            Phone::useService()->createPhoneToPerson(
                                $this->formNumber()
                                    ->appendFormButton( new Primary( 'Telefonnummer hinzufügen' ) )
                                    ->setConfirm( 'Eventuelle Änderungen wurden noch nicht gespeichert' )
                                , $tblPerson, $Number, $Type
                            )
                        )
                    )
                ) ),
            ) )
        );

        return $Stage;
    }

    /**
     * @return Form
     */
    private function formNumber()
    {

        $tblPhoneAll = Phone::useService()->getPhoneAll();
        $tblTypeAll = Phone::useService()->getTypeAll();

        return new Form(
            new FormGroup( array(
                new FormRow( array(
                    new FormColumn(
                        new Panel( 'Telefonnummer',
                            array(
                                new SelectBox( 'Type[Type]', 'Typ',
                                    array( '{{ Name }} {{ Description }}' => $tblTypeAll ), new TileBig()
                                ),
                                new AutoCompleter( 'Number', 'Telefonnummer', 'Telefonnummer',
                                    array( 'Number' => $tblPhoneAll ), new PhoneIcon()
                                )
                            ), Panel::PANEL_TYPE_INFO
                        ), 6 ),
                    new FormColumn(
                        new Panel( 'Sonstiges',
                            new TextArea( 'Type[Remark]', 'Bemerkungen', 'Bemerkungen', new Pencil() )
                            , Panel::PANEL_TYPE_INFO
                        ), 6 ),
                ) ),
            ) )
        );
    }

    /**
     * @param TblPerson $tblPerson
     *
     * @return Layout
     */
    public function frontendLayoutPerson( TblPerson $tblPerson )
    {

        $tblPhoneAll = Phone::useService()->getPhoneAllByPerson( $tblPerson );
        if ($tblPhoneAll !== false) {
            array_walk( $tblPhoneAll, function ( TblToPerson &$tblToPerson ) {

                $Panel = array( $tblToPerson->getTblPhone()->getNumber() );
                if ($tblToPerson->getRemark()) {
                    array_push( $Panel, new Muted( new Small( $tblToPerson->getRemark() ) ) );
                }

                $tblToPerson = new LayoutColumn(
                    new Panel(
                        ( preg_match( '!Mobil!is', $tblToPerson->getTblType()->getName().' '.$tblToPerson->getTblType()->getDescription() )
                            ? new PhoneMobil()
                            : new PhoneIcon()
                        )
                        .' '.$tblToPerson->getTblType()->getName().' '.$tblToPerson->getTblType()->getDescription(),
                        $Panel,
                        ( preg_match( '!Notfall!is', $tblToPerson->getTblType()->getName().' '.$tblToPerson->getTblType()->getDescription() )
                            ? Panel::PANEL_TYPE_DANGER
                            : Panel::PANEL_TYPE_WARNING
                        ),
                        new Standard(
                            '', '/People/Person/Phone/Edit', new Pencil(),
                            array( 'Id' => $tblToPerson->getId() ),
                            'Bearbeiten'
                        )
                        .new Standard(
                            '', '/People/Person/Phone/Destroy', new Remove(),
                            array( 'Id' => $tblToPerson->getId() ), 'Löschen'
                        )
                    )
                    , 3 );
            } );
        } else {
            $tblPhoneAll = array(
                new LayoutColumn(
                    new Warning( 'Keine Telefonnummern hinterlegt' )
                )
            );
        }

        $LayoutRowList = array();
        $LayoutRowCount = 0;
        $LayoutRow = null;
        /**
         * @var LayoutColumn $tblPhone
         */
        foreach ($tblPhoneAll as $tblPhone) {
            if ($LayoutRowCount % 4 == 0) {
                $LayoutRow = new LayoutRow( array() );
                $LayoutRowList[] = $LayoutRow;
            }
            $LayoutRow->addColumn( $tblPhone );
            $LayoutRowCount++;
        }

        return new Layout( new LayoutGroup( $LayoutRowList ) );
    }
}
