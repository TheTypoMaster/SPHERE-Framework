<?php

namespace SPHERE\Application\Billing\Inventory\Commodity;

use SPHERE\Application\Billing\Accounting\Account\Account;
use SPHERE\Application\Billing\Accounting\Account\Service\Entity\TblAccount;
use SPHERE\Application\Billing\Inventory\Commodity\Service\Entity\TblCommodity;
use SPHERE\Application\Billing\Inventory\Commodity\Service\Entity\TblCommodityItem;
use SPHERE\Application\Billing\Inventory\Item\Item;
use SPHERE\Application\Billing\Inventory\Item\Service\Entity\TblItem;
use SPHERE\Application\Billing\Inventory\Item\Service\Entity\TblItemAccount;
use SPHERE\Common\Frontend\Form\Repository\Field\SelectBox;
use SPHERE\Common\Frontend\Form\Repository\Field\TextField;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\ChevronLeft;
use SPHERE\Common\Frontend\Icon\Repository\Conversation;
use SPHERE\Common\Frontend\Icon\Repository\Edit;
use SPHERE\Common\Frontend\Icon\Repository\Listing;
use SPHERE\Common\Frontend\Icon\Repository\Minus;
use SPHERE\Common\Frontend\Icon\Repository\Plus;
use SPHERE\Common\Frontend\Icon\Repository\Quantity;
use SPHERE\Common\Frontend\Icon\Repository\Remove;
use SPHERE\Common\Frontend\IFrontendInterface;
use SPHERE\Common\Frontend\Layout\Repository\Panel;
use SPHERE\Common\Frontend\Layout\Repository\Title;
use SPHERE\Common\Frontend\Layout\Structure\Layout;
use SPHERE\Common\Frontend\Layout\Structure\LayoutColumn;
use SPHERE\Common\Frontend\Layout\Structure\LayoutGroup;
use SPHERE\Common\Frontend\Layout\Structure\LayoutRow;
use SPHERE\Common\Frontend\Link\Repository\Danger;
use SPHERE\Common\Frontend\Link\Repository\Primary;
use SPHERE\Common\Frontend\Message\Repository\Warning;
use SPHERE\Common\Frontend\Table\Structure\TableData;
use SPHERE\Common\Window\Stage;
use SPHERE\System\Extension\Extension;

class Frontend extends Extension implements IFrontendInterface
{

    /**
     * @return Stage
     */
    public function frontendStatus()
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistungen');
        $Stage->setDescription('Übersicht');
        // ToDo
        $Stage->setMessage('Zeigt die verfügbaren Leistungen an. <br />
                            Leistungen sind Zusammenfassungen aller Artikel,
                            die unter einem Punkt für den Debitor abgerechnet werden. <br />
                            Beispielsweise: Schulgeld, Hortgeld, Klassenfahrt usw.');
        $Stage->addButton(
            new Primary('Leistung anlegen', '/Billing/Inventory/Commodity/Create', new Plus())
        );

        $tblCommodityAll = Commodity::useService()->entityCommodityAll();

        if (!empty( $tblCommodityAll )) {
            array_walk($tblCommodityAll, function (TblCommodity $tblCommodity) {

                $tblCommodity->Type = $tblCommodity->getTblCommodityType()->getName();
                $tblCommodity->ItemCount = Commodity::useService()->countItemAllByCommodity($tblCommodity);
                $tblCommodity->SumPriceItem = Commodity::useService()->sumPriceItemAllByCommodity($tblCommodity);
                $tblCommodity->Option =
                    (new Primary('Bearbeiten', '/Billing/Inventory/Commodity/Edit',
                        new Edit(), array(
                            'Id' => $tblCommodity->getId()
                        )))->__toString().
                    (new Primary('Artikel auswählen', '/Billing/Inventory/Commodity/Item/Select',
                        new Listing(), array(
                            'Id' => $tblCommodity->getId()
                        )))->__toString().
                    (new Danger('Löschen', '/Billing/Inventory/Commodity/Delete',
                        new Remove(), array(
                            'Id' => $tblCommodity->getId()
                        )))->__toString();
            });
        }

        $Stage->setContent(
            new TableData($tblCommodityAll, null,
                array(
                    'Name'         => 'Name',
                    'Description'  => 'Beschreibung',
                    'Type'         => 'Leistungsart',
                    'ItemCount'    => 'Artikelanzahl',
                    'SumPriceItem' => 'Gesamtpreis',
                    'Option'       => 'Option'
                )
            )
        );

        return $Stage;
    }

