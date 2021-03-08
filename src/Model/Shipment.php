<?php


namespace SLONline\DHLGlobalMail\Model;


class Shipment
{
    /**
     * Transport document issued by a carrier or a forwarder towards the business customer.
     * @var string
     */
    protected $awb;
    /**
     * The items associated with this shipment.
     * @var OrderItem[]
     */
    protected $items;
    /**
     * @var Order
     */
    protected $order;
    
    public function __construct(string $awb, array $items, ?Order $order = null)
    {
        $this->setAwb($awb);
        if ($order) {
            $this->setOrder($order);
        }
        $this->items = [];
        foreach ((array)$items as $itemData) {
            $this->items[] = OrderItem::createFromData($itemData);
        }
    }
    
    /**
     * @return Order
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }
    
    /**
     * @param Order $order
     *
     * @return Shipment
     */
    public function setOrder(?Order $order): Shipment
    {
        $this->order = $order;
        
        return $this;
    }
    
    public function toArray(): array
    {
        $item = [
            'awb' => $this->getAwb(),
        ];
        
        $item['items'] = array_map(function (OrderItem $item): array {
            return $item->toArray();
        }, $this->getItems());
        
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
     * @return Shipment
     */
    public function setAwb(string $awb): Shipment
    {
        $this->awb = $awb;
        
        return $this;
    }
    
    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
    
    /**
     * @param OrderItem[] $items
     *
     * @return Shipment
     */
    public function setItems(array $items): Shipment
    {
        $this->items = $items;
        
        return $this;
    }
}