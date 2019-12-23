@extends('layouts.basic')


@section('title', 'PaypalPlus')

@section('content')
    <h1>PaypalPlus</h1>
    <div id="ppplus"></div>

    <script src="https://www.paypalobjects.com/webstatic/ppplus/ppplus.min.js" type="text/javascript"></script>

    <script type="application/javascript">
        var ppp = PAYPAL.apps.PPP({
            "approvalUrl": "{!! $approvalUrl !!}",
            "placeholder": "ppplus",
            "mode": "sandbox",
            "country": "DE",
            "language": "de_DE",
        });
    </script>
@endsection
