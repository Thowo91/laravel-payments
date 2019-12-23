@extends('layouts.basic')


@section('title', 'Klarna Return Confirmation')

@section('content')
    {!! $confirmationData['html_snippet'] !!}
@endsection
