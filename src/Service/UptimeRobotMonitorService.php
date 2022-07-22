<?php

namespace Pn\UptimeRobotBundle\Service;

use Pn\UptimeRobotBundle\Model\AlertContact;
use Pn\UptimeRobotBundle\Model\Monitor;

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
     * @return array|bool
     */
    public function getMonitors($params = [])
    {
        $this->cachedMonitors = [];

        try {
            $jsonResponse = $this->client->perform(self::GET_MONITORS, $params);
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }
        $response = json_decode($jsonResponse);

        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    foreach ($response->monitors as $monitorObj) {
                        $monitor = Monitor::getMonitorFromResponse($monitorObj);

                        $this->cachedMonitors[] = $monitor;
                    }
                    break;
                default:
                    return false;
                    break;
            }
        }
        return $this->cachedMonitors;
    }

    /**
     * @param Monitor $monitor
     * @return bool|Monitor
     */
    public function create(Monitor $monitor, array $alertContacts, $threshold = 0, $recurrence = 0)
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
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);

        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    $monitor->setId($response->monitor->id);
                    return $monitor;
                    break;
                default:
                    break;
            }
        }
        return null;

    }

    /**
     * @param Monitor $oldMonitor
     * @param Monitor $monitor
     * @return bool|Monitor
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
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    $monitor->setId($response->monitor->id);
                    return $monitor;
                    break;
                default:
                    return false;
                    break;
            }
        }
    }

    /**
     * @param Monitor $monitor
     * @return bool|Monitor
     */
    public function delete(Monitor $monitor)
    {
        try {
            $jsonResponse = $this->client->perform(self::DELETE_MONITOR, [
                'id' => $monitor->getId()
            ]);
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    return $monitor;
                    break;
                default:
                    return false;
                    break;
            }
        }
        return null;
    }

    /**
     * @param Monitor $monitor
     * @return bool|Monitor
     */
    public function reset(Monitor $monitor)
    {
        try {
            $jsonResponse = $this->client->perform(self::RESET_MONITOR, [
                'id' => $monitor->getId()
            ]);
        } catch (\Vdhicts\UptimeRobot\Client\Exceptions\FailedRequestException $exception) {
            $this->logError($exception);
        }

        $response = json_decode($jsonResponse);
        if ($response) {
            switch ($response->stat) {
                case 'ok':
                    return $monitor;
                    break;
                default:
                    return false;
                    break;
            }
        }
        return null;
    }

    /**
     * @param $id
     * @param bool $forceRefresh
     * @return mixed|Monitor|null
     */
    public function find($id = null, $name = null, $url = null, $forceRefresh = false)
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
     * @return mixed|Monitor|null
     */
    public function findOneByURLAndType($url, $type = Monitor::TYPE_HTTP, $forceRefresh = false)
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
     * @return bool|mixed|Monitor
     */
    public function createOrUpdate(Monitor $monitor, array $alertContacts, $threshold = 0, $recurrence = 0)
    {
        if ($monitor->isValidObjectForCreate()) {

            $foundMonitor = $this->findOneByURLAndType($monitor->getUrl(), $monitor->getType(), true);

            if ($foundMonitor instanceof Monitor) {
                return $this->update($foundMonitor, $monitor, $alertContacts, $threshold, $recurrence);
            } else {
                return $this->create($monitor, $alertContacts, $threshold, $recurrence);
            }
        } else {
            return $monitor->getErrors();
        }
    }
}