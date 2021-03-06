<?php
namespace SPHERE\Application\Platform\Assistance\Support;

use SPHERE\Common\Frontend\Form\IFormInterface;
use SPHERE\Common\Frontend\Message\IMessageInterface;
use SPHERE\Common\Frontend\Message\Repository\Danger;
use SPHERE\Common\Frontend\Message\Repository\Success;
use SPHERE\System\Support\Support;
use SPHERE\System\Support\Type\YouTrack;

/**
 * Class Service
 *
 * @package SPHERE\Application\System\Assistance\Support\Ticket
 */
class Service
{

    /**
     * @param IFormInterface $Form
     * @param null|string    $TicketSubject
     * @param null|string    $TicketMessage
     *
     * @return IMessageInterface|IFormInterface
     */
    public function executeCreateTicket(IFormInterface &$Form, $TicketSubject, $TicketMessage)
    {

        $Error = false;
        if (empty( $TicketSubject ) && null !== $TicketSubject) {
            $Form->setError('TicketSubject', 'Bitte geben Sie ein Thema ein');
            $Error = true;
        } elseif (null === $TicketSubject) {
            $Error = true;
        } else {
            $Form->setSuccess('TicketSubject', '');
        }
        if (empty( $TicketMessage ) && null !== $TicketMessage) {
            $Form->setError('TicketMessage', 'Bitte geben Sie ein Mitteilung ein');
            $Error = true;
        } elseif (null === $TicketMessage) {
            $Error = true;
        } else {
            $Form->setSuccess('TicketMessage', '');
        }

        if ($Error) {
            /**
             * Nothing to do
             */
            try {
                $Youtrack = (new Support(new YouTrack()))->getSupport();
                $Form->prependGridGroup($Youtrack->ticketCurrent());
                return $Form;
            } catch (\Exception $E) {
                return new Danger('Das Support-System konnte nicht geladen werden');
            }
        } else {
            /**
             * Submit Ticket
             */
            try {
                $Youtrack = (new Support(new YouTrack()))->getSupport();
                $Youtrack->createTicket(urldecode($TicketSubject), urldecode($TicketMessage));
                return new Success('Das Problem wurde erfolgreich dem Support mitgeteilt');
            } catch (\Exception $E) {
                return new Danger('Das Problem konnte nicht übertragen werden');
            }
        }
    }
}
