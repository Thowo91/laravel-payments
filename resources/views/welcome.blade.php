@extends('layouts.basic')


@section('title', 'Payments')

@section('content')
    <div class="title m-b-md">
        Payments
    </div>

    <div class="links">
        <a href="{{ route('paypalplus') }}">PaypalPlus</a>
        <a href="{{ route('paypalplus.paymentall') }}">Paypal All</a>
        <a href="{{ route('klarna.checkout') }}">Klarna Checkout</a>
        <a href="{{ route('stripe') }}">Stripe</a>
    </div>
@endsection
