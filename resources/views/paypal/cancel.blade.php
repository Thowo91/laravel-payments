@extends('layouts.basic')


@section('title', 'PaypalPlus')

@section('content')
    <h1 style="color: red">Cancel</h1>
    <p>token: {{ $response['token'] }}</p>
@endsection
