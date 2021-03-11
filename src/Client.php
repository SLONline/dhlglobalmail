<?php


namespace SLONline\DHLGlobalMail;


use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use SLONline\DHLGlobalMail\Exception\DHLGlobalMailRequestException;
use SLONline\DHLGlobalMail\Model\Order;
use SLONline\DHLGlobalMail\Model\OrderItem;
use SLONline\DHLGlobalMail\Model\Shipment;
use SLONline\DHLGlobalMail\Model\Tracking;
use function GuzzleHttp\default_user_agent;

class Client
{
    const API_BASE_URL = 'https://api.deutschepost.com';
    
    const API_TEST_BASE_URL = 'https://api-qa.deutschepost.com';
    
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
                $response     = $guzzleClient->get('v1/auth/accesstoken');
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
     * For a given item a label is generated or retrieved from the cache.
     *
     * @param string $itemId
     *
     * @return string
     * @throws DHLGlobalMailRequestException
     */
    public function getItemLabelPDF(string $itemId): string
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/items/' . $itemId . '/label',
                ['headers' => ['Accept' => 'application/pdf']]);
            
            return (string)$response->getBody();
        } catch (RequestException $exception) {
            throw $this->parseRequestException($exception, 'Could not receive item label from DHL Global Mail.');
        }
    }
    
    /**
     * For a given shipment awb an awb labels is generated or retrieved from the cache.
     *
     * @param string $awb
     *
     * @return string
     * @throws DHLGlobalMailRequestException
     */
    public function getItemLabelsPDF(string $awb): string
    {
        try {
            $response = $this->guzzleClient->get('dpi/shipping/v1/shipments/' . $awb . '/itemlabels',
                ['headers' => ['Accept' => 'application/pdf']]);
            
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
}