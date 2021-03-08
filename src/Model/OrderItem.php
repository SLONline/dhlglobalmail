<?php


namespace SLONline\DHLGlobalMail\Model;


class OrderItem
{
    /**
     * Business Mail Standard
     */
    public const PRODUCT_GMM = 'GMM';
    /**
     * Business Mail Registered
     */
    public const PRODUCT_GMR = 'GMR';
    /**
     * Packet Tracked
     */
    public const PRODUCT_GPT = 'GPT';
    /**
     * Packet Plus
     */
    public const PRODUCT_GPP = 'GPP';
    /**
     * Packet
     */
    public const PRODUCT_GMP = 'GMP';
    public const PRODUCTS = [
        self::PRODUCT_GMM,
        self::PRODUCT_GMP,
        self::PRODUCT_GMR,
        self::PRODUCT_GPP,
        self::PRODUCT_GPT,
    ];
    
    public const SERVICE_LEVEL_PRIORITY = 'PRIORITY';
    public const SERVICE_LEVEL_STANDARD = 'STANDARD';
    public const SERVICE_LEVEL_REGISTERED = 'REGISTERED';
    public const SERVICE_LEVELS = [
        self::SERVICE_LEVEL_PRIORITY,
        self::SERVICE_LEVEL_STANDARD,
        self::SERVICE_LEVEL_REGISTERED,
    ];
    
    public const SHIPMENT_NATURE_TYPE_SALE_GOODS = 'SALE_GOODS';
    public const SHIPMENT_NATURE_TYPE_RETURN_GOODS = 'RETURN_GOODS';
    public const SHIPMENT_NATURE_TYPE_GIFT = 'GIFT';
    public const SHIPMENT_NATURE_TYPE_COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE';
    public const SHIPMENT_NATURE_TYPE_DOCUMENTS = 'DOCUMENTS';
    public const SHIPMENT_NATURE_TYPE_MIXED_CONTENTS = 'MIXED_CONTENTS';
    public const SHIPMENT_NATURE_TYPE_OTHERS = 'OTHERS';
    public const SHIPMENT_NATURE_TYPES = [
        self::SHIPMENT_NATURE_TYPE_SALE_GOODS,
        self::SHIPMENT_NATURE_TYPE_RETURN_GOODS,
        self::SHIPMENT_NATURE_TYPE_GIFT,
        self::SHIPMENT_NATURE_TYPE_COMMERCIAL_SAMPLE,
        self::SHIPMENT_NATURE_TYPE_DOCUMENTS,
        self::SHIPMENT_NATURE_TYPE_MIXED_CONTENTS,
        self::SHIPMENT_NATURE_TYPE_OTHERS,
    ];
    public static $optionalFields = [
        'addressLine2',
        'addressLine3',
        'barcode',
        'contents',
        'custRef',
        'custRef2',
        'custRef3',
        'id',
        'postalCode',
        'recipientEmail',
        'recipientFax',
        'recipientPhone',
        'importerTaxId',
        'returnItemWanted',
        'senderAddressLine1',
        'senderAddressLine2',
        'senderCity',
        'senderCountry',
        'senderEmail',
        'senderName',
        'senderPhone',
        'senderPostalCode',
        'senderTaxId',
        'serviceLevel',
        'shipmentAmount',
        'shipmentCurrency',
        'shipmentNaturetype',
        'state',
        'thirdPartyVendorId',
        'voucherId',
    ];
    /**
     * The id of the item
     * @var int
     */
    protected $id;
    /**
     * The product that is used for the shipment of this item. Available products are: GPP (Packet Plus), GMP (Packet),
     * GMM (Business Mail Standard), GMR (Business Mail Registered), GPT (Packet Tracked).246/247/... (Internet Stamp
     * Product)
     * @var string
     */
    protected $product;
    /**
     * Frist line of address information of the recipient.
     * @var string
     */
    protected $addressLine1;
    /**
     * Second line of address information of the recipient.
     * @var string
     */
    protected $addressLine2;
    /**
     * Third line of address information of the recipient.
     * @var string
     */
    protected $addressLine3;
    /**
     * City of the recipient address.
     * @var string
     */
    protected $city;
    /**
     * Postal code of the recipient address.
     * @var string
     */
    protected $postalCode;
    /**
     * State of the recipient address.
     * @var string
     */
    protected $state;
    /**
     * Destination country of the item, based on ISO-3166-1. Please check
     * https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2 for further details.
     * @var string[2]
     */
    protected $destinationCountry;
    /**
     * The barcode of this item (if available).
     * @var string
     */
    protected $barcode;
    /**
     * Reference to the customer.
     * @var string
     */
    protected $custRef;
    /**
     * Generic field to deliver input depending on the given business context. E.g when using 'Internetstamp -
     * OneClickForShop' mapped to 'UserID'.
     * @var string
     */
    protected $custRef2;
    /**
     * Generic field to deliver input depending on the given business context. E.g when using 'Internetstamp -
     * OneClickForShop' mapped to 'ShopOrderId'.
     * @var string
     */
    protected $custRef3;
    /**
     * The descriptions of the content pieces.
     * @var array|string|number
     */
    protected $contents;
    /**
     * Name of the recipient.
     * @var string
     */
    protected $recipient;
    /**
     * Email address of the recipient. Used for notification.
     * @var string
     */
    protected $recipientEmail;
    /**
     * Fax number of the recipient
     * @var string
     */
    protected $recipientFax;
    /**
     * Phone number of the recipient
     * @var string
     */
    protected $recipientPhone;
    /**
     * Tax ID of the importer/recipient of the item
     * @var string
     */
    protected $importerTaxId;
    /**
     * Is Packet Return.
     * @var bool
     */
    protected $returnItemWanted;
    /**
     * Frist line of address information of the sender.
     * @var string
     */
    protected $senderAddressLine1;
    /**
     * Second line of address information of the sender.
     * @var string
     */
    protected $senderAddressLine2;
    /**
     * City of the sender address.
     * @var string
     */
    protected $senderCity;
    /**
     * Sender country of the item, based on ISO-3166-1. Please check https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
     * for further details.
     * @var string[2]
     */
    protected $senderCountry;
    /**
     * Email address of the sender. Used for notification.
     * @var string
     */
    protected $senderEmail;
    /**
     * Name of the sender.
     * @var string
     */
    protected $senderName;
    /**
     * Phone number of the sender
     * @var string
     */
    protected $senderPhone;
    /**
     * Postal code of the sender address.
     * @var string
     */
    protected $senderPostalCode;
    /**
     * Tax ID of the sender of the item
     * @var string
     */
    protected $senderTaxId;
    /**
     * The service level that is used for the shipment of this item. There are restrictions for use of service level:
     * Registered is only available with product GMR and SalesChannel DPI, STANDARD is only available with products GMM
     * and GMP, PRIORITY is only available with products GPT, GPP and GMP.
     * @var string
     */
    protected $serviceLevel;
    /**
     * Overall value of all content pieces of the item.
     * @var double
     */
    protected $shipmentAmount;
    /**
     * Currency code of the value, based on ISO-4217. Please check https://en.wikipedia.org/wiki/ISO_4217#Active_codes
     * for further details.
     * @var string
     */
    protected $shipmentCurrency;
    /**
     * Gross weight of the item (in g). May not exceed 2000 g.
     * @var int
     */
    protected $shipmentGrossWeight;
    /**
     * Nature of the pieces in this item, based on UPU code list 136.
     * @var string
     */
    protected $shipmentNaturetype;
    /**
     * The ID of the 3PV/Thrid Party Vendor who created this item.
     * @var string
     */
    protected $thirdPartyVendorId;
    /**
     * VoucherId of corresponding internet stamp.
     * @var string
     */
    protected $voucherId;
    
