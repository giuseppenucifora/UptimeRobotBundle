<?php

namespace Pn\UptimeRobotBundle\Service;

use Pn\UptimeRobotBundle\Model\AlertContact;
use Pn\UptimeRobotBundle\Model\Monitor;
use Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException;

class UptimeRobotMonitorService extends UptimeRobotService
{
    private $cachedMonitors = [];

    const GET_MONITORS = 'getMonitors';
    const NEW_MONITOR = 'newMonitor';
    const EDIT_MONITOR = 'editMonitor';
    const DELETE_MONITOR = 'deleteMonitor';
    const RESET_MONITOR = 'resetMonitor';


    /**
     * @return array
     */
    public function getAllMonitors()
    {
        return $this->getMonitors();
    }

    /**
     * @param array $params
     * @return array
     * @throws FailedRequestException
     */
    public function getMonitors($params = [])
    {
        $this->cachedMonitors = [];

        try {
            $jsonResponse = $this->client->perform(self::GET_MONITORS, $params);
        } catch (FailedRequestException $exception) {
            $this->logError($exception);
            throw $exception;
        }
        $response = json_decode($jsonResponse, false);

        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    foreach ($response->monitors as $monitorObj) {
                        $monitor = Monitor::getMonitorFromResponse($monitorObj);

                        $this->cachedMonitors[] = $monitor;
                    }
                    break;
            }

