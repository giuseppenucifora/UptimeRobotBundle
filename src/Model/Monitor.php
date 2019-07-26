<?php

namespace Pn\UptimeRobotBundle\Model;

class Monitor
{
    const TYPE_HTTP = 1;
    const TYPE_KEYWORD = 2;
    const TYPE_PING = 3;
    const TYPE_PORT = 4;

    const SUB_TYPE_HTTP = 1;
    const SUB_TYPE_HTTPS = 2;
    const SUB_TYPE_FTP = 3;
    const SUB_TYPE_SMTP = 4;
    const SUB_TYPE_POP3 = 5;
    const SUB_TYPE_IMAP = 6;
    const SUB_TYPE_CUSTOM_PORT = 99;

    const STATUS_DOWN = 9;
    const STATUS_SEEMS_DOWN = 8;
    const STATUS_UP = 2;
    const STATUS_IS_NOT_CHECKED_YET = 1;
    const STATUS_PAUSED = 0;

    const KEYWORD_TYPE_EXIST = 1;
    const KEYWORD_TYPE_NOT_EXIST = 2;

    /** @var int */
    private $id;

    /** @var string */
    private $friendlyName;

    /** @var string */
    private $url;

    /** @var int */
    private $type;

    /** @var string */
    private $subType;

    /** @var string */
    private $keywordType;

    /** @var string */
    private $keywordValue;

    /** @var string */
    private $httpUsername;

    /** @var string */
    private $httpPassword;

    /** @var string */
    private $port;

    /** @var int */
    private $interval;

    /** @var int */
    private $status;

    /** @var \DateTime */
    private $createDatetime;

    /**
     * @param $monitorObj
     * @return Monitor
     */
    public static function getMonitorFromResponse($monitorObj)
    {
        $monitor = new Monitor();

        $monitor->setId($monitorObj->id);
        $monitor->setFriendlyName($monitorObj->friendly_name);
        $monitor->setUrl($monitorObj->url);
        $monitor->setType($monitorObj->type);
        $monitor->setSubType($monitorObj->sub_type);
        $monitor->setKeywordType($monitorObj->keyword_type);
        $monitor->setKeywordValue($monitorObj->keyword_value);
        $monitor->setHttpUsername($monitorObj->http_username);
        $monitor->setHttpPassword($monitorObj->http_password);
        $monitor->setPort($monitorObj->port);
        $monitor->setInterval($monitorObj->interval);
        $monitor->setStatus($monitorObj->status);

        $date = new \DateTime();
        $date->setTimestamp($monitorObj->create_datetime);
        $monitor->setCreateDatetime($date);

        return $monitor;
    }

    public function __construct()
    {
        $this->type = self::TYPE_HTTP;
    }

    /**
     * @return bool
     */
    public function isValidObjectForCreate()
    {
        $result = true;

        return $result;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Monitor
     */
    public function setId(int $id): Monitor
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getFriendlyName(): ?string
    {
        return $this->friendlyName;
    }

    /**
     * @param string $friendlyName
     * @return Monitor
     */
    public function setFriendlyName(string $friendlyName): Monitor
    {
        $this->friendlyName = $friendlyName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Monitor
     */
    public function setUrl(string $url): Monitor
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Monitor
     */
    public function setType(?int $type): Monitor
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubType(): ?string
    {
        return $this->subType;
    }

    /**
     * @param string $subType
     * @return Monitor
     */
    public function setSubType(?string $subType): Monitor
    {
        $this->subType = $subType;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywordType(): ?string
    {
        return $this->keywordType;
    }

    /**
     * @param string $keywordType
     * @return Monitor
     */
    public function setKeywordType(?string $keywordType): Monitor
    {
        $this->keywordType = $keywordType;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywordValue(): ?string
    {
        return $this->keywordValue;
    }

    /**
     * @param string $keywordValue
     * @return Monitor
     */
    public function setKeywordValue(?string $keywordValue): Monitor
    {
        $this->keywordValue = $keywordValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getHttpUsername(): ?string
    {
        return $this->httpUsername;
    }

    /**
     * @param string $httpUsername
     * @return Monitor
     */
    public function setHttpUsername(?string $httpUsername): Monitor
    {
        $this->httpUsername = $httpUsername;
        return $this;
    }

    /**
     * @return string
     */
    public function getHttpPassword(): ?string
    {
        return $this->httpPassword;
    }

    /**
     * @param string $httpPassword
     * @return Monitor
     */
    public function setHttpPassword(?string $httpPassword): Monitor
    {
        $this->httpPassword = $httpPassword;
        return $this;
    }

    /**
     * @return string
     */
    public function getPort(): ?string
    {
        return $this->port;
    }

    /**
     * @param string $port
     * @return Monitor
     */
    public function setPort(?string $port): Monitor
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return int
     */
    public function getInterval(): ?int
    {
        return $this->interval;
    }

    /**
     * @param int $interval
     * @return Monitor
     */
    public function setInterval(?int $interval): Monitor
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Monitor
     */
    public function setStatus(?int $status): Monitor
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDatetime(): ?\DateTime
    {
        return $this->createDatetime;
    }

    /**
     * @param \DateTime $createDatetime
     * @return Monitor
     */
    public function setCreateDatetime(\DateTime $createDatetime): Monitor
    {
        $this->createDatetime = $createDatetime;
        return $this;
    }
}