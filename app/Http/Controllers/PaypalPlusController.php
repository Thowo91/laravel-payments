<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Order;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use Illuminate\Http\Request;

class PaypalPlusController extends Controller
{

    /**
     * @return array
     */
    public function config()
    {
        $config = [];

        $oAuthToken = new OAuthTokenCredential(
            config('paypal.credentials.sandbox.client_id'),
            config('paypal.credentials.sandbox.secret')
        );
        $config['oAuthToken'] = $oAuthToken;

        $apiContext = new ApiContext($oAuthToken);
        $apiContext->setConfig(
            config('paypal.settings')
        );
        $config['apiContext'] = $apiContext;

        return $config;
    }

    /**
     * @return Factory|View
     */
    public function test()
    {

        $config = $this->config();

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
        $payment->setIntent('sale');
        $payment->setPayer($payer);
        $payment->setRedirectUrls($redirectUrl);
        $payment->setTransactions([$transaction]);

        try {
            $payment = $payment->create($config['apiContext']);

            $approvalUrl = $payment->getApprovalLink();

            return view('paypal.paypal', compact('approvalUrl'));

        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }

    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function returnUrl(Request $request)
    {
        $response = [];
        $response['paymentId'] = $request->get('paymentId');
        $response['token'] = $request->get('token');
        $response['PayerID'] = $request->get('PayerID');

        $this->execution($response['paymentId'], $response['PayerID']);

        return view('paypal.return', compact('response'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function cancelUrl(Request $request)
    {
        $response = [];
        $response['token'] = $request->get('token');

        return view('paypal.cancel', compact('response'));
    }

    /**
     * @param $paymentId
     * @return Factory|View
     */
    public function paymentInfo($paymentId)
    {
        $config = $this->config();

        $payment = new Payment();
        $info = $payment->get($paymentId, $config['apiContext']);

        return view('paypal.paymentInfo', compact('info'));
    }

    /**
     * @return Factory|View
     */
    public function paymentAll()
    {
        $config = $this->config();

        $payment = new Payment();

        $params = [
            'count' => 20,
            'start_index' => 0,
            'sort_by' => 'create_time'
        ];

        $all = $payment->all($params, $config['apiContext']);

        return view('paypal.all', compact('all'));
    }

    /**
     * @param $paymentId
     * @param $payerId
     */
    public function execution($paymentId, $payerId)
    {
        $config = $this->config();

        $payment = Payment::get($paymentId, $config['apiContext']);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        try {

            $result = $payment->execute($execution, $config['apiContext']);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }
    }

    public function capture()
    {
        $config = $this->config();
        // get Order with OrderId
        $order = new Order();

        $amount = new Amount();
        $amount->setCurrency('EUR')
            ->setTotal(15);

        $captureDetails = new Capture();
        $captureDetails->setAmount($amount);

        try {
            $result = $order->capture($captureDetails, $config['apiContext']);
        } catch (PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        }
    }

}
