@extends('layouts.basic')


@section('title', 'PaypalPlus')

@section('content')
    <h1 style="color: green">Return</h1>
    <p>paymentId: {{ $response['paymentId'] }}</p>
    <p><a href="{{ route('paypalplus.paymentinfo', [$response['paymentId']]) }}" target="_blank">Paypal PaymentInfo</a></p>
    <p>token: {{ $response['token'] }}</p>
    <p>PayerID: {{ $response['PayerID'] }}</p>
@endsection
