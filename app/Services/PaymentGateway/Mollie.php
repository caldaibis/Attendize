<?php

namespace Services\PaymentGateway;

class Mollie
{

    CONST GATEWAY_NAME = 'Mollie';

    private $transaction_data;

    private $gateway;

    private $extra_params = [];

    public function __construct($gateway)
    {
        $this->gateway = $gateway;
        $this->options = [];

        $request = $gateway->fetchIssuers();
        $response = $request->send();
    }

    private function createTransactionData($order_total, $order_email, $event)
    {
        $returnUrl = route('showEventCheckoutPaymentReturn', [
            'event_id' => $event->id,
        ]);

        $this->transaction_data = [
            'amount' => $order_total,
            'currency' => $event->currency->code,
            'description' => 'Order for customer: ' . $order_email,
            'returnUrl' => $returnUrl,
            'receipt_email' => $order_email
        ];

        return $this->transaction_data;
    }

    public function startTransaction($order_total, $order_email, $event)
    {
        $this->createTransactionData($order_total, $order_email, $event);
        $response = $this->gateway->purchase($this->transaction_data)->send();

        return $response;
    }

    public function getTransactionData()
    {
        return $this->transaction_data;
    }

    public function extractRequestParameters($request)
    {
        foreach ($this->extra_params as $param) {
            if (!empty($request->get($param))) {
                $this->options[$param] = $request->get($param);
            }
        }
    }

    public function completeTransaction($ticket_order_data)
    {        
        $response = $this->gateway->completePurchase(['transaction_reference' => $ticket_order_data['transaction_id']])->send();
        return $response;
    }

    public function getAdditionalData(){}

    public function storeAdditionalData()
    {
        return false;
    }

    public function refundTransaction($order, $refund_amount, $refund_application_fee)
    {

        $request = $this->gateway->refund([
            'transactionReference' => $order->transaction_id,
            'amount' => $refund_amount,
            'refundApplicationFee' => $refund_application_fee
        ]);

        $response = $request->send();

        if ($response->isSuccessful()) {
            $refundResponse['successful'] = true;
        } else {
            $refundResponse['successful'] = false;
            $refundResponse['error_message'] = $response->getMessage();
        }

        return $refundResponse;
    }
}