    /**
     * OrderItem constructor.
     *
     * @param string $addressLine1
     * @param string $city
     * @param string $destinationCountry
     * @param string $product
     * @param string $recipient
     * @param int    $shipmentGrossWeight (in g)
     */
    public function __construct(
        string $addressLine1,
        string $city,
        string $destinationCountry,
        string $product,
        string $recipient,
        int $shipmentGrossWeight
    ) {
        $this->setAddressLine1($addressLine1);
        $this->setCity($city);
        $this->setDestinationCountry($destinationCountry);
        $this->setProduct($product);
        $this->setRecipient($recipient);
        $this->setShipmentGrossWeight($shipmentGrossWeight);
    }
    
    public static function createFromData(array $data): self
    {
        $item = new self(
            (string)$data['addressLine1'],
            (string)$data['city'],
            (string)$data['destinationCountry'],
            (string)$data['product'],
            (string)$data['recipient'],
            (double)$data['shipmentGrossWeight']
        );
        
        foreach (self::$optionalFields as $key) {
            $method = 'set' . \ucfirst($key);
            if (\array_key_exists($key, $data) &&
                \method_exists($item, $method)) {
                $item->{$method}($data[$key]);
            }
        }
        
        return $item;
    }
    
    /**
     * @return string
     */
    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }
    
    /**
     * @param string $addressLine2
     *
     * @return OrderItem
     */
    public function setAddressLine2(string $addressLine2): OrderItem
    {
        $this->addressLine2 = $addressLine2;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAddressLine3(): string
    {
        return $this->addressLine3;
    }
    
    /**
     * @param string $addressLine3
     *
     * @return OrderItem
     */
    public function setAddressLine3(string $addressLine3): OrderItem
    {
        $this->addressLine3 = $addressLine3;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }
    
    /**
     * @param string $postalCode
     *
     * @return OrderItem
     */
    public function setPostalCode(string $postalCode): OrderItem
    {
        $this->postalCode = $postalCode;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
    
    /**
     * @param string $state
     *
     * @return OrderItem
     */
    public function setState(string $state): OrderItem
    {
        $this->state = $state;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getBarcode(): string
    {
        return $this->barcode;
    }
    
    /**
     * @param string $barcode
     *
     * @return OrderItem
     */
    public function setBarcode(string $barcode): OrderItem
    {
        $this->barcode = $barcode;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCustRef(): string
    {
        return $this->custRef;
    }
    
    /**
     * @param string $custRef
     *
     * @return OrderItem
     */
    public function setCustRef(string $custRef): OrderItem
    {
        $this->custRef = $custRef;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCustRef2(): string
    {
        return $this->custRef2;
    }
    
    /**
     * @param string $custRef2
     *
     * @return OrderItem
     */
    public function setCustRef2(string $custRef2): OrderItem
    {
        $this->custRef2 = $custRef2;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCustRef3(): string
    {
        return $this->custRef3;
    }
    
    /**
     * @param string $custRef3
     *
     * @return OrderItem
     */
    public function setCustRef3(string $custRef3): OrderItem
    {
        $this->custRef3 = $custRef3;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }
    
    /**
     * @param mixed $contents
     *
     * @return OrderItem
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }
    
    /**
     * @param string $recipientEmail
     *
     * @return OrderItem
     */
    public function setRecipientEmail(string $recipientEmail): OrderItem
    {
        $this->recipientEmail = $recipientEmail;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRecipientFax(): string
    {
        return $this->recipientFax;
    }
    
    /**
     * @param string $recipientFax
     *
     * @return OrderItem
     */
    public function setRecipientFax(string $recipientFax): OrderItem
    {
        $this->recipientFax = $recipientFax;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRecipientPhone(): string
    {
        return $this->recipientPhone;
    }
    
    /**
     * @param string $recipientPhone
     *
     * @return OrderItem
     */
    public function setRecipientPhone(string $recipientPhone): OrderItem
    {
        $this->recipientPhone = $recipientPhone;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getImporterTaxId(): string
    {
        return $this->importerTaxId;
    }
    
    /**
     * @param string $importerTaxId
     *
     * @return OrderItem
     */
    public function setImporterTaxId(string $importerTaxId): OrderItem
    {
        $this->importerTaxId = $importerTaxId;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isReturnItemWanted(): bool
    {
        return $this->returnItemWanted;
    }
    
    /**
     * @param bool $returnItemWanted
     *
     * @return OrderItem
     */
    public function setReturnItemWanted(bool $returnItemWanted): OrderItem
    {
        $this->returnItemWanted = $returnItemWanted;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderAddressLine1(): string
    {
        return $this->senderAddressLine1;
    }
    
    /**
     * @param string $senderAddressLine1
     *
     * @return OrderItem
     */
    public function setSenderAddressLine1(string $senderAddressLine1): OrderItem
    {
        $this->senderAddressLine1 = $senderAddressLine1;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderAddressLine2(): string
    {
        return $this->senderAddressLine2;
    }
    
    /**
     * @param string $senderAddressLine2
     *
     * @return OrderItem
     */
    public function setSenderAddressLine2(string $senderAddressLine2): OrderItem
    {
        $this->senderAddressLine2 = $senderAddressLine2;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderCity(): string
    {
        return $this->senderCity;
    }
    
    /**
     * @param string $senderCity
     *
     * @return OrderItem
     */
    public function setSenderCity(string $senderCity): OrderItem
    {
        $this->senderCity = $senderCity;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderCountry(): string
    {
        return $this->senderCountry;
    }
    
    /**
     * @param string $senderCountry
     *
     * @return OrderItem
     */
    public function setSenderCountry(string $senderCountry): OrderItem
    {
        $this->senderCountry = $senderCountry;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }
    
    /**
     * @param string $senderEmail
     *
     * @return OrderItem
     */
    public function setSenderEmail(string $senderEmail): OrderItem
    {
        $this->senderEmail = $senderEmail;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }
    
    /**
     * @param string $senderName
     *
     * @return OrderItem
     */
    public function setSenderName(string $senderName): OrderItem
    {
        $this->senderName = $senderName;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderPhone(): string
    {
        return $this->senderPhone;
    }
    
    /**
     * @param string $senderPhone
     *
     * @return OrderItem
     */
    public function setSenderPhone(string $senderPhone): OrderItem
    {
        $this->senderPhone = $senderPhone;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderPostalCode(): string
    {
        return $this->senderPostalCode;
    }
    
    /**
     * @param string $senderPostalCode
     *
     * @return OrderItem
     */
    public function setSenderPostalCode(string $senderPostalCode): OrderItem
    {
        $this->senderPostalCode = $senderPostalCode;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getSenderTaxId(): string
    {
        return $this->senderTaxId;
    }
    
    /**
     * @param string $senderTaxId
     *
     * @return OrderItem
     */
    public function setSenderTaxId(string $senderTaxId): OrderItem
    {
        $this->senderTaxId = $senderTaxId;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getServiceLevel(): string
    {
        return $this->serviceLevel;
    }
    
    /**
     * @param string $serviceLevel
     *
     * @return OrderItem
     */
    public function setServiceLevel(string $serviceLevel): OrderItem
    {
        $this->serviceLevel = $serviceLevel;
        
        return $this;
    }
    
    /**
     * @return float
     */
    public function getShipmentAmount(): float
    {
        return $this->shipmentAmount;
    }
    
    /**
     * @param float $shipmentAmount
     *
     * @return OrderItem
     */
    public function setShipmentAmount(float $shipmentAmount): OrderItem
    {
        $this->shipmentAmount = $shipmentAmount;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getShipmentCurrency(): string
    {
        return $this->shipmentCurrency;
    }
    
    /**
     * @param string $shipmentCurrency
     *
     * @return OrderItem
     */
    public function setShipmentCurrency(string $shipmentCurrency): OrderItem
    {
        $this->shipmentCurrency = $shipmentCurrency;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getShipmentNaturetype(): string
    {
        return $this->shipmentNaturetype;
    }
    
    /**
     * @param string $shipmentNaturetype
     *
     * @return OrderItem
     */
    public function setShipmentNaturetype(string $shipmentNaturetype): OrderItem
    {
        $this->shipmentNaturetype = $shipmentNaturetype;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getThirdPartyVendorId(): string
    {
        return $this->thirdPartyVendorId;
    }
    
    /**
     * @param string $thirdPartyVendorId
     *
     * @return OrderItem
     */
    public function setThirdPartyVendorId(string $thirdPartyVendorId): OrderItem
    {
        $this->thirdPartyVendorId = $thirdPartyVendorId;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getVoucherId(): string
    {
        return $this->voucherId;
    }
    
    /**
     * @param string $voucherId
     *
     * @return OrderItem
     */
    public function setVoucherId(string $voucherId): OrderItem
    {
        $this->voucherId = $voucherId;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     *
     * @return OrderItem
     */
    public function setId(int $id): OrderItem
    {
        $this->id = $id;
        
        return $this;
    }
    
    public function toArray(): array
    {
        $item = [
            'addressLine1'        => $this->getAddressLine1(),
            'city'                => $this->getCity(),
            'destinationCountry'  => $this->getDestinationCountry(),
            'product'             => $this->getProduct(),
            'recipient'           => $this->getRecipient(),
            'shipmentGrossWeight' => $this->getShipmentGrossWeight(),
        ];
        
        foreach (self::$optionalFields as $key) {
            if (!empty($this->{$key}) && \method_exists($this, 'get' . \ucfirst($key))) {
                $item[$key] = $this->{'get' . \ucfirst($key)}();
            }
        }
        
        return $item;
    }
    
    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }
    
    /**
     * @param string $addressLine1
     *
     * @return OrderItem
     */
    public function setAddressLine1(string $addressLine1): OrderItem
    {
        $this->addressLine1 = $addressLine1;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }
    
    /**
     * @param string $city
     *
     * @return OrderItem
     */
    public function setCity(string $city): OrderItem
    {
        $this->city = $city;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDestinationCountry(): string
    {
        return $this->destinationCountry;
    }
    
    /**
     * @param string $destinationCountry
     *
     * @return OrderItem
     */
    public function setDestinationCountry(string $destinationCountry): OrderItem
    {
        $this->destinationCountry = $destinationCountry;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getProduct(): string
    {
        return $this->product;
    }
    
    /**
     * @param string $product
     *
     * @return OrderItem
     */
    public function setProduct(string $product): OrderItem
    {
        $this->product = $product;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }
    
    /**
     * @param string $recipient
     *
     * @return OrderItem
     */
    public function setRecipient(string $recipient): OrderItem
    {
        $this->recipient = $recipient;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getShipmentGrossWeight(): int
    {
        return $this->shipmentGrossWeight;
    }
    
    /**
     * @param int $shipmentGrossWeight
     *
     * @return OrderItem
     */
    public function setShipmentGrossWeight(int $shipmentGrossWeight): OrderItem
    {
        $this->shipmentGrossWeight = $shipmentGrossWeight;
        
        return $this;
    }
}