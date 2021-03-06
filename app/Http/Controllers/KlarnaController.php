<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Klarna\Rest\Checkout\Order;
use Klarna\Rest\Transport\ConnectorInterface;
use Klarna\Rest\Transport\GuzzleConnector;

class KlarnaController extends Controller
{

    /**
     * @return GuzzleConnector
     */
    public function connector()
    {
        $merchantId = env('KlARNA_MERCHANT_ID');
        $sharedSecret = env('KLARNA_SHARED_SECRET');
        $apiEndpoint = ConnectorInterface::EU_TEST_BASE_URL;

        $connector = GuzzleConnector::create(
            $merchantId,
            $sharedSecret,
            $apiEndpoint
        );

        return $connector;
    }

    /**
     * @return Factory|View
     */
    public function checkout()
    {
        $connector = $this->connector();

        $address = [
            'title' => 'Herr',
            'given_name' => 'Max',
            'family_name' => 'Mustermann',
            'email' => 'test@mail.com',
            'street_address' => 'Magnolienweg 5',
            'postal_code' => '63741',
            'city' => 'Aschaffenburg',
            'country' => 'DE'
        ];

        $data = [
            'billing_address' => $address,
            'shipping_address' => $address,
            'purchase_country' => 'DE',
            'purchase_currency' => 'EUR',
            'locale' => 'de-DE',
            'order_amount' => '100000', // cents
            'order_tax_amount' => 0,
            'order_lines' => [
                [
                    'type' => 'physical',
                    'name' => 'Tomatoes',
                    'quantity' => 10,
                    'unit_price' => 6000,
                    'total_amount' => 60000,
                    'tax_rate' => 0,
                    'total_tax_amount' => 0,
                ],
                [
                    'type' => 'physical',
                    'name' => 'Potatoes',
                    'quantity' => 2,
                    'unit_price' => 20000,
                    'total_amount' => 40000,
                    'tax_rate' => 0,
                    'total_tax_amount' => 0,
                ]
            ],
            "merchant_urls" => [
                "terms" => "http://merchant.com/tac.php", // agb
                "checkout" => route('klarna.checkout'), // on error on klarna site
                "confirmation" => 'http://payments.local/klarna/return/confirmation?klarna_order_id={checkout.order.id}', // confirmation page
                "push" => "http://localhost/kco/push.php?klarna_order_id={checkout.order.id}" // push
            ]
        ];

        try {

            $checkout = new Order($connector);
            $checkoutData = $checkout->create($data);
//            dd($checkoutData);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . '\n';
        }

        return view('klarna.checkout', compact('checkoutData'));

    }

    /**
     * @return Factory|View
     */
    public function returnConfirmation()
    {
        $connector = $this->connector();

        $id = $_GET['klarna_order_id'];

        $checkout = new Order($connector, $id);
        $confirmationData = $checkout->fetch();

        // acknowlege after recieve the push from klarna/save in our system
        $orderManagement = new \Klarna\Rest\OrderManagement\Order($connector, $id);
        $orderManagement->acknowledge();

        // capture the amount of the order
//        $capture = $orderManagement->createCapture(['captured_amount' => 100000]);

        return view('klarna.return.confirmation', compact('confirmationData'));
    }
}
