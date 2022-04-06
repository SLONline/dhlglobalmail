<?php


namespace SLONline\DHLGlobalMail;


use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use SLONline\DHLGlobalMail\Exception\DHLGlobalMailRequestException;
use SLONline\DHLGlobalMail\Model\Order;
use SLONline\DHLGlobalMail\Model\OrderItem;
use SLONline\DHLGlobalMail\Model\Paperwork;
use SLONline\DHLGlobalMail\Model\Shipment;
use SLONline\DHLGlobalMail\Model\Tracking;
use function GuzzleHttp\default_user_agent;

class Client
{
    const API_BASE_URL = 'https://api.dhl.com/';
    
    const API_TEST_BASE_URL = 'https://api-sandbox.dhl.com/';
    
    /** @var \GuzzleHttp\Client */
    protected $guzzleClient;
    
    /** @var string */
    protected $clientID;
    
    /** @var string */
    protected $clientSecret;
    
    protected $sandbox = false;
    
    protected $accessToken = null;
    protected $tokenType = null;
    protected $expiresAt = null;
    
    public function __construct(
        string $clientID,
        string $clientSecret,
        bool $sandbox = false
    ) {
        $this->clientID     = $clientID;
        $this->clientSecret = $clientSecret;
        $this->sandbox      = $sandbox;
        
        $this->getAccessToken();
        
        $clientConfig = [
            'base_uri' => $sandbox ? self::API_TEST_BASE_URL : self::API_BASE_URL,
            'timeout'  => 35,
            'headers'  => [
                'User-Agent'    => 'slonline/dhlglobalmail ' . default_user_agent(),
                'Authorization' => $this->tokenType . ' ' . $this->accessToken,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
        ];
        
        $this->guzzleClient = new \GuzzleHttp\Client($clientConfig);
    }
    
    protected function getAccessToken()
    {
        if (empty($this->accessToken) || $this->expiresAt <= \time()) {
            $clientConfig = [
                'base_uri' => $this->sandbox ? self::API_TEST_BASE_URL : self::API_BASE_URL,
                'timeout'  => 35,
                'auth'     => [
                    $this->clientID,
                    $this->clientSecret,
                ],
            ];
            
            try {
                $guzzleClient = new \GuzzleHttp\Client($clientConfig);
                $response     = $guzzleClient->get('dpi/v1/auth/accesstoken');
                $data         = \json_decode($response->getBody()->getContents(), true);
                
                if (\array_key_exists('access_token', $data) && \array_key_exists('token_type', $data)) {
                    $this->accessToken = $data['access_token'];
                    $this->tokenType   = $data['token_type'];
                    $this->expiresAt   = time() + (int)$data['expires_in'];
                }
            } catch (RequestException $exception) {
                throw $this->parseRequestException(
                    $exception,
                    'OAuth error occurred.'
                );
            }
        }
    }
    
    protected function parseRequestException(
        RequestException $exception,
        string $defaultMessage
    ): DHLGlobalMailRequestException {
        $message = $defaultMessage;
        $code    = DHLGlobalMailRequestException::CODE_UNKNOWN;
        
        $responseCode    = null;
        $responseMessage = null;
        if ($exception->hasResponse()) {
            $responseData = json_decode((string)$exception->getResponse()->getBody(), true);
            $responseCode = $exception->getCode();
            if (\is_array($responseData['messages'])) {
                $responseMessage = \implode('\n', $responseData['messages']);
            }
        }
        
        if ($exception instanceof ConnectException) {
            $message = 'Could not contact DHL Global Mail API.';
            $code    = DHLGlobalMailRequestException::CODE_CONNECTION_FAILED;
        }
        
        // Precondition failed, parse response message to determine code of exception
        if ($exception->getCode() === 401) {
            $message = 'Invalid client_id/client_secret key combination or invalid OAuth token.';
            $code    = DHLGlobalMailRequestException::CODE_UNAUTHORIZED;
        }
        
        return new DHLGlobalMailRequestException($message, $code, $exception, $responseCode, $responseMessage);
    }
    
    /**
     * Get tracking information of all items of a shipment (identified by the given awb).
     *
     * @param string $awb
     *
     * @return Tracking[]
     * @throws DHLGlobalMailRequestException
     */
    public function getTrackingShipment(string $awb): array
    {
        try {
            $trackings = [];
            $response  = $this->guzzleClient->get('dpi/tracking/v1/trackings/awb/' . $awb);
            $response  = json_decode((string)$response->getBody(), true);
            foreach ($response as $trackingInfo) {
                $trackings[] = Tracking::createFromData($trackingInfo);
            }
            
            return $trackings;
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
    
    /**
     * For a given shipment awb all available data will be send.
     *
     * @param string $awb
     *
     * @return Shipment
     * @throws DHLGlobalMailRequestException
     */
    public function getShipmentAwb(string $awb): Shipment
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/shipments/' . $awb);
            $response = json_decode((string)$response->getBody(), true);
            
            $order = null;
            if (\array_key_exists('order', $response)) {
                $order = new Order($response['order']);
            }
            
            return new Shipment($response['awb'], $response['items'], $order);
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
    
    /**
     * Validate OrderItems, required by efiliale
     *
     * @param Order $order
     *
     * @return bool
     */
    public function validateOrderItems(Order $order): bool
    {
        try {
            $response = $this->guzzleClient->post('dpi/shipping/v1/validation', [
                'json' => [
                    'customerEkp' => $order->getCustomerEkp(),
                    'items'       => array_map(function (OrderItem $item): array {
                        return $item->toArray();
                    }, $order->getItems()),
                ],
            ]);
            
            if ($response->getStatusCode() == 200) {
                return true;
            }
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Validation error');
        }
        
        return false;
    }
    
    /**
     * Validate OrderItem, required by efiliale
     *
     * @param string    $customerEkp
     * @param OrderItem $item
     *
     * @return bool
     * @throws DHLGlobalMailRequestException
     */
    public function validateOrderItem($customerEkp, OrderItem $item): bool
    {
        try {
            $response = $this->guzzleClient->post('dpi/shipping/v1/validation', [
                'json' => [
                    'customerEkp' => $customerEkp,
                    'items'       => [
                        $item->toArray()
                    ],
                ],
            ]);
            
            if ($response->getStatusCode() == 200) {
                return true;
            }
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Validation error');
        }
        
        return false;
    }
    
    /**
     * For a given shipment awb an awb labels is generated or retrieved from the cache.
     *
     * @param string $awb
     *
     * @return string
     * @throws DHLGlobalMailRequestException
     */
    public function getAwbLabelPDF(string $awb): string
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/shipments/' . $awb . '/awblabels',
                ['headers' => ['Accept' => 'application/pdf']]);
            
            return (string)$response->getBody();
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item label from DHL Global Mail.');
        }
    }
    
