<?php
namespace SPHERE\Application\System\Gatekeeper\Authentication\Identification;

use SPHERE\Application\System\Gatekeeper\Account\Account;
use SPHERE\Common\Frontend\Form\Repository\Button\Primary;
use SPHERE\Common\Frontend\Form\Repository\Field\PasswordField;
use SPHERE\Common\Frontend\Form\Repository\Field\TextField;
use SPHERE\Common\Frontend\Form\Structure\Form;
use SPHERE\Common\Frontend\Form\Structure\FormColumn;
use SPHERE\Common\Frontend\Form\Structure\FormGroup;
use SPHERE\Common\Frontend\Form\Structure\FormRow;
use SPHERE\Common\Frontend\Icon\Repository\Lock;
use SPHERE\Common\Frontend\Icon\Repository\Person;
use SPHERE\Common\Frontend\Icon\Repository\YubiKey;
use SPHERE\Common\Frontend\Link\Repository\Primary as PrimaryLink;
use SPHERE\Common\Window\Redirect;
use SPHERE\Common\Window\Stage;

/**
 * Class Frontend
 *
 * @package SPHERE\Application\System\Gatekeeper\Account
 */
class Frontend
{

    /**
     * @return Stage
     */
    public function frontendIdentification()
    {

        $View = new Stage( 'Anmeldung', 'Bitte wählen Sie den Typ der Anmeldung' );
        $View->setContent(
            new PrimaryLink( 'Schüler', '/System/Gatekeeper/Authentication/Identification/Student', new Lock() )
            .new PrimaryLink( 'Lehrer', '/System/Gatekeeper/Authentication/Identification/Teacher', new Lock() )
            .new PrimaryLink( 'Verwaltung', '/System/Gatekeeper/Authentication/Identification/Management', new Lock() )
            .new PrimaryLink( 'System', '/System/Gatekeeper/Authentication/Identification/System', new Lock() )
        );
        return $View;
    }

    /**
     * @param string $CredentialName
     * @param string $CredentialLock
     * @param string $CredentialKey
     *
     * @return Stage
     */
    public function frontendcreatesessionTeacher( $CredentialName, $CredentialLock, $CredentialKey )
    {

        $View = new Stage( 'Anmeldung', 'Lehrer' );
        $View->setMessage( 'Bitte geben Sie Ihre Benutzerdaten ein' );
        $View->setContent( Account::useService()->createSessionCredentialToken(
            new Form(
                new FormGroup( array(
                        new FormRow(
                            new FormColumn( new TextField( 'CredentialName', 'Benutzername', '', new Person() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialLock', 'Passwort', '', new Lock() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialKey', 'YubiKey', '', new YubiKey() ) )
                        )
                    )
                ), new Primary( 'Anmelden' )
            ),
            $CredentialName, $CredentialLock, $CredentialKey,
            Account::useService()->getIdentificationByName( 'Teacher' )
        ) );
        return $View;
    }

    /**
     * @param string $CredentialName
     * @param string $CredentialLock
     * @param string $CredentialKey
     *
     * @return Stage
     */
    public function frontendcreatesessionSystem( $CredentialName, $CredentialLock, $CredentialKey )
    {

        $View = new Stage( 'Anmeldung', 'System' );
        $View->setMessage( 'Bitte geben Sie Ihre Benutzerdaten ein' );
        $View->setContent( Account::useService()->createSessionCredentialToken(
            new Form(
                new FormGroup( array(
                        new FormRow(
                            new FormColumn( new TextField( 'CredentialName', 'Benutzername', '', new Person() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialLock', 'Passwort', '', new Lock() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialKey', 'YubiKey', '', new YubiKey() ) )
                        )
                    )
                ), new Primary( 'Anmelden' )
            ),
            $CredentialName, $CredentialLock, $CredentialKey,
            Account::useService()->getIdentificationByName( 'System' )
        ) );
        return $View;
    }

    /**
     * @param string $CredentialName
     * @param string $CredentialLock
     *
     * @return Stage
     */
    public function frontendcreatesessionStudent( $CredentialName, $CredentialLock )
    {

        $View = new Stage( 'Anmeldung', 'Schüler' );
        $View->setMessage( 'Bitte geben Sie Ihre Benutzerdaten ein' );
        $View->setContent( Account::useService()->createSessionCredential(
            new Form(
                new FormGroup( array(
                        new FormRow(
                            new FormColumn( new TextField( 'CredentialName', 'Benutzername', 'Benutzername',
                                new Person() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialLock', 'Passwort', 'Passwort',
                                new Lock() ) )
                        )
                    )
                ), new Primary( 'Anmelden' )
            ),
            $CredentialName, $CredentialLock,
            Account::useService()->getIdentificationByName( 'Student' )
        ) );
        return $View;
    }

    /**
     * @param string $CredentialName
     * @param string $CredentialLock
     * @param string $CredentialKey
     *
     * @return Stage
     */
    public function frontendcreatesessionManagement( $CredentialName, $CredentialLock, $CredentialKey )
    {

        $View = new Stage( 'Anmeldung', 'Verwaltung' );
        $View->setMessage( 'Bitte geben Sie Ihre Benutzerdaten ein' );
        $View->setContent( Account::useService()->createSessionCredentialToken(
            new Form(
                new FormGroup( array(
                        new FormRow(
                            new FormColumn( new TextField( 'CredentialName', 'Benutzername', '', new Person() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialLock', 'Passwort', '', new Lock() ) )
                        ),
                        new FormRow(
                            new FormColumn( new PasswordField( 'CredentialKey', 'YubiKey', '', new YubiKey() ) )
                        )
                    )
                ), new Primary( 'Anmelden' )
            ),
            $CredentialName, $CredentialLock, $CredentialKey,
            Account::useService()->getIdentificationByName( 'Management' )
        ) );
        return $View;
    }

    /**
     * @return Stage
     */
    public function frontendDestroySession()
    {

        $View = new Stage( 'Abmelden', 'Bitte warten...' );
        $View->setContent( Account::useService()->destroySession(
            new Redirect( '/System/Gatekeeper/Authentication/Identification', 0 )
        ) );
        return $View;

    }
}