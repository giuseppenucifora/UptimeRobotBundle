<?php

namespace Pn\UptimeRobotBundle\Service;

use Pn\UptimeRobotBundle\Model\AlertContact;

class UptimeRobotAlertContacsService extends UptimeRobotService
{
    private $cachedAlertContacts = [];

    const GET_ALERT_CONTACTS = 'getAlertContacts';
    const NEW_ALERT_CONTACT = 'newAlertContact';
    const EDIT_ALERT_CONTACT = 'editAlertContact';
    const DELETE_ALERT_CONTACT = 'deleteAlertContact';

    /**
     * @return array
     */
    public function getAlertContacts()
    {
        try {
            $jsonResponse = $this->client->perform(self::GET_ALERT_CONTACTS);
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);

        switch ($response->stat) {
            case 'ok':

                foreach ($response->alert_contacts as $alert_contact) {

                    $alertContact = AlertContact::getAlertContactFromResponse($alert_contact);

                    $this->cachedAlertContacts[] = $alertContact;
                }

                break;
            default:
                break;
        }


        return $this->cachedAlertContacts;
    }


    /**
     * @param AlertContact $alertContact
     * @return bool|AlertContact
     */
    public function create(AlertContact $alertContact)
    {
        try {
            $jsonResponse = $this->client->perform(
                self::NEW_ALERT_CONTACT,
                [
                    'type' => $alertContact->getType(),
                    'value' => $alertContact->getValue(),
                    'friendly_name' => $alertContact->getFriendlyName()
                ]
            );
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);

        switch ($response->stat) {
            case 'ok':
                $alertContact->setId($response->alertcontact->id);
                return $alertContact;
                break;
            default:
                return false;
                break;
        }

    }

    /**
     * @param AlertContact $oldAlertContact
     * @param AlertContact $alertContact
     * @return bool|AlertContact
     */
    public function update(AlertContact $oldAlertContact, AlertContact $alertContact)
    {
        try {
            $jsonResponse = $this->client->perform(
                self::EDIT_ALERT_CONTACT,
                [
                    'id' => $oldAlertContact->getId(),
                    'type' => $alertContact->getType(),
                    'value' => $alertContact->getValue(),
                    'friendly_name' => $alertContact->getFriendlyName()
                ]
            );
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);

        switch ($response->stat) {
            case 'ok':
                $alertContact->setId($response->alert_contact->id);
                return $alertContact;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @param AlertContact $alertContact
     * @return bool|AlertContact
     */
    public function delete(AlertContact $alertContact)
    {
        try {
            $jsonResponse = $this->client->perform(self::DELETE_ALERT_CONTACT, [
                'id' => $alertContact->getId()
            ]);
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);

        switch ($response->stat) {
            case 'ok':
                return $alertContact;
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * @param $id
     * @param bool $forceRefresh
     * @return mixed|AlertContact|null
     */
    public function find($id, $forceRefresh = false)
    {
        if (empty($this->cachedAlertContacts) || $forceRefresh) {
            $this->getAlertContacts();
        }

        /** @var AlertContact $alertContact */
        foreach ($this->cachedAlertContacts as $alertContact) {
            if ($alertContact->getId() === $id) {
                return $alertContact;
            }
        }

        return null;
    }


    /**
     * @param $value
     * @param int $type
     * @param bool $forceRefresh
     * @return mixed|AlertContact|null
     */
    public function findOneByValueAndType($value, $type = AlertContact::TYPE_EMAIL, $forceRefresh = false)
    {
        if (empty($this->cachedAlertContacts) || $forceRefresh) {
            $this->getAlertContacts();
        }

        /** @var AlertContact $alertContact */
        foreach ($this->cachedAlertContacts as $alertContact) {
            if ($alertContact->getValue() === $value && $alertContact->getType() === $type) {
                return $alertContact;
            }
        }

        return null;
    }

    /**
     * @param AlertContact $alertContact
     * @return bool|mixed|AlertContact
     */
    public function createOrUpdate(AlertContact $alertContact)
    {
        if ($alertContact->isValidObjectForCreate()) {

            $foundAlertContact = $this->findOneByValueAndType($alertContact->getValue(), $alertContact->getType(), true);

            if ($foundAlertContact instanceof AlertContact) {
                return $this->update($foundAlertContact, $alertContact);
            } else {
                return $this->create($alertContact);
            }
        } else {
            return $alertContact->getErrors();
        }
    }
}