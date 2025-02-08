<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MercadoPagoServices
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $key;
    protected $secret;
    protected $baseCurrency;
    protected $converter;

    public function __construct(CurrencyConversionService $converter)
    {

        $this->baseUri =  config('services.mercadopago.base_uri');
        $this->key =  config('services.mercadopago.key');
        $this->secret =  config('services.mercadopago.secret');
        $this->baseCurrency =  config('services.mercadopago.base_currency');
        $this->converter = $converter;
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $queryParams['access_token'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        return $this->secret;
    }

    public function handlePayment(Request $request)
    {
        /*
        $request->validate([
            'card_network' => 'required',
            'card_token' => 'required',
            'email' => 'required',
            'first_name' => 'required|string',
            'document_number' => 'required|string',
        ]);

        */

        $payment = $this->createPixPayment();

        return view('pagamento', [
            'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
            'qr_code' => $payment->point_of_interaction->transaction_data->qr_code,
            'ticket_url' => $payment->point_of_interaction->transaction_data->ticket_url,
        ]);




        $payment = $this->createPayment(
            $request->value,
            $request->currency,
            $request->card_network,
            $request->card_token,
            $request->email,
            $request->first_name,
            $request->document_number,
        );


        if ($payment->status === 'approved') {
            $name = $payment->payer->first_name;
            $currency = strtoupper($payment->currency_id);
            $amount = number_format($payment->transaction_amount, 0, ',', '.');

            $originalAmount = $request->value;
            $originalCurrency = strtoupper($request->currency);

            return redirect()
                ->route('home')
                ->withSuccess(['payment' => "Thanks, {$name}, we received your {$originalAmount}{$originalCurrency} payment.
                 ({$amount}{$currency})"]);
        }

        // Caso contrário, capture o erro de forma mais detalhada
        Log::error('Payment failed', [
            'status' => $payment->status,
            'status_detail' => $payment->status_detail,
            'payer' => $payment->payer,
            'transaction_amount' => $payment->transaction_amount,
        ]);

        dd($payment);

        return redirect()
            ->route('home')
            ->withErrors('we were unable to confirm you payment, try again, please');
    }



    public function handleApproval() {}

    public function createPayment($value, $currency, $cardNetwork, $cardToken, $email, $firstName, $documentNumber, $installments = 1)
    {

        $idempotencyKey = uniqid('payment_', true);

        $response =  $this->makeRequest(
            'POST',
            '/v1/payments',
            [],
            [
                'payer' => [
                    'email' => $email,
                    'first_name' => $firstName,
                    'identification' => [
                        'type' => 'CPF',  // Ou outro tipo de identificação
                        'number' => $documentNumber,
                    ]
                ],
                'binary_mode' => true,
                'transaction_amount' => round($value * $this->resolveFactor($currency)),
                'payment_method_id' => $cardNetwork,
                'token' => $cardToken,
                'installments' => $installments,
                'statement_descriptor' => config('app.name'),

            ],
            [
                'X-Idempotency-Key' => $idempotencyKey,
            ],
            $isJsonRequest = true,
        );
        Log::info('Response from Mercado Pago:', ['response' => $response]);


        return $response;
    }


    public function createPixPayment()
{
    $idempotencyKey = uniqid('payment_', true);

    // Configurando os dados do pagamento via Pix
    $response = $this->makeRequest(
        'POST',
        '/v1/payments', // Endpoint de pagamentos via Pix
        [],
        [
            "transaction_amount" => 12.34,
            "date_of_expiration" => "2025-12-25T19:30:00.000-03:00",
            "payment_method_id" => "pix",
            "external_reference" => "1234",
            "notification_url" => "http://example.com.br/notification",
            "metadata" => [
                "order_number" => "order_1724857044"
            ],
            "description" => "PEDIDO NOVO - VIDEOGAME",
            "payer" => [
                "first_name" => "Joao",
                "last_name" => "Silva",
                "email" => "antonioivo.3@gmail.com",
                "identification" => [
                    "type" => "CPF",
                    "number" => "19119119100"
                ],
                "address" => [
                    "zip_code" => "06233-200",
                    "street_name" => "Av. das Nações Unidas",
                    "street_number" => "3003",
                    "neighborhood" => "Bonfim",
                    "city" => "Osasco",
                    "federal_unit" => "SP"
                ]
            ],
            "additional_info" => [
                "items" => [
                    [
                        "id" => "1941",
                        "title" => "Ingresso Antecipado",
                        "description" => "Natal Iluminado 2019",
                        "picture_url" => null,
                        "category_id" => "Tickets",
                        "quantity" => 1,
                        "unit_price" => 100.00,
                        "event_date" => "2019-12-25T19:30:00.000-03:00"
                    ]
                ],
                "payer" => [
                    "first_name" => "Nome",
                    "last_name" => "Sobrenome",
                    "is_prime_user" => "1",
                    "is_first_purchase_online" => "1",
                    "last_purchase" => "2019-10-25T19:30:00.000-03:00",
                    "phone" => [
                        "area_code" => "11",
                        "number" => "987654321"
                    ],
                    "address" => [
                        "zip_code" => "06233-200",
                        "street_name" => "Av. das Nações Unidas",
                        "street_number" => "3003"
                    ],
                    "registration_date" => "2013-08-06T09:25:04.000-03:00"
                ],
                "shipments" => [
                    "express_shipment" => "0",
                    "pick_up_on_seller" => "1",
                    "receiver_address" => [
                        "zip_code" => "95630000",
                        "street_name" => "são Luiz",
                        "street_number" => "15",
                        "floor" => "12",
                        "apartment" => "123"
                    ]
                ]
            ]
        ],
        [
            'X-Idempotency-Key' => $idempotencyKey,
            'Authorization' => 'Bearer ' . $this->key,
        ],
        $isJsonRequest = true
    );

    Log::info('Response from Mercado Pago for Pix payment:', ['response' => $response]);

    return $response;
}




    public function capturePayment($approvalId)
    {
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json',
            ]

        );
    }


    public function resolveFactor($currency)
    {
        return $this->converter
            ->convertCurrency($currency, $this->baseCurrency);
    }
}
