<?php

namespace Pn\UptimeRobotBundle\Service;

use Symfony\Component\Console\Style\SymfonyStyle;
use Vdhicts\UptimeRobot\Client\Client;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UptimeRobotService
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    /** @var \Vdhicts\UptimeRobot\Client\Client $client */
    protected $client;

    /** @var Serializer */
    protected $serializer;

    public function __construct(Client $client)
    {
        $this->client = $client;

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @return SymfonyStyle
     */
    public function getIo(): SymfonyStyle
    {
        return $this->io;
    }

    /**
     * @param SymfonyStyle $io
     * @return UptimeRobotService
     */
    public function setIo(SymfonyStyle $io): UptimeRobotService
    {
        $this->io = $io;
        return $this;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    protected function logError(\Exception $exception)
    {
        if ($this->getIo()) {
            $this->getIo()->error($exception->getMessage());
        }
    }
}