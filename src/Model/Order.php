<?php


namespace SLONline\DHLGlobalMail\Model;


class Order
{
    public const STATUS_OPEN = 'OPEN';
    public const STATUS_FINALIZE = 'FINALIZE';
    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_FINALIZE,
    ];
    
    /**
     * ID of the Order.
     * @var int
     */
    protected $orderId;
    /**
     * Deutsche Post Customer Account number (EKP) of the customer who wants to create an order.
     * @var string
     */
    protected $customerEkp;
    /**
     * The status of the order. It indicates if an order shall be held open to add more items at a later point in time.
     * "OPEN" means items can be added later, "FINALIZE" means that the order shall be processed immediately.
     * @var string[OPEN,FINALIZE]
     */
    protected $orderStatus;
    /**
     * The items associated with this order.
     * @var OrderItem[]
     */
    protected $items;
    /**
     * @var Paperwork
     */
    protected $paperwork;
    /**
     * @var Shipment[]
     */
    protected $shipments;
    
    public function __construct(array $data)
    {
        $this->customerEkp = (string)$data['customerEkp'];
        $this->orderStatus = (string)$data['orderStatus'];
        if (\array_key_exists('paperwork', $data)) {
            $this->paperwork = new Paperwork($data['paperwork']);
        }
        
        $this->items = [];
        if (\array_key_exists('items', $data) && isset($data['items'])) {
            foreach ((array)$data['items'] as $itemData) {
                $this->items[] = OrderItem::createFromData($itemData);
            }
        }
        
        $this->shipments = [];
        if (\array_key_exists('shipments', $data) && isset($data['shipments'])) {
            foreach ($data['shipments'] as $shipment) {
                if (\array_key_exists('awb', $shipment) && \array_key_exists('items', $shipment)) {
                    $this->shipments[] = new Shipment($shipment['awb'], $shipment['items'], $this);
                }
            }
        }
    }
    
    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }
    
    /**
     * @param int $orderId
     *
     * @return Order
     */
    public function setOrderId(int $orderId): Order
    {
        $this->orderId = $orderId;
        
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getShipments()
    {
        return $this->shipments;
    }
    
    /**
     * @param mixed $shipments
     *
     * @return Order
     */
    public function setShipments($shipments): Order
    {
        $this->shipments = $shipments;
        
        return $this;
    }
    
    /**
     * Deutsche Post Customer Account number (EKP) of the customer who wants to create an order.
     *
     * @return string
     */
    public function getCustomerEkp(): string
    {
        return $this->customerEkp;
    }
    
    /**
     * Set Deutsche Post Customer Account number (EKP) of the customer who wants to create an order.
     *
     * @param string $customerEkp
     */
    public function setCustomerEkp(string $customerEkp): Order
    {
        $this->customerEkp = $customerEkp;
        
        return $this;
    }
    
    /**
     * The status of the order
     *
     * @return string
     */
    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }
    
    /**
     * Set the status of the order
     *
     * @param string $orderStatus
     */
    public function setOrderStatus(string $orderStatus): Order
    {
        $this->orderStatus = $orderStatus;
        
        return $this;
    }
    
    /**
     * @return Paperwork
     */
    public function getPaperwork(): Paperwork
    {
        return $this->paperwork;
    }
    
    /**
     * @param Paperwork $paperwork
     */
    public function setPaperwork(Paperwork $paperwork): Order
    {
        $this->paperwork = $paperwork;
        
        return $this;
    }
    
    public function toArray(): array
    {
        $item = [
            'customerEkp' => $this->customerEkp,
            'orderStatus' => $this->orderStatus,
        ];
        
        if ($this->paperwork instanceof Paperwork) {
            $item['paperwork'] = $this->paperwork->toArray();
        }
        $item['items'] = array_map(function (OrderItem $item): array {
            return $item->toArray();
        }, $this->getItems());
        
        if ($this->shipments instanceof Shipment) {
            $item['shipment'] = $this->shipments->toArray();
        }
        
        return $item;
    }
    
    /**
     * The items associated with this order.
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * Set the items associated with this order.
     *
     * @param array|OrderItem $items
     */
    public function setItems($items): Order
    {
        $this->items = $items;
        
        return $this;
    }
    
    /**
     * Add the item associated with this order.
     *
     * @param OrderItem $item
     */
    public function addItem(OrderItem $item): Order
    {
        $this->items[] = $item;
        
        return $this;
    }
}