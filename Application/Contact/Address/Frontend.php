<?php
namespace SPHERE\Application\Contact\Address;

use SPHERE\Application\Contact\Address\Service\Entity\TblToCompany;
use SPHERE\Application\Contact\Address\Service\Entity\TblToPerson;
use SPHERE\Application\Corporation\Company\Company;
use SPHERE\Application\Corporation\Company\Service\Entity\TblCompany;
use SPHERE\Application\People\Person\Person;
use SPHERE\Application\People\Person\Service\Entity\TblPerson;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\AutoCompleter;
use SPHERE\Common\Frontend\Form\Repository\Field\SelectBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextArea;
use SPHERE\Common\Frontend\Form\Repository\Field\TextField;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\Building;
use SPHERE\Common\Frontend\Icon\Repository\ChevronLeft;
use SPHERE\Common\Frontend\Icon\Repository\Disable;
use SPHERE\Common\Frontend\Icon\Repository\Map;
use SPHERE\Common\Frontend\Icon\Repository\MapMarker;
use SPHERE\Common\Frontend\Icon\Repository\Ok;
use SPHERE\Common\Frontend\Icon\Repository\Pencil;
use SPHERE\Common\Frontend\Icon\Repository\Person as PersonIcon;
use SPHERE\Common\Frontend\Icon\Repository\Question;
use SPHERE\Common\Frontend\Icon\Repository\Remove;
use SPHERE\Common\Frontend\Icon\Repository\TileBig;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Frontend\Layout\Repository\Address as AddressLayout;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Layout\Structure\Layout;
use SPHERE\Common\Frontend\Layout\Structure\LayoutColumn;
use SPHERE\Common\Frontend\Layout\Structure\LayoutGroup;
use SPHERE\Common\Frontend\Layout\Structure\LayoutRow;
use SPHERE\Common\Frontend\Link\Repository\Standard;
use SPHERE\Common\Frontend\Message\Repository\Danger;
use SPHERE\Common\Frontend\Message\Repository\Success;
use SPHERE\Common\Frontend\Message\Repository\Warning;
use SPHERE\Common\Frontend\Text\Repository\Muted;
use SPHERE\Common\Frontend\Text\Repository\Small;
use SPHERE\Common\Window\Redirect;
use SPHERE\Common\Window\Stage;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\Contact\Address
 */
class Frontend implements IFrontendInterface
{

    /**
     * @param int   $Id
     * @param array $Street
     * @param array $City
     * @param int   $State
     * @param array $Type
     *
     * @return Stage
     */
    public function frontendCreateToPerson($Id, $Street, $City, $State, $Type)
    {

        $Stage = new Stage('Adresse', 'Hinzufügen');
        $Stage->setMessage('Eine Adresse zur gewählten Person hinzufügen');

        $tblPerson = Person::useService()->getPersonById($Id);

        $Stage->setContent(
            new Layout(array(
                new LayoutGroup(array(
                    new LayoutRow(
                        new LayoutColumn(
                            new Panel(new PersonIcon().' Person',
                                $tblPerson->getFullName(),
                                Panel::PANEL_TYPE_SUCCESS,
                                new Standard('Zurück zur Person', '/People/Person', new ChevronLeft(),
                                    array('Id' => $tblPerson->getId())
                                )
                            )
                        )
                    ),
                )),
                new LayoutGroup(array(
                    new LayoutRow(
                        new LayoutColumn(
                            Address::useService()->createAddressToPerson(
                                $this->formAddress()
                                    ->appendFormButton(new Primary('Adresse hinzufügen'))
                                    ->setConfirm('Eventuelle Änderungen wurden noch nicht gespeichert')
                                , $tblPerson, $Street, $City, $State, $Type
                            )
                        )
                    )
                )),
            ))
        );

        return $Stage;
    }

