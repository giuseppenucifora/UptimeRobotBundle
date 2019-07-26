<?php


namespace Pn\UptimeRobotBundle\Model;


class AlertContact
{
    const STATUS_ACTIVE = 2;
    const STATUS_PAUSED = 1;
    const STATUS_NOT_ACTIVATED = 0;

    const TYPE_SMS = 1;
    const TYPE_EMAIL = 2;
    const TYPE_TWITTER_DM = 3;
    const TYPE_BOXCAR = 4;
    const TYPE_WEBHOOK = 5;
    const TYPE_PUSHBULLET = 6;
    const TYPE_ZAPIER = 7;
    const TYPE_PUSHOVER = 9;
    const TYPE_HIPCHAT = 10;
    const TYPE_SLACK = 11;

    const ALLOWED_FOR_CREATE = [self::TYPE_SMS, self::TYPE_EMAIL, self::TYPE_TWITTER_DM, self::TYPE_BOXCAR, self::TYPE_WEBHOOK, self::TYPE_PUSHBULLET, self::TYPE_PUSHOVER];

    private $id;

    private $friendlyName;

    private $type;

    private $status;

    private $value;

    private $errors;

    public function __construct()
    {
        $this->type = 2;
        $this->errors = [];
    }

    /**
     * @param $alertContactObj
     * @return AlertContact
     */
    public static function getAlertContactFromResponse($alertContactObj)
    {
        $alertContact = new AlertContact();

        $alertContact->setId($alertContactObj->id);
        $alertContact->setFriendlyName($alertContactObj->friendly_name);
        $alertContact->setType($alertContactObj->type);
        $alertContact->setStatus($alertContactObj->status);
        $alertContact->setValue($alertContactObj->value);

        return $alertContact;
    }

    /**
     * @return bool
     */
    public function isValidObjectForCreate()
    {
        $result = true;
        $this->errors = [];
        if (!in_array($this->getType(), self::ALLOWED_FOR_CREATE)) {
            $this->errors[] = 'Type not valid for create alert contact';
            $result = false;
        }

        switch ($this->getType()) {
            case self::TYPE_SMS:
                $checkValue = $this->getValue();

                if (!is_numeric(str_replace(' ', '', str_replace('+', '', $checkValue)))) {
                    $this->errors[] = 'Invalid number format';
                    $result = false;
                }

                break;
            case self::TYPE_EMAIL:
                if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
                    $this->errors[] = 'Invalid email format';
                    $result = false;
                }
                break;
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AlertContact
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * @param mixed $friendlyName
     * @return AlertContact
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return AlertContact
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return AlertContact
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return AlertContact
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getIdForMonitorRequest($threshold = 0, $recurrence = 0)
    {
        return sprintf('%u_%u_%u', $this->id, $threshold, $recurrence);
    }
}