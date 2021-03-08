<?php


namespace SLONline\DHLGlobalMail\Model;


class Paperwork
{
    public const TIME_SLOT_MORNING = 'MORNING';
    public const TIME_SLOT_MIDDAY = 'MIDDAY';
    public const TIME_SLOT_EVENING = 'EVENING';
    public const TIME_SLOTS = [
        self::TIME_SLOT_MORNING,
        self::TIME_SLOT_MIDDAY,
        self::TIME_SLOT_MIDDAY,
    ];
    
    public const PICKUP_TYPE_CUSTOMER_DROP_OFF = 'CUSTOMER_DROP_OFF';
    public const PICKUP_TYPE_SCHEDULED = 'SCHEDULED';
    public const PICKUP_TYPE_DHL_GLOBAL_MAIL = 'DHL_GLOBAL_MAIL';
    public const PICKUP_TYPE_DHL_EXPRESS = 'DHL_EXPRESS';
    public const PICKUP_TYPES = [
        self::PICKUP_TYPE_CUSTOMER_DROP_OFF,
        self::PICKUP_TYPE_SCHEDULED,
        self::PICKUP_TYPE_DHL_GLOBAL_MAIL,
        self::PICKUP_TYPE_DHL_EXPRESS,
    ];
    
    /**
     * Copies of AWB labels.
     * Number must be less than, or equal to 99
     * Number must be more than, or equal to 1
     * Value must be of format 'int32'
     * @var int
     */
    protected $awbCopyCount;
    
    /**
     * Contact name for paperwork.
     * @var string
     */
    protected $contactName;
    
    /**
     * Job reference for paperwork.
     * @var string
     */
    protected $jobReference;
    
    /**
     * Pickup date used in pickup information. Only applicable, if you have chosen Pick-Up Type DHL_GLOBAL_MAIl or
     * DHL_EXPRESS.
     * @var string
     */
    protected $pickupDate;
    
    /**
     * Pickup location used in pickup information. Only applicable, if you have chosen Pick-Up Type DHL_GLOBAL_MAIl or
     * DHL_EXPRESS.
     * @var string
     */
    protected $pickupLocation;
    
    /**
     * Pickup timeslot used in pickup information. Only necessary if pickupType set to DHL_GLOBAL_MAIl or DHL_EXPRESS.
     * The following values are avaliable MORNING (timeslot from 08:00 to 12:00), MIDDAY (timeslot from 12:00 to 15:00)
     * and EVENING (timeslot from 15:00 to 19:00).
     *
     * @var string[MORNING,MIDDAY,EVENING]
     */
    protected $pickupTimeSlot;
    /**
     * Pickup type used in pickup information. If not set it defaults to "CUSTOMER_DROP_OFF".
     * @var string[CUSTOMER_DROP_OFF,SCHEDULED,DHL_GLOBAL_MAIL,DHL_EXPRESS]
     */
    protected $pickupType = 'CUSTOMER_DROP_OFF';
    /**
     * Telephone number for paperwork. Required for sales channel EXPRESS.
     * @var string
     */
    protected $telephoneNumber;
    
    public function __construct(array $data)
    {
        $this->contactName  = $data['contactName'];
        $this->awbCopyCount = $data['awbCopyCount'];
        if (\array_key_exists('jobReference', $data)) {
            $this->jobReference = $data['jobReference'];
        }
        if (\array_key_exists('pickupType', $data)) {
            $this->pickupType = $data['pickupType'];
        }
        if (\array_key_exists('pickupLocation', $data)) {
            $this->pickupLocation = $data['pickupLocation'];
        }
        if (\array_key_exists('pickupDate', $data)) {
            $this->pickupDate = $data['pickupDate'];
        }
        if (\array_key_exists('pickupTimeSlot', $data)) {
            $this->pickupTimeSlot = $data['pickupTimeSlot'];
        }
        if (\array_key_exists('telephoneNumber', $data)) {
            $this->telephoneNumber = $data['telephoneNumber'];
        }
    }
    
    /**
     * @return string
     */
    public function getPickupDate(): string
    {
        return $this->pickupDate;
    }
    
    /**
     * @param string $pickupDate
     */
    public function setPickupDate(string $pickupDate)
    {
        $this->pickupDate = $pickupDate;
    }
    
    /**
     * @return string
     */
    public function getPickupLocation(): string
    {
        return $this->pickupLocation;
    }
    
    /**
     * @param string $pickupLocation
     */
    public function setPickupLocation(string $pickupLocation)
    {
        $this->pickupLocation = $pickupLocation;
    }
    
    /**
     * @return string
     */
    public function getPickupTimeSlot(): string
    {
        return $this->pickupTimeSlot;
    }
    
    /**
     * @param string $pickupTimeSlot
     */
    public function setPickupTimeSlot(string $pickupTimeSlot)
    {
        if (\in_array($pickupTimeSlot, self::TIME_SLOTS)) {
            $this->pickupTimeSlot = $pickupTimeSlot;
        }
    }
    
    /**
     * @return string
     */
    public function getTelephoneNumber(): string
    {
        return $this->telephoneNumber;
    }
    
    /**
     * @param string $telephoneNumber
     */
    public function setTelephoneNumber(string $telephoneNumber)
    {
        $this->telephoneNumber = $telephoneNumber;
    }
    
    public function getContactName(): string
    {
        return $this->contactName;
    }
    
    public function setContactName(string $name): void
    {
        $this->contactName = $name;
    }
    
    public function getAwbCopyCount(): int
    {
        return $this->awbCopyCount;
    }
    
    public function setAwbCopyCount(int $count): void
    {
        $this->awbCopyCount = $count;
    }
    
    public function getJobReference(): string
    {
        return $this->jobReference;
    }
    
    public function setJobReference(string $reference): void
    {
        $this->jobReference = $reference;
    }
    
    public function getPickupType(): string
    {
        return $this->pickupType;
    }
    
    public function setPickupType(string $type): void
    {
        if (\in_array($type, self::PICKUP_TYPES)) {
            $this->pickupType = $type;
        }
    }
    
    public function __toString(): string
    {
        return $this->contactName;
    }
    
    public function toArray(): array
    {
        return [
            'contactName'     => $this->contactName,
            'awbCopyCount'    => $this->awbCopyCount,
            'jobReference'    => $this->jobReference,
            'pickupType'      => $this->pickupType,
            'pickupLocation'  => $this->pickupLocation,
            'pickupDate'      => $this->pickupDate,
            'pickupTimeSlot'  => $this->pickupTimeSlot,
            'telephoneNumber' => $this->telephoneNumber,
        ];
    }
}