    /**
     * @param $Commodity
     *
     * @return Stage
     */
    public function frontendCreate($Commodity)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistung');
        $Stage->setDescription('Hinzufügen');
        $Stage->setMessage(
            '<b>Hinweis:</b> <br>
            Bei einer Einzelleistung wird für jede Person der gesamten Betrag berechnet. <br>
            Hingegen bei einer Sammelleisung bezahlt jede Person einen Teil des gesamten Betrags, abhängig von der
            Personenanzahl. <br>
            (z.B.: für Klassenfahrten)
        ');
        $Stage->addButton(new Primary('Zurück', '/Billing/Inventory/Commodity',
            new ChevronLeft()
        ));

        $Stage->setContent(Commodity::useService()->executeCreateCommodity(
            new Form(array(
                new FormGroup(array(
                    new FormRow(array(
                        new FormColumn(
                            new TextField('Commodity[Name]', 'Name', 'Name', new Conversation()
                            ), 6),
                        new FormColumn(
                            new SelectBox('Commodity[Type]', 'Leistungsart', array(
                                'Name' => Commodity::useService()->entityCommodityTypeAll()
                            ))
                            , 6)
                    )),
                    new FormRow(array(
                        new FormColumn(
                            new TextField('Commodity[Description]', 'Beschreibung', 'Beschreibung', new Conversation()
                            ), 12)
                    ))
                ))
            ), new \SPHERE\Common\Frontend\Form\Repository\Button\Primary('Hinzufügen')), $Commodity));

        return $Stage;
    }

    /**
     * @param $Id
     *
     * @return Stage
     */
    public function frontendDelete($Id)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistung');
        $Stage->setDescription('Entfernen');

        $tblCommodity = Commodity::useService()->entityCommodityById($Id);
        $Stage->setContent(Commodity::useService()->executeRemoveCommodity($tblCommodity));

