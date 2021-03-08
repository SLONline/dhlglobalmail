<?php


namespace SLONline\DHLGlobalMail\Model;


class Tracking
{
    /**
     * AWB number
     * @var string
     */
    protected $awb;
    /**
     * Barcode
     * @var string
     */
    protected $barcode;
    /**
     * All events
     * @var array
     */
    protected $events;
    /**
     * Sender address line 1
     * @var string
     */
    protected $fromAddress1;
    /**
     * Sender address line 2
     * @var string
     */
    protected $fromAddress2;
    /**
     * Sender address line 3
     * @var string
     */
    protected $fromAddress3;
    /**
     * Sender city
     * @var string
     */
    protected $fromCity;
    /**
     * Sender country
     * @var string
     */
    protected $fromCountry;
    /**
     * Sender name
     * @var string
     */
    protected $fromName;
    /**
     * Sender zip code
     * @var string
     */
    protected $fromZip;
    /**
     * Public URL
     * @var string
     */
    protected $publicUrl;
    /**
     * Recipient address line 1
     * @var string
     */
    protected $toAddress1;
    /**
     * Recipient address line 2
     * @var string
     */
    protected $toAddress2;
    /**
     * Recipient address line 3
     * @var string
     */
    protected $toAddress3;
    /**
     * Recipient city
     * @var string
     */
    protected $toCity;
    /**
     * Recipient country
     * @var string
     */
    protected $toCountry;
    /**
     * Recipient name
     * @var string
     */
    protected $toName;
    /**
     * Recipient zip code
     * @var string
     */
    protected $toZip;
    
    public function __construct(string $awb, string $barcode, array $events)
    {
        $this->setAwb($awb);
        $this->setBarcode($barcode);
        $this->setEvents($events);
    }
    
    public static function createFromData(array $data): self
    {
        $item = new self(
            (string)$data['awb'],
            (string)$data['barcode'],
            $data['events']
        );
        unset($data['awb']);
        unset($data['barcode']);
        unset($data['events']);
        
        foreach ($data as $key=>$value) {
            $method = 'set' . \ucfirst($key);
            if (\method_exists($item, $method)) {
                $item->{$method}($value);
            }
        }
        
        return $item;
    }
    
    /**
     * @return string
     */
    public function getAwb(): string
    {
        return $this->awb;
    }
    
    /**
     * @param string $awb
     *
     * @return Tracking
     */
    public function setAwb(string $awb): Tracking
    {
        $this->awb = $awb;
        
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
     * @return Tracking
     */
    public function setBarcode(string $barcode): Tracking
    {
        $this->barcode = $barcode;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getEvents()
    {
        return $this->events;
    }
    
    /**
     * @param mixed $events
     *
     * @return Tracking
     */
    public function setEvents($events)
    {
        $this->events = $events;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromAddress1(): string
    {
        return $this->fromAddress1;
    }
    
    /**
     * @param string $fromAddress1
     *
     * @return Tracking
     */
    public function setFromAddress1(string $fromAddress1): Tracking
    {
        $this->fromAddress1 = $fromAddress1;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromAddress2(): string
    {
        return $this->fromAddress2;
    }
    
    /**
     * @param string $fromAddress2
     *
     * @return Tracking
     */
    public function setFromAddress2(string $fromAddress2): Tracking
    {
        $this->fromAddress2 = $fromAddress2;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromAddress3(): string
    {
        return $this->fromAddress3;
    }
    
    /**
     * @param string $fromAddress3
     *
     * @return Tracking
     */
    public function setFromAddress3(string $fromAddress3): Tracking
    {
        $this->fromAddress3 = $fromAddress3;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromCity(): string
    {
        return $this->fromCity;
    }
    
    /**
     * @param string $fromCity
     *
     * @return Tracking
     */
    public function setFromCity(string $fromCity): Tracking
    {
        $this->fromCity = $fromCity;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromCountry(): string
    {
        return $this->fromCountry;
    }
    
    /**
     * @param string $fromCountry
     *
     * @return Tracking
     */
    public function setFromCountry(string $fromCountry): Tracking
    {
        $this->fromCountry = $fromCountry;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromName(): string
    {
        return $this->fromName;
    }
    
    /**
     * @param string $fromName
     *
     * @return Tracking
     */
    public function setFromName(string $fromName): Tracking
    {
        $this->fromName = $fromName;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getFromZip(): string
    {
        return $this->fromZip;
    }
    
    /**
     * @param string $fromZip
     *
     * @return Tracking
     */
    public function setFromZip(string $fromZip): Tracking
    {
        $this->fromZip = $fromZip;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPublicUrl(): string
    {
        return $this->publicUrl;
    }
    
    /**
     * @param string $publicUrl
     *
     * @return Tracking
     */
    public function setPublicUrl(string $publicUrl): Tracking
    {
        $this->publicUrl = $publicUrl;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToAddress1(): string
    {
        return $this->toAddress1;
    }
    
    /**
     * @param string $toAddress1
     *
     * @return Tracking
     */
    public function setToAddress1(string $toAddress1): Tracking
    {
        $this->toAddress1 = $toAddress1;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToAddress2(): string
    {
        return $this->toAddress2;
    }
    
    /**
     * @param string $toAddress2
     *
     * @return Tracking
     */
    public function setToAddress2(string $toAddress2): Tracking
    {
        $this->toAddress2 = $toAddress2;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToAddress3(): string
    {
        return $this->toAddress3;
    }
    
    /**
     * @param string $toAddress3
     *
     * @return Tracking
     */
    public function setToAddress3(string $toAddress3): Tracking
    {
        $this->toAddress3 = $toAddress3;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToCity(): string
    {
        return $this->toCity;
    }
    
    /**
     * @param string $toCity
     *
     * @return Tracking
     */
    public function setToCity(string $toCity): Tracking
    {
        $this->toCity = $toCity;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getToCountry(): string
    {
        return $this->toCountry;
    }

    /**
     * @param string $toCountry
     *
     * @return Tracking
     */
    public function setToCountry(string $toCountry): Tracking
    {
        $this->toCountry = $toCountry;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getToName(): string
    {
        return $this->toName;
    }

    /**
     * @param string $toName
     *
     * @return Tracking
     */
    public function setToName(string $toName): Tracking
    {
        $this->toName = $toName;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getToZip(): string
    {
        return $this->toZip;
    }
    
    /**
     * @param string $toZip
     *
     * @return Tracking
     */
    public function setToZip(string $toZip): Tracking
    {
        $this->toZip = $toZip;
        
        return $this;
    }
}