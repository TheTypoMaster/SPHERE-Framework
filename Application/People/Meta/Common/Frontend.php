<?php
namespace SPHERE\Application\People\Meta\Common;

use SPHERE\Application\People\Meta\Common\Service\Entity\TblCommon;
use SPHERE\Application\People\Meta\Common\Service\Entity\TblCommonBirthDates;
use SPHERE\Application\People\Meta\Common\Service\Entity\TblCommonInformation;
use SPHERE\Application\People\Person\Service\Entity\TblPerson;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\AutoCompleter;
use SPHERE\Common\Frontend\Form\Repository\Field\DatePicker;
use SPHERE\Common\Frontend\Form\Repository\Field\SelectBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextArea;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\Calendar;
use SPHERE\Common\Frontend\Icon\Repository\Child;
use SPHERE\Common\Frontend\Icon\Repository\Info;
use SPHERE\Common\Frontend\Icon\Repository\MapMarker;
use SPHERE\Common\Frontend\Icon\Repository\Nameplate;
use SPHERE\Common\Frontend\Icon\Repository\Pencil;
use SPHERE\Common\Frontend\Icon\Repository\Sheriff;
use SPHERE\Common\Frontend\Icon\Repository\TempleChurch;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Text\Repository\Danger;
use SPHERE\Common\Window\Stage;
use SPHERE\System\Extension\Extension;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\People\Meta\Common
 */
class Frontend extends Extension implements IFrontendInterface
{

    /**
     * @param TblPerson $tblPerson
     * @param array     $Meta
     *
     * @return Stage
     */
    public function frontendMeta(TblPerson $tblPerson = null, $Meta = array())
    {

        $Stage = new Stage();

        $Stage->setMessage(
            new Danger(
                new Info().' Es dürfen ausschließlich für die Schulverwaltung notwendige Informationen gespeichert werden.'
            )
        );

        if (null !== $tblPerson) {
            $Global = $this->getGlobal();
            if (!isset( $Global->POST['Meta'] )) {
                /** @var TblCommon $tblCommon */
                $tblCommon = Common::useService()->getCommonByPerson($tblPerson);
                if ($tblCommon) {
                    $Global->POST['Meta']['Remark'] = $tblCommon->getRemark();

                    /** @var TblCommonBirthDates $tblCommonBirthDates */
                    $tblCommonBirthDates = $tblCommon->getTblCommonBirthDates();
                    if ($tblCommonBirthDates) {
                        $Global->POST['Meta']['BirthDates']['Birthday'] = $tblCommonBirthDates->getBirthday();
                        $Global->POST['Meta']['BirthDates']['Birthplace'] = $tblCommonBirthDates->getBirthplace();
                        $Global->POST['Meta']['BirthDates']['Gender'] = $tblCommonBirthDates->getGender();
                        $Global->POST['Meta']['BirthDates']['Nationality'] = $tblCommonBirthDates->getNationality();
                    }
                    /** @var TblCommonInformation $tblCommonInformation */
                    $tblCommonInformation = $tblCommon->getTblCommonInformation();
                    if ($tblCommonInformation) {
                        $Global->POST['Meta']['Title'] = $tblCommonInformation->getTitle();
                        $Global->POST['Meta']['First'] = $tblCommonInformation->getFirstName();
                        $Global->POST['Meta']['Middle'] = $tblPerson->getSecondName();
                        $Global->POST['Meta']['Last'] = $tblPerson->getLastName();
                        $Global->POST['Meta']['Type'] = $tblPerson->getTblPersonType()->getId();
                        $Global->POST['Meta']['Remark'] = $tblPerson->getRemark();
                        $Global->POST['Meta']['Denomination'] = $tblPerson->getDenomination();
                    }
                    $Global->savePost();
                }
            }
        }

        $Stage->setContent(
            (new Form(array(
                new FormGroup(array(
                    new FormRow(array(
                        new FormColumn(
                            new Panel('Geburtsdaten', array(
                                new DatePicker('Meta[BirthDates][Birthday]', 'Geburtstag', 'Geburtstag',
                                    new Calendar()),
                                new AutoCompleter('Meta[BirthDates][Birthplace]', 'Geburtsort', 'Geburtsort', array(),
                                    new MapMarker()),
                                new SelectBox('Meta[BirthDates][Gender]', 'Geschlecht', array(
                                    TblCommonBirthDates::VALUE_GENDER_NULL   => '',
                                    TblCommonBirthDates::VALUE_GENDER_MALE   => 'Männlich',
                                    TblCommonBirthDates::VALUE_GENDER_FEMALE => 'Weiblich'
                                ), new Child()),
                                new AutoCompleter('Meta[BirthDates][Nationality]', 'Staatsangehörigkeit',
                                    'Staatsangehörigkeit',
                                    array(), new Nameplate()
                                ),
                            ), Panel::PANEL_TYPE_INFO), 6),
                        new FormColumn(
                            new Panel('Informationen', array(
                                new AutoCompleter('Meta[Information][Denomination]', 'Konfession',
                                    'Konfession',
                                    array(), new TempleChurch()
                                ),
                                new SelectBox('Meta[Information][IsAssistance]', 'Mitarbeitsbereitschaft', array(
                                    TblCommonInformation::VALUE_IS_ASSISTANCE_NULL => '',
                                    TblCommonInformation::VALUE_IS_ASSISTANCE_YES  => 'Ja',
                                    TblCommonInformation::VALUE_IS_ASSISTANCE_NO   => 'Nein'
                                ), new Sheriff()
                                ),
                                new Danger(new Info().' Es dürfen ausschließlich für die Schulverwaltung notwendige Informationen gespeichert werden.'),
                                new TextArea('Meta[Information][AssistanceActivity]',
                                    'Mitarbeitsbereitschaft - Tätigkeiten',
                                    'Mitarbeitsbereitschaft - Tätigkeiten', new Pencil()
                                ),
                            ), Panel::PANEL_TYPE_INFO), 6),
                    )),
                )),
                new FormGroup(array(
                    new FormRow(array(
                        new FormColumn(
                            new Panel('Sonstiges', array(
                                new Danger(new Info().' Es dürfen ausschließlich für die Schulverwaltung notwendige Informationen gespeichert werden.'),
                                new TextArea('Meta[Remark]', 'Bemerkungen', 'Bemerkungen', new Pencil())
                            ), Panel::PANEL_TYPE_INFO)),
                    )),
                )),
            ),
                new Primary('Informationen speichern')))->setConfirm('Eventuelle Änderungen wurden noch nicht gespeichert.')
        );

        return $Stage;
    }
}