            return $this->cachedMonitors;
        }

        throw new FailedRequestException($jsonResponse);
    }

    /**
     * @param Monitor $monitor
     * @param array $alertContacts
     * @param int $threshold
     * @param int $recurrence
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function create(Monitor $monitor, array $alertContacts, int $threshold = 0, int $recurrence = 0): ?Monitor
    {
        try {
            $alertContactsString = '';

            /** @var AlertContact $alertContact */
            foreach ($alertContacts as $alertContact) {
                if ($alertContactsString !== '') {
                    $alertContactsString .= '-';
                }
                $alertContactsString .= $alertContact->getIdForMonitorRequest($threshold, $recurrence);
            }

            $jsonResponse = $this->client->perform(
                self::NEW_MONITOR,
                [
                    'friendly_name' => $monitor->getFriendlyName(),
                    'url' => $monitor->getUrl(),
                    'type' => $monitor->getType(),
                    'sub_type' => $monitor->getSubType(),
                    'port' => $monitor->getPort(),
                    'keyword_type' => $monitor->getKeywordType(),
                    'keyword_value' => $monitor->getKeywordValue(),
                    'interval' => $monitor->getInterval(),
                    'http_username' => $monitor->getHttpUsername(),
                    'http_password' => $monitor->getHttpPassword(),
                    'alert_contacts' => $alertContactsString,
                    'mwindows' => ''
                ]
            );
        } catch (FailedRequestException $exception) {
            $this->logError($exception);
            throw $exception;
        }

        $response = json_decode($jsonResponse, false);

        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    $monitor->setId($response->monitor->id);
                    return $monitor;
                default:
                    return null;
            }
        }
        throw new FailedRequestException($jsonResponse);
    }

    /**
     * @param Monitor $oldMonitor
     * @param Monitor $monitor
     * @param array $alertContacts
     * @param int $threshold
     * @param int $recurrence
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function update(Monitor $oldMonitor, Monitor $monitor, array $alertContacts, $threshold = 0, $recurrence = 0)
    {
        try {
            $alertContactsString = '';

            /** @var AlertContact $alertContact */
            foreach ($alertContacts as $alertContact) {
                if ($alertContactsString !== '') {
                    $alertContactsString .= '-';
                }
                $alertContactsString .= $alertContact->getIdForMonitorRequest($threshold, $recurrence);
            }

            $params = [
                'id' => $oldMonitor->getId(),
                'friendly_name' => $monitor->getFriendlyName(),
                'url' => $monitor->getUrl(),
                'type' => $monitor->getType(),
                'sub_type' => $monitor->getSubType(),
                'port' => $monitor->getPort(),
                'keyword_type' => $monitor->getKeywordType(),
                'keyword_value' => $monitor->getKeywordValue(),
                'interval' => $monitor->getInterval(),
                'http_username' => $monitor->getHttpUsername(),
                'http_password' => $monitor->getHttpPassword(),
                'mwindows' => '',
                'status' => $monitor->getStatus()
            ];

            if (!empty($alertContactsString)) {
                $params['alert_contacts'] = $alertContactsString;
            }

            $jsonResponse = $this->client->perform(
                self::EDIT_MONITOR,
                $params
            );
        } catch (FailedRequestException $exception) {
            $this->logError($exception);
            throw $exception;
        }

        $response = json_decode($jsonResponse, false);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    $monitor->setId($response->monitor->id);
                    return $monitor;
                default:
                    return null;

            }
        }
        throw new FailedRequestException($jsonResponse);
    }

    /**
     * @param Monitor $monitor
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function delete(Monitor $monitor)
    {
        try {
            $jsonResponse = $this->client->perform(self::DELETE_MONITOR, [
                'id' => $monitor->getId()
            ]);
        } catch (FailedRequestException $exception) {
            $this->logError($exception);
            throw $exception;
        }

        $response = json_decode($jsonResponse, false);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    return $monitor;
                default:
                    return null;

            }
        }

        throw new FailedRequestException($jsonResponse);
    }

    /**
     * @param Monitor $monitor
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function reset(Monitor $monitor)
    {
        try {
            $jsonResponse = $this->client->perform(self::RESET_MONITOR, [
                'id' => $monitor->getId()
            ]);
        } catch (FailedRequestException $exception) {
            $this->logError($exception);
            throw $exception;
        }

        $response = json_decode($jsonResponse, false);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    return $monitor;
                default:
                    return null;
            }
        }

        throw new FailedRequestException($jsonResponse);
    }

    /**
     * @param null $id
     * @param null $name
     * @param null $url
     * @param bool $forceRefresh
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function find($id = null, $name = null, $url = null, bool $forceRefresh = false)
    {
        if (empty($this->cachedMonitors) || $forceRefresh) {
            $this->getMonitors();
        }

        /** @var Monitor $monitor */
        foreach ($this->cachedMonitors as $monitor) {
            if ($monitor->getId() === $id || $monitor->getFriendlyName() === $name || $monitor->getUrl() === $url) {
                return $monitor;
            }
        }

        return null;
    }


    /**
     * @param $url
     * @param int $type
     * @param bool $forceRefresh
     * @return Monitor|null
     * @throws FailedRequestException
     */
    public function findOneByURLAndType($url, int $type = Monitor::TYPE_HTTP, bool $forceRefresh = false)
    {
        if (empty($this->cachedMonitors) || $forceRefresh) {
            $this->getMonitors();
        }

        /** @var Monitor $monitor */
        foreach ($this->cachedMonitors as $monitor) {
            if ($monitor->getUrl() === $url && $monitor->getType() === $type) {
                return $monitor;
            }
        }

        return null;
    }

    /**
     * @param Monitor $monitor
     * @param array $alertContacts
     * @param int $threshold
     * @param int $recurrence
     * @return null|Monitor
     * @throws FailedRequestException
     */
    public function createOrUpdate(Monitor $monitor, array $alertContacts, int $threshold = 0, int $recurrence = 0)
    {
        if ($monitor->isValidObjectForCreate()) {

            $foundMonitor = $this->findOneByURLAndType($monitor->getUrl(), $monitor->getType(), true);

            if ($foundMonitor instanceof Monitor) {
                return $this->update($foundMonitor, $monitor, $alertContacts, $threshold, $recurrence);
            }

            return $this->create($monitor, $alertContacts, $threshold, $recurrence);
        }

        return null;
    }
}