<?php

namespace Pn\UptimeRobotBundle\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Vdhicts\UptimeRobot\Client\Client;
use Vdhicts\UptimeRobot\Client\Configuration;

class UptimeRobotApiService
{
    private $apiKey;

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
    }

    /**
     * Check if apikey is setted and suppose that api is valid
     *
     * @return bool
     */
    public function isActive(){
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
     * @return UptimeRobotMonitorService
     */
    public function getMonitorService(): UptimeRobotMonitorService
    {
        if (null === $this->monitorService) {
            $this->monitorService = New UptimeRobotMonitorService($this->client);
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
            $this->alertContactService = New UptimeRobotAlertContacsService($this->client);
            if ($this->io instanceof SymfonyStyle) {
                $this->alertContactService->setIo($this->io);
            }
        }
        return $this->alertContactService;
    }


}