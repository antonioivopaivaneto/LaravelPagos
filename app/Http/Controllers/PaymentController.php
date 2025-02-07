<?php

namespace App\Http\Controllers;

use App\Resolvers\PaymentPlatformResolver;
use App\Services\PaypalServices;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    protected $paymentPlatformResolver;
    public function __construct(PaymentPlatformResolver $paymentPlatformResolver)
    {
        $this->middleware('auth');
        $this->paymentPlatformResolver = $paymentPlatformResolver;
    }

    public function pay(Request $request)
    {


        if(!$request->type === 'pix'){

            $rules = [
                'value' => ['required','numeric','min:5'],
                'currency' => ['required','exists:currencies,iso'],
                'payment_platform' => ['required','exists:payment_platforms,id'],
            ];

            //dd($request->all());

            $request->validate($rules);

            $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService($request->payment_platform);

            session()->put('paymentPlatformId',$request->payment_platform);

        }



        $paymentPlatform = $this->paymentPlatformResolver
        ->resolveService($request->payment_platform);

        session()->put('paymentPlatformId',$request->payment_platform);






        return $paymentPlatform->handlePayment($request);
    }
    public function approved()
    {
        if(session()->has('paymentPlatformId')){
            $paymentPlatform = $this->paymentPlatformResolver
            ->resolveService(session()->get('paymentPlatformId'));

            return $paymentPlatform->handleApproval();
        }


        return redirect()
        ->route('home')
        ->withErrors('nao conseguimos aceesar o metodo');


    }
    public function cancelled()
    {
        return redirect()
            ->route('home')
            ->withErrors('ypu cancelled the payment');

    }
}
