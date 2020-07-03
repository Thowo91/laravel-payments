<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;

class StripeController extends Controller
{

    public function index()
    {

        $stripe_config = config('stripe');
        Stripe::setApiKey(config('stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'customer_email' => 'customer@example.com',
            'payment_method_types' => ['card', 'ideal', 'giropay'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => 'Handy',
                    ],
                    'unit_amount' => 2000,
                ],
                'quantity' => 2,
            ],
            [
                'price_data' => [
                    'currency' => 'EUR',
                    'product_data' => [
                        'name' => 'Caseable',
                    ],
                    'unit_amount' => 2990,
                ],
                'quantity' => 1,
            ],
                ],
            'mode' => 'payment',
            'success_url' => route('stripe.sucess').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel'),
        ]);

        return view('stripe.index', compact('session', 'stripe_config'));
    }

    public function sucessUrl()
    {

    }

    public function cancelUrl(Request $request)
    {

        return view('stripe.cancel', compact('request'));
    }

}