    /**
     * @return Form
     */
    private function formAddress()
    {

        $tblAddress = Address::useService()->getAddressAll();
        $tblCity = Address::useService()->getCityAll();
        $tblState = Address::useService()->getStateAll();
        $tblType = Address::useService()->getTypeAll();

        return new Form(
            new FormGroup(array(
                new FormRow(array(
                    new FormColumn(
                        new Panel('Straße', array(
                            new SelectBox('Type[Type]', 'Typ', array('{{ Name }} {{ Description }}' => $tblType),
                                new TileBig()),
                            new AutoCompleter('Street[Name]', 'Name', 'Name',
                                array('StreetName' => $tblAddress), new MapMarker()
                            ),
                            new TextField('Street[Number]', 'Hausnummer', 'Hausnummer', new MapMarker())
                        ), Panel::PANEL_TYPE_INFO)
                        , 4),
                    new FormColumn(
                        new Panel('Stadt', array(
                            new AutoCompleter('City[Code]', 'Postleitzahl', 'Postleitzahl',
                                array('Code' => $tblCity), new MapMarker()
                            ),
                            new AutoCompleter('City[Name]', 'Ort', 'Ort',
                                array('Name' => $tblCity), new MapMarker()
                            ),
                            new AutoCompleter('City[District]', 'Ortsteil', 'Ortsteil',
                                array('District' => $tblCity), new MapMarker()
                            ),
                            new SelectBox('State', 'Bundesland',
                                array('Name' => $tblState), new Map()
                            )
                        ), Panel::PANEL_TYPE_INFO)
                        , 4),
                    new FormColumn(
                        new Panel('Sonstiges', array(
                            new TextArea('Type[Remark]', 'Bemerkungen', 'Bemerkungen', new Pencil())
                        ), Panel::PANEL_TYPE_INFO)
                        , 4),
                )),
            ))
        );
    }

    /**
     * @param int   $Id
     * @param array $Street
     * @param array $City
     * @param int   $State
     * @param array $Type
     *
     * @return Stage
     */
    public function frontendCreateToCompany($Id, $Street, $City, $State, $Type)
    {

        $Stage = new Stage('Adresse', 'Hinzufügen');
        $Stage->setMessage('Eine Adresse zur gewählten Firma hinzufügen');

        $tblCompany = Company::useService()->getCompanyById($Id);

        $Stage->setContent(
            new Layout(array(
                new LayoutGroup(array(
                    new LayoutRow(
                        new LayoutColumn(
                            new Panel(new Building().' Firma',
                                $tblCompany->getName(),
                                Panel::PANEL_TYPE_SUCCESS,
                                new Standard('Zurück zur Firma', '/Corporation/Company', new ChevronLeft(),
                                    array('Id' => $tblCompany->getId())
                                )
                            )
                        )
                    ),
                )),
                new LayoutGroup(array(
                    new LayoutRow(
                        new LayoutColumn(
                            Address::useService()->createAddressToCompany(
                                $this->formAddress()
                                    ->appendFormButton(new Primary('Adresse hinzufügen'))
                                    ->setConfirm('Eventuelle Änderungen wurden noch nicht gespeichert')
                                , $tblCompany, $Street, $City, $State, $Type
                            )
                        )
                    )
                )),
            ))
        );

        return $Stage;
    }

    /**
     * @return Stage
     */
    public function frontendUpdate()
    {

        $Stage = new Stage('Adresse', 'Bearbeiten');

        $Stage->setContent(
            $this->formAddress()
                ->appendFormButton(new Primary('Änderungen speichern'))
                ->setConfirm('Eventuelle Änderungen wurden noch nicht gespeichert')
        );

        return $Stage;

    }

