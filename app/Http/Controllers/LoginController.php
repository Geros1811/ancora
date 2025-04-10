<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    // Mostrar formulario de Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Manejar Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && password_verify($request->password, $user->password)) {
            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    // Cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // Mostrar formulario de registro (solo accesible por enlace directo)
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Manejar registro de arquitectos
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|in:arquitecto,maestro_obra,cliente', // Validar el rol
        ]);
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_by' => Auth::id() ?? 1,
        ];

        $request->session()->put('user_data', $userData);

        if ($request->role === 'arquitecto') {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            $products = \Stripe\Product::all(['limit' => 10]); // Adjust limit as needed
            $prices = [];
            foreach ($products->data as $product) {
                $prices[$product->id] = \Stripe\Price::all(['product' => $product->id, 'limit' => 1]);
            }

            return view('auth.payment', compact('products', 'prices'));
        }

        return redirect()->route('obra.create')->with('success', 'User registered successfully.');
    }

    public function submitPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required',
        ]);

        $userData = session('user_data');

        if (!$userData) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }

        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
            'created_by' => $userData['created_by'],
        ]);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $planId = $request->plan;

        try {
            $prices = \Stripe\Price::all(['product' => $planId, 'limit' => 1]);
            $amount = $prices->data[0]->unit_amount;

            // Charge the user using Stripe
            \Stripe\Charge::create([
                'amount' => $amount, // Amount in cents
                'currency' => 'mxn',
                'source' => $request->stripeToken, // Token obtained with Stripe.js
                'description' => 'Payment for plan ' . $planId,
            ]);

            session()->forget('user_data');

            return redirect()->route('login')->with('success', 'Registration successful! Please log in.');

        } catch (\Exception $e) {
            // Handle payment errors
            return back()->with('error', $e->getMessage());
        }
    }
}