    /**
     * Searches the order for the given orderId.
     *
     * @param string $orderId
     *
     * @return Order
     */
    public function getOrder(string $orderId): Order
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/orders/' . $orderId);
            
            return new Order(json_decode((string)$response->getBody(), true));
        } catch (RequestException $exception) {
            throw $this->parseRequestException(
                $exception,
                'An error occurred while fetching order from the DHL Global Mail API.'
            );
        }
    }
    
    /**
     * You get all information about a given item.
     *
     * @param string $itemId
     *
     * @return OrderItem
     * @throws DHLGlobalMailRequestException
     */
    public function getItem(string $itemId): OrderItem
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/items/' . $itemId);
            
            return OrderItem::createFromData(json_decode((string)$response->getBody(), true));
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
    
    /**
     * Add a new item to a open order.
     *
     * @param string    $orderID
     * @param OrderItem $orderItem
     *
     * @return OrderItem
     * @throws DHLGlobalMailRequestException
     */
    public function addItem(string $orderID, OrderItem $orderItem): OrderItem
    {
        return $this->addItems($orderID, [$orderItem])[0];
    }
    
    /**
     * Add a new items to a open order.
     *
     * @param string      $orderID
     * @param OrderItem[] $ordreItems
     *
     * @return OrderItem[]
     * @throws DHLGlobalMailRequestException
     */
    public function addItems(string $orderID, array $ordreItems): array
    {
        try {
            $response = $this->guzzleClient->post('dpi/shipping/v1/orders/' . $orderID . '/items',
                [
                    'json' => array_map(function (OrderItem $item): array {
                        return $item->toArray();
                    }, $ordreItems),
                ]);
            $response = json_decode((string)$response->getBody(), true);
            return array_map(function ($item): OrderItem {
                return OrderItem::createFromData($item);
            }, $response);
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
    
    /**
     * Deletes the item for the specified item id.
     * This operation only works for items attached to orders that are in the OPEN state.
     * Called on items of orders in the FINALIZED state leads to an 404 error (an item with the desired attributes
     * cannot be found).
     *
     * @param string $orderItemID
     *
     * @return bool
     * @throws DHLGlobalMailRequestException
     */
    public function deleteItem(string $orderItemID): bool
    {
        try {
            $response = $this->guzzleClient->delete('dpi/shipping/v1/items/' . $orderItemID);
            
            if ($response->getStatusCode() == 200) {
                return true;
            }
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
        
        return false;
    }
    
    /**
     * Creates a new order based on the given data
     *
     * @param Order $order
     *
     * @return Order
     */
    public function createOrder(Order $order): Order
    {
        try {
            $response = $this->guzzleClient->post('dpi/shipping/v1/orders', ['json' => $order->toArray()]);
            
            return new Order(json_decode((string)$response->getBody(), true));
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not create order in DHL Global Mail.');
        }
    }
    
    /**
     * Finalize the given order
     *
     * @param string    $orderID
     * @param Paperwork $paperwork
     *
     * @return Order
     * @throws DHLGlobalMailRequestException
     */
    public function finalizeOrder(string $orderID, Paperwork $paperwork): Order
    {
        try {
            $response = $this->guzzleClient->post('dpi/shipping/v1/orders/' . $orderID . '/finalization',
                ['json' => $paperwork->toArray()]);
            
            return new Order(json_decode((string)$response->getBody(), true));
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not create order in DHL Global Mail.');
        }
    }
    
    /**
     * For a given item a label is generated or retrieved from the cache.
     * Format can be:
     *      application/pdf
     *      application/pdf+singlepage
     *      application/pdf+singlepage+6x4
     *      application/zpl
     *      application/zpl+6x4
     *      application/zpl+rotated
     *      application/zpl+rotated+6x4
     *      image/png
     *      image/png+6x4
     *      application/json
     *
     * @param string $itemId
     * @param string $format
     *
     * @return string
     * @throws DHLGlobalMailRequestException
     */
    public function getItemLabelPDF(string $itemId, string $format = 'application/pdf'): string
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/items/' . $itemId . '/label',
                ['headers' => ['Accept' => $format]]);
            
            return (string)$response->getBody();
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item label from DHL Global Mail.');
        }
    }
    
    /**
     * For a given shipment awb an awb labels is generated or retrieved from the cache.
     * Format can be:
     *      application/pdf
     *      application/pdf+singlepage
     *      application/pdf+singlepage+6x4
     *      application/zpl
     *      application/zpl+6x4
     *      application/zpl+rotated
     *      application/zpl+rotated+6x4
     *      image/png
     *      image/png+6x4
     *      application/json
     *
     * @param string $awb
     * @param string $format
     *
     * @return string
     * @throws DHLGlobalMailRequestException
     */
    public function getItemLabelsPDF(string $awb, string $format = 'application/pdf'): string
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/shipments/' . $awb . '/itemlabels',
                ['headers' => ['Accept' => $format]]);
            
            return (string)$response->getBody();
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item label from DHL Global Mail.');
        }
    }
    
    /**
     * Get all tracking information of for the item (identified by the given barcode).
     *
     * @param string $barcode
     *
     * @return Tracking
     * @throws DHLGlobalMailRequestException
     */
    public function getTrackings(string $barcode): Tracking
    {
        try {
            $response = $this->guzzleClient->get('dpi/tracking/v1/trackings/' . $barcode);
            
            return Tracking::createFromData(json_decode((string)$response->getBody(), true));
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
    
    /**
     * Get shipments for an order
     * Searches shipments attached to an given order. Answers with a not found status (404) if order does not exist or
     * if there are no shipmments attached to this order.
     *
     * @param string $orderID
     *
     * @return Shipment[]
     * @throws DHLGlobalMailRequestException
     */
    public function getOrderShipments(string $orderID):array
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/orders/' . $orderID . '/shipments');
            $response = json_decode((string)$response->getBody(), true);
            
            $shipments = [];
            foreach ($response as $item) {
                $shipments[] = new Shipment(
                    $item['awb'],
                    $item['items'],
                    new Order($item['order'])
                );
            }
            
            return $shipments;
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item data from DHL Global Mail.');
        }
    }
}