    /**
     * @param int  $Id
     * @param bool $Confirm
     *
     * @return Stage
     */
    public function frontendDestroyToPerson($Id, $Confirm = false)
    {

        $Stage = new Stage('Adresse', 'Löschen');
        if ($Id) {
            $tblToPerson = Address::useService()->getAddressToPersonById($Id);
            $tblPerson = $tblToPerson->getServiceTblPerson();
            if (!$Confirm) {
                $Stage->setContent(
                    new Layout(new LayoutGroup(new LayoutRow(new LayoutColumn(array(
                        new Panel(new PersonIcon().' Person',
                            $tblPerson->getFullName(),
                            Panel::PANEL_TYPE_SUCCESS,
                            new Standard('Zurück zur Person', '/People/Person', new ChevronLeft(),
                                array('Id' => $tblPerson->getId())
                            )
                        ),
                        new Panel(new Question().' Diese Adresse wirklich löschen?', array(
                            $tblToPerson->getTblType()->getName().' '.$tblToPerson->getTblType()->getDescription(),
                            new AddressLayout($tblToPerson->getTblAddress()),
                            new Muted(new Small($tblToPerson->getRemark()))
                        ),
                            Panel::PANEL_TYPE_DANGER,
                            new Standard(
                                'Ja', '/People/Person/Address/Destroy', new Ok(),
                                array('Id' => $Id, 'Confirm' => true)
                            )
                            .new Standard(
                                'Nein', '/People/Person', new Disable(),
                                array('Id' => $tblPerson->getId())
                            )
                        )
                    )))))
                );
            } else {
                $Stage->setContent(
                    new Layout(new LayoutGroup(array(
                        new LayoutRow(new LayoutColumn(array(
                            ( Address::useService()->removeAddressToPerson($tblToPerson)
                                ? new Success('Die Adresse wurde gelöscht')
                                : new Danger('Die Adresse konnte nicht gelöscht werden')
                            ),
                            new Redirect('/People/Person', 1, array('Id' => $tblPerson->getId()))
                        )))
                    )))
                );
            }
        } else {
            $Stage->setContent(
                new Layout(new LayoutGroup(array(
                    new LayoutRow(new LayoutColumn(array(
                        new Danger('Die Adresse konnte nicht gefunden werden'),
                        new Redirect('/People/Search/Group')
                    )))
                )))
            );
        }
        return $Stage;
    }

    /**
     * @param int  $Id
     * @param bool $Confirm
     *
     * @return Stage
     */
    public function frontendDestroyToCompany($Id, $Confirm = false)
    {

        $Stage = new Stage('Adresse', 'Löschen');
        if ($Id) {
            $tblToCompany = Address::useService()->getAddressToCompanyById($Id);
            $tblCompany = $tblToCompany->getServiceTblCompany();
            if (!$Confirm) {
                $Stage->setContent(
                    new Layout(new LayoutGroup(new LayoutRow(new LayoutColumn(array(
                        new Panel(new Building().' Company',
                            $tblCompany->getName(),
                            Panel::PANEL_TYPE_SUCCESS,
                            new Standard('Zurück zur Firma', '/Corporation/Company', new ChevronLeft(),
                                array('Id' => $tblCompany->getId())
                            )
                        ),
                        new Panel(new Question().' Diese Adresse wirklich löschen?', array(
                            $tblToCompany->getTblType()->getName().' '.$tblToCompany->getTblType()->getDescription(),
                            new AddressLayout($tblToCompany->getTblAddress()),
                            new Muted(new Small($tblToCompany->getRemark()))
                        ),
                            Panel::PANEL_TYPE_DANGER,
                            new Standard(
                                'Ja', '/Corporation/Company/Address/Destroy', new Ok(),
                                array('Id' => $Id, 'Confirm' => true)
                            )
                            .new Standard(
                                'Nein', '/Corporation/Company', new Disable(),
                                array('Id' => $tblCompany->getId())
                            )
                        )
                    )))))
                );
            } else {
                $Stage->setContent(
                    new Layout(new LayoutGroup(array(
                        new LayoutRow(new LayoutColumn(array(
                            ( Address::useService()->removeAddressToCompany($tblToCompany)
                                ? new Success('Die Adresse wurde gelöscht')
                                : new Danger('Die Adresse konnte nicht gelöscht werden')
                            ),
                            new Redirect('/Corporation/Company', 1, array('Id' => $tblCompany->getId()))
                        )))
                    )))
                );
            }
        } else {
            $Stage->setContent(
                new Layout(new LayoutGroup(array(
                    new LayoutRow(new LayoutColumn(array(
                        new Danger('Die Adresse konnte nicht gefunden werden'),
                        new Redirect('/Corporation/Search/Group')
                    )))
                )))
            );
        }
        return $Stage;
    }

