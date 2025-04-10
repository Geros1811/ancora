<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'membresia_id' => 'required|exists:membresias,id',
            'payment_method' => 'required',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $user = auth()->user();
        $membresia = Membresia::find($request->membresia_id);

        $customer = Customer::create([
            'email' => $user->email,
            'name' => $user->name,
            'payment_method' => $request->payment_method,
        ]);

        $subscription = Subscription::create([
            'customer' => $customer->id,
            'items' => [['price' => $membresia->stripe_price_id]],
        ]);

        $user->update(['membresia_id' => $membresia->id]);

        return redirect()->route('home')->with('success', 'Suscripción completada.');
    }
}
