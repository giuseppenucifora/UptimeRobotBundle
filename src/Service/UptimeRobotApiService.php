<?php

namespace Pn\UptimeRobotBundle\Service;

use Pn\UptimeRobotBundle\Model\AlertContact;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Vdhicts\UptimeRobot\Client\Client;
use Vdhicts\UptimeRobot\Client\Configuration;

class UptimeRobotApiService
{
    private $apiKey;

    private $interval;

    private $alertContactsString;

    private $alertContacts;

    /** @var $client */
    protected $client;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var UptimeRobotMonitorService $monitorService */
    private $monitorService;

    /** @var UptimeRobotAlertContacsService $alertContactService */
    private $alertContactService;

    /**
     * @var
     */
    private $io;

    public function __construct(string $apiKey, SerializerInterface $serializer)
    {
        $this->apiKey = $apiKey;
        $configuration = new Configuration($this->apiKey);

        $this->client = new Client($configuration);
        $this->serializer = $serializer;
        $this->alertContacts = [];
    }

    /**
     * Check if apikey is setted and suppose that api is valid
     *
     * @return bool
     */
    public function isActive()
    {
        return !empty($this->apiKey);
    }


    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @param SymfonyStyle $io
     * @return UptimeRobotApiService
     */
    public function setIo(SymfonyStyle $io): UptimeRobotApiService
    {
        $this->io = $io;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param mixed $interval
     */
    public function setInterval($interval): void
    {
        $this->interval = $interval;
    }

    /**
     * @param mixed $alertContactsString
     */
    public function setAlertContactsString(?string $alertContactsString): void
    {
        $this->alertContactsString = $alertContactsString;
        if (!empty($alertContactsString) && $this->isActive()) {
            $alertContacts = explode(',', $alertContactsString);

            foreach ($alertContacts as $alertContactName) {
                if (!empty($alertContactName)) {
                    $alertContact = $this->alertContactService->findbyName($alertContactName);
                    if ($alertContact instanceof AlertContact) {
                        $this->alertContacts[] = $alertContact;
                    }
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAlertContacts()
    {
        return $this->alertContacts;
    }

    /**
     * @param mixed $alertContacts
     */
    public function setAlertContacts($alertContacts): void
    {
        $this->alertContacts = $alertContacts;
    }

    /**
     * @return UptimeRobotMonitorService
     */
    public function getMonitorService(): UptimeRobotMonitorService
    {
        if (null === $this->monitorService) {
            $this->monitorService = new UptimeRobotMonitorService($this->client);
            if ($this->io instanceof SymfonyStyle) {
                $this->monitorService->setIo($this->io);
            }
        }
        return $this->monitorService;
    }

    /**
     * @return UptimeRobotAlertContacsService
     */
    public function getAlertContactService(): UptimeRobotAlertContacsService
    {
        if (null === $this->alertContactService) {
            $this->alertContactService = new UptimeRobotAlertContacsService($this->client);
            if ($this->io instanceof SymfonyStyle) {
                $this->alertContactService->setIo($this->io);
            }
        }
        return $this->alertContactService;
    }


}