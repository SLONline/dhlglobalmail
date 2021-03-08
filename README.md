# DHL Global Mail

This is a PHP library that provides a simple way to communicate with the DHL Global Mail API. It was created because there
were no simple alternatives that follow good object-oriented code practices.

## Example

```php
use SLONline\DHLGlobalMail\Client;
use SLONline\DHLGlobalMail\Model\Order;
use SLONline\DHLGlobalMail\Model\OrderItem;
use SLONline\DHLGlobalMail\Model\Shipment;
use SLONline\DHLGlobalMail\Model\Tracking;
use SLONline\DHLGlobalMail\Exception\DHLGlobalMailRequestException;

$client = new Client('your_client_id', 'your_client_secret');

// Create a order and label
try {
    $orer = $client->createOrder([
            'customerEkp' => '9012345678',
            'orderStatus' => Order::STATUS_FINALIZE,
            'paperwork'   => [
                'contactName'     => "Max Mustermann",
                'awbCopyCount'    => 3,
                'jobReference'    => "Job ref",
                'pickupType'      => Paperwork::PICKUP_TYPE_CUSTOMER_DROP_OFF,
                "pickupLocation"  => "Mustergasse 12",
                "pickupDate"      => date('Y-m-d'),
                "pickupTimeSlot"  => Paperwork::TIME_SLOT_MIDDAY,
                "telephoneNumber" => "+4935120681234",
            ],
            'items'       => [
                [
                    "product"             => OrderItem::PRODUCT_GPP,
                    "serviceLevel"        => OrderItem::SERVICE_LEVEL_PRIORITY,
                    "recipient"           => "Alfred J. Quack",
                    "addressLine1"        => "Mustergasse 12",
                    "addressLine2"        => "Hinterhaus",
                    "addressLine3"        => "1. Etage",
                    "city"                => "Dresden",
                    "destinationCountry"  => "DE",
                    "custRef"             => "#REF-2361890-AB",
                    "recipientPhone"      => "+4935120681234",
                    "recipientEmail"      => "alfred.j.quack@somewhere.eu",
                    "postalCode"          => "01432",
                    "shipmentGrossWeight" => 1200,
                    "returnItemWanted"    => false,
                    "shipmentNaturetype"  => OrderItem::SHIPMENT_NATURE_TYPE_SALE_GOODS,
                    'shipmentCurrency'    => 'EUR',
                    "shipmentAmount"      => 100,
                ],
                [
                    "product"             => OrderItem::PRODUCT_GPP,
                    "serviceLevel"        => OrderItem::SERVICE_LEVEL_PRIORITY,
                    "recipient"           => "One Person",
                    "addressLine1"        => "180 St Kilda Rd",
                    "city"                => "Melbourne",
                    "destinationCountry"  => "AU",
                    "custRef"             => "#455",
                    "recipientPhone"      => "+32112122",
                    "recipientEmail"      => "someone@somewhere.eu",
                    "postalCode"          => "VIC 3006",
                    "shipmentGrossWeight" => 120,
                    "returnItemWanted"    => false,
                    "shipmentNaturetype"  => OrderItem::SHIPMENT_NATURE_TYPE_SALE_GOODS,
                    'shipmentCurrency'    => 'EUR',
                    'contents'            => [
                        [
                            'contentPieceAmount'      => 1,
                            'contentPieceDescription' => 'test book',
                            'contentPieceHsCode'      => '49019900',
                            'contentPieceNetweight'   => 120,
                            'contentPieceOrigin'      => 'NL',
                            'contentPieceValue'       => '12.50',
                        ],
                    ],
                ],
                [
                    "product"             => OrderItem::PRODUCT_GPP,
                    "serviceLevel"        => OrderItem::SERVICE_LEVEL_PRIORITY,
                    "recipient"           => "Apple Park",
                    "addressLine1"        => "One Apple Park Way",
                    "city"                => "Cupertino",
                    "state"               => 'CA',
                    "destinationCountry"  => "US",
                    "custRef"             => "#455",
                    "recipientPhone"      => "+321232131",
                    "recipientEmail"      => "someone@somewhere.eu",
                    "postalCode"          => "95014",
                    "shipmentGrossWeight" => 120,
                    "returnItemWanted"    => false,
                    "shipmentNaturetype"  => OrderItem::SHIPMENT_NATURE_TYPE_SALE_GOODS,
                    'shipmentCurrency'    => 'EUR',
                    'shipmentAmount'      => 12.5,
                    'contents'            => [
                        [
                            'contentPieceAmount'      => 1,
                            'contentPieceDescription' => 'test book',
                            'contentPieceHsCode'      => '49019900',
                            'contentPieceNetweight'   => 120,
                            'contentPieceOrigin'      => 'NL',
                            'contentPieceValue'       => '12.50',
                        ],
                    ],
                ],
            ],
        ]
    );

    $awb = $order->getShipments()[0]->getAwb();

    $awbPdf = $client->getAwbLabelPDF($awb);
    $itemsPDF = $client->getItemLabelsPDF($awb);

    var_dump($order, $awbPdf);
} catch (DHLGlobalMailRequestException $exception) {
    echo $exception->getMessage();
}

```

## Installation
`composer require slonline/dhlglobalmail`
