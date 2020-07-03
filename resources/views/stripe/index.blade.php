@extends('layouts.basic')

@section('title', 'Stripe')

@section('content')

    <script src="https://js.stripe.com/v3/"></script>

    <button id="checkout-button">
        Checkout
    </button>

    <script>
        var checkoutButton = document.getElementById('checkout-button');

        var stripe = Stripe('{!! $stripe_config['public'] !!}');

        checkoutButton.addEventListener('click', function() {
            stripe.redirectToCheckout({
                // Make the id field from the Checkout Session creation API response
                // available to this file, so you can provide it as argument here
                // instead of the {CHECKOUT_SESSION_ID} placeholder.
                sessionId: '{!! $session->id !!}'
            }).then(function (result) {
                console.log(result.error.message)
                // If `redirectToCheckout` fails due to a browser or network
                // error, display the localized error message to your customer
                // using `result.error.message`.
            });
        });
    </script>

@endsection
