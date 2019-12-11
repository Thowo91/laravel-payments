<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Klarna\Rest\Checkout\Order;
use Klarna\Rest\Payments\Sessions;
use Klarna\Rest\Transport\ConnectorInterface;
use Klarna\Rest\Transport\GuzzleConnector;

class KlarnaController extends Controller
{
    public function test()
    {
        $merchantId = env('KlARNA_MERCHANT_ID');
        $sharedSecret = env('KLARNA_SHARED_SECRET');
        $apiEndpoint = ConnectorInterface::EU_TEST_BASE_URL;

        $connector = GuzzleConnector::create(
            $merchantId,
            $sharedSecret,
            $apiEndpoint
        );

        $address = [
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
            ]
        ];

        try {

            $session = new Sessions($connector);
            $session->create($data);

            $categories = '';
            foreach ($session['payment_method_categories'] as $category) {
                $categories .= $category['identifier'];
            }

//            dd($session);

        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . '\n';
        }

        return view('klarna.klarna', compact('session', 'categories'));

    }

    public function checkout()
    {
        $merchantId = env('KlARNA_MERCHANT_ID');
        $sharedSecret = env('KLARNA_SHARED_SECRET');
        $apiEndpoint = ConnectorInterface::EU_TEST_BASE_URL;

        $connector = GuzzleConnector::create(
            $merchantId,
            $sharedSecret,
            $apiEndpoint
        );

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
                "checkout" => "http://merchant.com/checkout.php?sid={checkout.order.id}",
                "confirmation" => "http://merchant.com/thankyou.php?sid={checkout.order.id}",
                "push" => "http://localhost/kco/push.php?sid={checkout.order.id}"
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
}
