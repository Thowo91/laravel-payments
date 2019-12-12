<?php

namespace App\Http\Controllers;

use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Http\Request;

class PaypalPlusController extends Controller
{

    public function test() {

        $oAuthToken = new OAuthTokenCredential(
            config('paypal.credentials.sandbox.client_id'),
            config('paypal.credentials.sandbox.secret')
        );

        $apiContext = new ApiContext($oAuthToken);
        $apiContext->setConfig(
            config('paypal.settings')
        );

        $item1 = new Item();
        $item1->setName('Test Item')
            ->setCurrency('EUR')
            ->setQuantity(1)
            ->setPrice(15);

        $itemList = new ItemList();
        $itemList->setItems([$item1]);

        $payerInfo = new PayerInfo();
        $payerInfo->setEmail('test@mail.com')
            ->setFirstName('Max')
            ->setLastName('Mustermann');

        $address = new Address();
        $address->setPostalCode('63741')
            ->setCity('Aschaffenburg')
            ->setLine1('Magnolienweg 5')
            ->setCountryCode('DE');

        $payerInfo->setBillingAddress($address);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $payer->setPayerInfo($payerInfo);

        $amount = new Amount();
        $amount->setCurrency('EUR');
        $amount->setTotal(15);

        $transaction = new Transaction();
        $transaction->setAmount($amount);
        $transaction->setItemList($itemList);
        $transaction->setDescription('Payment Description');

        $redirectUrl = new RedirectUrls();
        $redirectUrl->setReturnUrl(route('paypalplus.return'));
        $redirectUrl->setCancelUrl(route('paypalplus.cancel'));

        $payment = new Payment();
        $payment->setIntent('payment');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrl);
        $payment->setTransactions([$transaction]);

        try {
            $payment = $payment->create($apiContext);

            $approvalUrl = $payment->getApprovalLink();

            return view('paypal.paypal', compact('approvalUrl'));

        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }

    }

    public function returnUrl(Request $request)
    {
        $response = [];
        $response['paymentId'] = $request->get('paymentId');
        $response['token'] = $request->get('token');
        $response['PayerID'] = $request->get('PayerID');

        return view('paypal.return', compact('response'));
    }

    public function cancelUrl(Request $request)
    {
        $response = [];
        $response['token'] = $request->get('token');

        return view('paypal.cancel', compact('response'));
    }

    public function paymentInfo()
    {
        $oAuthToken = new OAuthTokenCredential(
            config('paypal.credentials.sandbox.client_id'),
            config('paypal.credentials.sandbox.secret')
        );

        $apiContext = new ApiContext($oAuthToken);
        $apiContext->setConfig(
            config('paypal.settings')
        );

        $payment = new Payment();
        $info = $payment->get('PAYID-LXZAKTY98G95380V1071823Y', $apiContext);

        return view('paypal.paymentInfo', compact('info'));
    }

}
