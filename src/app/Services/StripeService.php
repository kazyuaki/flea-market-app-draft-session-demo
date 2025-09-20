<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeService
{
    public function createCheckoutSession(User $user, Item $item, string $payment_method_type, int $payment_method_code)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::create([
            'payment_method_types' => [$payment_method_type],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.complete', ['item' => $item->id, 'method' => $payment_method_code]),
            'cancel_url' => route('purchase.cancel', ['item' => $item->id]),
            'customer_email' => $user->email,
        ]);
    }
}