    /**
     * @param TblPerson $tblPerson
     *
     * @return Layout
     */
    public function frontendLayoutPerson(TblPerson $tblPerson)
    {

        $tblAddressAll = Address::useService()->getAddressAllByPerson($tblPerson);
        if ($tblAddressAll !== false) {
            array_walk($tblAddressAll, function (TblToPerson &$tblToPerson) {

                $Panel = array($tblToPerson->getTblAddress()->getLayout());
                if ($tblToPerson->getRemark()) {
                    array_push($Panel, new Muted(new Small($tblToPerson->getRemark())));
                }

                $tblToPerson = new LayoutColumn(
                    new Panel(
                        new MapMarker().' '.$tblToPerson->getTblType()->getName(), $Panel, Panel::PANEL_TYPE_SUCCESS,
                        new Standard(
                            '', '/People/Person/Address/Edit', new Pencil(),
                            array('Id' => $tblToPerson->getId()),
                            'Bearbeiten'
                        )
                        .new Standard(
                            '', '/People/Person/Address/Destroy', new Remove(),
                            array('Id' => $tblToPerson->getId()), 'Löschen'
                        )
                    )
                    , 3);
            });
        } else {
            $tblAddressAll = array(
                new LayoutColumn(
                    new Warning('Keine Adressen hinterlegt')
                )
            );
        }

        $LayoutRowList = array();
        $LayoutRowCount = 0;
        $LayoutRow = null;
        /**
         * @var LayoutColumn $tblAddress
         */
        foreach ($tblAddressAll as $tblAddress) {
            if ($LayoutRowCount % 4 == 0) {
                $LayoutRow = new LayoutRow(array());
                $LayoutRowList[] = $LayoutRow;
            }
            $LayoutRow->addColumn($tblAddress);
            $LayoutRowCount++;
        }

        return new Layout(new LayoutGroup($LayoutRowList));
    }

    /**
     * @param TblCompany $tblCompany
     *
     * @return Layout
     */
    public function frontendLayoutCompany(TblCompany $tblCompany)
    {

        $tblAddressAll = Address::useService()->getAddressAllByCompany($tblCompany);
        if ($tblAddressAll !== false) {
            array_walk($tblAddressAll, function (TblToCompany &$tblToCompany) {

                $Panel = array($tblToCompany->getTblAddress()->getLayout());
                if ($tblToCompany->getRemark()) {
                    array_push($Panel, new Muted(new Small($tblToCompany->getRemark())));
                }

                $tblToCompany = new LayoutColumn(
                    new Panel(
                        new MapMarker().' '.$tblToCompany->getTblType()->getName(), $Panel, Panel::PANEL_TYPE_SUCCESS,
                        new Standard(
                            '', '/Corporation/Company/Address/Edit', new Pencil(),
                            array('Id' => $tblToCompany->getId()),
                            'Bearbeiten'
                        )
                        .new Standard(
                            '', '/Corporation/Company/Address/Destroy', new Remove(),
                            array('Id' => $tblToCompany->getId()), 'Löschen'
                        )
                    )
                    , 3);
            });
        } else {
            $tblAddressAll = array(
                new LayoutColumn(
                    new Warning('Keine Adressen hinterlegt')
                )
            );
        }

        $LayoutRowList = array();
        $LayoutRowCount = 0;
        $LayoutRow = null;
        /**
         * @var LayoutColumn $tblAddress
         */
        foreach ($tblAddressAll as $tblAddress) {
            if ($LayoutRowCount % 4 == 0) {
                $LayoutRow = new LayoutRow(array());
                $LayoutRowList[] = $LayoutRow;
            }
            $LayoutRow->addColumn($tblAddress);
            $LayoutRowCount++;
        }

        return new Layout(new LayoutGroup($LayoutRowList));
    }
}