        return $Stage;
    }


    /**
     * @param $Id
     * @param $Commodity
     *
     * @return Stage
     */
    public function frontendEdit($Id, $Commodity)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistungen');
        $Stage->setDescription('Bearbeiten');
        $Stage->setMessage(
            '<b>Hinweis:</b> <br>
            Bei einer Einzelleistung wird für jede Person der gesamten Betrag berechnet. <br>
            Hingegen bei einer Sammelleisung bezahlt jede Person einen Teil des gesamten Betrags, abhängig von der
            Personenanzahl. <br>
            (z.B.: für Klassenfahrten)
        ');
        $Stage->addButton(new Primary('Zurück', '/Billing/Inventory/Commodity',
            new ChevronLeft()
        ));

        if (empty( $Id )) {
            $Stage->setContent(new Warning('Die Daten konnten nicht abgerufen werden'));
        } else {
            $tblCommodity = Commodity::useService()->entityCommodityById($Id);
            if (empty( $tblCommodity )) {
                $Stage->setContent(new Warning('Die Leistung konnte nicht abgerufen werden'));
            } else {

                $Global = $this->getGlobal();
                if (!isset( $Global->POST['Commodity'] )) {
                    $Global->POST['Commodity']['Name'] = $tblCommodity->getName();
                    $Global->POST['Commodity']['Description'] = $tblCommodity->getDescription();
                    $Global->POST['Commodity']['Type'] = $tblCommodity->getTblCommodityType()->getId();
                    $Global->savePost();
                }

                $Stage->setContent(Commodity::useService()->executeEditCommodity(
                    new Form(array(
                        new FormGroup(array(
                            new FormRow(array(
                                new FormColumn(
                                    new TextField('Commodity[Name]', 'Name', 'Name', new Conversation()
                                    ), 6),
                                new FormColumn(
                                    new SelectBox('Commodity[Type]', 'Leistungsart', array(
                                        'Name' => Commodity::useService()->entityCommodityTypeAll()
                                    ))
                                    , 6)
                            )),
                            new FormRow(array(
                                new FormColumn(
                                    new TextField('Commodity[Description]', 'Beschreibung', 'Beschreibung',
                                        new Conversation()
                                    ), 12)
                            ))
                        ))
                    ), new \SPHERE\Common\Frontend\Form\Repository\Button\Primary('Änderungen speichern')
                    ), $tblCommodity, $Commodity));
            }
        }

        return $Stage;
    }

    /**
     * @param $tblCommodityId
     * @param $tblItemId
     * @param $Item
     *
     * @return Stage
     */
    public function frontendItemAdd($tblCommodityId, $tblItemId, $Item)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistung');
        $Stage->setDescription('Artikel Hinzufügen');
        $tblCommodity = Commodity::useService()->entityCommodityById($tblCommodityId);
        $tblItem = Item::useService()->entityItemById($tblItemId);

        if (!empty( $tblCommodityId ) && !empty( $tblItemId )) {
            $Stage->setContent(Commodity::useService()->executeAddCommodityItem($tblCommodity, $tblItem, $Item));
        }

        return $Stage;
    }

    /**
     * @param $Id
     *
     * @return Stage
     */
    public function frontendItemAccountSelect($Id)
    {

        $Stage = new Stage();
        $Stage->setTitle('Artikel');
        $Stage->setDescription('FIBU-Konten auswählen');
        $Stage->addButton(new Primary('Zurück', '/Billing/Inventory/Commodity/Item',
            new ChevronLeft()
        ));

        if (empty( $Id )) {
            $Stage->setContent(new Warning('Die Daten konnten nicht abgerufen werden'));
        } else {
            $tblItem = Item::useService()->entityItemById($Id);
            if (empty( $tblItem )) {
                $Stage->setContent(new Warning('Der Artikel konnte nicht abgerufen werden'));
            } else {
                $tblItemAccountByItem = Commodity::useService()->entityItemAccountAllByItem($tblItem);
                $tblAccountByItem = Commodity::useService()->entityAccountAllByItem($tblItem);
                $tblAccountAllByActiveState = Account::useService()->entityAccountAllByActiveState();

                if (!empty( $tblAccountAllByActiveState )) {
                    $tblAccountAllByActiveState = array_udiff($tblAccountAllByActiveState, $tblAccountByItem,
                        function (TblAccount $ObjectA, TblAccount $ObjectB) {

                            return $ObjectA->getId() - $ObjectB->getId();
                        }
                    );
                }

                if (!empty( $tblItemAccountByItem )) {
                    array_walk($tblItemAccountByItem, function (TblItemAccount $tblItemAccountByItem) {

                        $tblItemAccountByItem->Number = $tblItemAccountByItem->getServiceBilling_Account()->getNumber();
                        $tblItemAccountByItem->Description = $tblItemAccountByItem->getServiceBilling_Account()->getDescription();
                        $tblItemAccountByItem->Option =
                            new Danger('Entfernen', '/Billing/Inventory/Commodity/Item/Account/Remove',
                                new Minus(), array(
                                    'Id' => $tblItemAccountByItem->getId()
                                ));
                    });
                }

                if (!empty( $tblAccountAllByActiveState )) {
                    /** @noinspection PhpUnusedParameterInspection */
                    array_walk($tblAccountAllByActiveState,
                        function (TblAccount $tblAccountAllByActiveState, $Index, TblItem $tblItem) {

                            $tblAccountAllByActiveState->Option =
                                new Primary('Hinzufügen', '/Billing/Inventory/Commodity/Item/Account/Add',
                                    new Plus(), array(
                                        'tblAccountId' => $tblAccountAllByActiveState->getId(),
                                        'tblItemId'    => $tblItem->getId()
                                    ));
                        }, $tblItem);
                }

                $Stage->setContent(
                    new Layout(array(
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(
                                    new Panel('Name', $tblItem->getName(), Panel::PANEL_TYPE_SUCCESS), 4
                                ),
                                new LayoutColumn(
                                    new Panel('Beschreibung', $tblItem->getDescription(), Panel::PANEL_TYPE_SUCCESS), 8
                                )
                            )),
                        )),
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(array(
                                        new TableData($tblItemAccountByItem, null,
                                            array(
                                                'Number'      => 'Nummer',
                                                'Description' => 'Beschreibung',
                                                'Option'      => 'Option'
                                            )
                                        )
                                    )
                                )
                            )),
                        ), new Title('zugewiesene FIBU-Konten')),
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(array(
                                        new TableData($tblAccountAllByActiveState, null,
                                            array(
                                                'Number'      => 'Nummer',
                                                'Description' => 'Beschreibung',
                                                'Option'      => 'Option '
                                            )
                                        )
                                    )
                                )
                            )),
                        ), new Title('mögliche FIBU-Konten'))
                    ))
                );
            }
        }

        return $Stage;
    }

    /**
     * @param $Id
     *
     * @return Stage
     */
    public function frontendItemSelect($Id)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistung');
        $Stage->setDescription('Artikel auswählen');
        $Stage->addButton(new Primary('Zurück', '/Billing/Inventory/Item',
            new ChevronLeft()
        ));

        if (empty( $Id )) {
            $Stage->setContent(new Warning('Die Daten konnten nicht abgerufen werden'));
        } else {
            $tblCommodity = Commodity::useService()->entityCommodityById($Id);
            if (empty( $tblCommodity )) {
                $Stage->setContent(new Warning('Die Leistung konnte nicht abgerufen werden'));
            } else {
                $tblCommodityItem = Commodity::useService()->entityCommodityItemAllByCommodity($tblCommodity);
                $tblItemAllByCommodity = Commodity::useService()->entityItemAllByCommodity($tblCommodity);
                $tblItemAll = Item::useService()->entityItemAll();

                if (!empty( $tblItemAllByCommodity )) {
                    $tblItemAll = array_udiff($tblItemAll, $tblItemAllByCommodity,
                        function (TblItem $ObjectA, TblItem $ObjectB) {

                            return $ObjectA->getId() - $ObjectB->getId();
                        }
                    );
                }

                if (!empty( $tblCommodityItem )) {
                    array_walk($tblCommodityItem, function (TblCommodityItem $tblCommodityItem) {

                        $tblItem = $tblCommodityItem->getTblItem();

                        $tblCommodityItem->Name = $tblItem->getName();
                        $tblCommodityItem->Description = $tblItem->getDescription();
                        $tblCommodityItem->PriceString = $tblItem->getPriceString();
                        $tblCommodityItem->TotalPriceString = $tblCommodityItem->getTotalPriceString();
                        $tblCommodityItem->QuantityString = str_replace('.', ',', $tblCommodityItem->getQuantity());
                        $tblCommodityItem->CostUnit = $tblItem->getCostUnit();
                        $tblCommodityItem->Option =
                            (new Danger('Entfernen', '/Billing/Inventory/Commodity/Item/Remove',
                                new Minus(), array(
                                    'Id' => $tblCommodityItem->getId()
                                )))->__toString();
                    });
                }

                if (!empty( $tblItemAll )) {
                    /** @var TblItem $tblItem */
                    foreach ($tblItemAll as $tblItem) {
                        $tblItem->PriceString = $tblItem->getPriceString();
                        $tblItem->Option =
                            (new Form(
                                new FormGroup(
                                    new FormRow(array(
                                        new FormColumn(
                                            new TextField('Item[Quantity]', 'Menge', '', new Quantity()
                                            )
                                            , 7),
                                        new FormColumn(
                                            new \SPHERE\Common\Frontend\Form\Repository\Button\Primary('Hinzufügen',
                                                new Plus())
                                            , 5)
                                    ))
                                ), null,
                                '/Billing/Inventory/Commodity/Item/Add', array(
                                    'tblCommodityId' => $tblCommodity->getId(),
                                    'tblItemId'      => $tblItem->getId()
                                )
                            ))->__toString();
                    }
                }

                $Stage->setContent(
                    new Layout(array(
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(
                                    new Panel('Name', $tblCommodity->getName(), Panel::PANEL_TYPE_SUCCESS), 4
                                ),
                                new LayoutColumn(
                                    new Panel('Beschreibung', $tblCommodity->getDescription(),
                                        Panel::PANEL_TYPE_SUCCESS), 8
                                )
                            ))
                        )),
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(array(
                                        new TableData($tblCommodityItem, null,
                                            array(
                                                'Name'             => 'Name',
                                                'Description'      => 'Beschreibung',
                                                'CostUnit'         => 'Kostenstelle',
                                                'PriceString'      => 'Preis',
                                                'QuantityString'   => 'Menge',
                                                'TotalPriceString' => 'Gesamtpreis',
                                                'Option'           => 'Option'
                                            )
                                        )
                                    )
                                )
                            )),
                        ), new Title('vorhandene Artikel')),
                        new LayoutGroup(array(
                            new LayoutRow(array(
                                new LayoutColumn(array(
                                        new TableData($tblItemAll, null,
                                            array(
                                                'Name'        => 'Name',
                                                'Description' => 'Beschreibung',
                                                'CostUnit'    => 'Kostenstelle',
                                                'PriceString' => 'Preis',
                                                'Option'      => 'Option'
                                            )
                                        )
                                    )
                                )
                            )),
                        ), new Title('mögliche Artikel'))
                    ))
                );
            }
        }

        return $Stage;
    }

    /**
     * @param $Id
     *
     * @return Stage
     */
    public function frontendItemRemove($Id)
    {

        $Stage = new Stage();
        $Stage->setTitle('Leistung');
        $Stage->setDescription('Artikel Entfernen');
        $tblCommodityItem = Commodity::useService()->entityCommodityItemById($Id);
        if (!empty( $tblCommodityItem )) {
            $Stage->setContent(Commodity::useService()->executeRemoveCommodityItem($tblCommodityItem));
        }

        return $Stage;
    }

    /**
     * @param $Id
     *
     * @return Stage
     */
    public function frontendItemAccountRemove($Id)
    {

        $Stage = new Stage();
        $Stage->setTitle('Artikel');
        $Stage->setDescription('FIBU-Konto Entfernen');
        $tblItemAccount = Item::useService()->entityItemAccountById($Id);
        if (!empty( $tblItemAccount )) {
            $Stage->setContent(Item::useService()->executeRemoveItemAccount($tblItemAccount));
        }

        return $Stage;
    }

    /**
     * @param $tblItemId
     * @param $tblAccountId
     *
     * @return Stage
     */
    public function frontendItemAccountAdd($tblItemId, $tblAccountId)
    {

        $Stage = new Stage();
        $Stage->setTitle('Artikel');
        $Stage->setDescription('FIBU-Konto Hinzufügen');
        $tblItem = Item::useService()->entityItemById($tblItemId);
        $tblAccount = Account::useService()->entityAccountById($tblAccountId);

        if (!empty( $tblItemId ) && !empty( $tblAccountId )) {
            $Stage->setContent(Item::useService()->executeAddItemAccount($tblItem, $tblAccount));
        }

        return $Stage;
    }
}
