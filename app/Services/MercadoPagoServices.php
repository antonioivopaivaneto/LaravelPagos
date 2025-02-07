<?php

namespace App\Services;

use App\Traits\ConsumesExternalServices;
use Illuminate\Http\Request;

class MercadoPagoServices
{
    use ConsumesExternalServices;

    protected $baseUri;
    protected $key;
    protected $secret;
    protected $baseCurrency;

    public function __construct()
    {

        $this->baseUri =  config('services.mercadopago.base_uri');
        $this->key =  config('services.mercadopago.key');
        $this->secret =  config('services.mercadopago.secret');
        $this->baseCurrency =  config('services.mercadopago.base_currency');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken() {}

    public function handlePayment(Request $request) {}

    public function handleApproval() {}



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


    public function resolveFactor($currency) {}
}
