<?php

namespace Database\Seeders;

use App\Models\PaymentPlatform;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentPlatformTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentPlatform::create([
            'name' => "PayPal",
            'image' => "img/payment-platforms/paypal.jpg"
        ]);
        PaymentPlatform::create([
            'name' => "Stripe",
            'image' => "img/payment-platforms/stripe.jpg"
        ]);
        PaymentPlatform::create([
            'name' => "MercadoPago",
            'image' => "img/payment-platforms/mercadopago.jpg"
        ]);
    }
}
