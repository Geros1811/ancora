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
            // Fetch the plans from the database
            $plans = \App\Models\Plan::all(['id', 'nombre', 'precio', 'stripe_price_id']); // Incluye stripe_price_id
            return view('auth.payment', compact('plans')); // Pasa los planes a la vista
        }
        return redirect()->route('obra.create')->with('success', 'User registered successfully.');
    }

    public function submitPayment(Request $request)
    {
        $request->validate([
            'plan' => 'required', // Solo el plan es obligatorio
        ]);

        $userData = session('user_data');

        if (!$userData) {
            return redirect()->route('register')->with('error', 'Session expired. Please register again.');
        }

        $planId = $request->plan;

        try {
            // Fetch the plan from the database
            $plan = \App\Models\Plan::find($planId);

            if (!$plan) {
                return back()->with('error', 'Plan not found.');
            }

            // Verifica que los datos de la sesión sean válidos
            if (empty($userData['name']) || empty($userData['email']) || empty($userData['password']) || empty($userData['role'])) {
                return back()->with('error', 'Invalid session data. Please try again.');
            }

            // Crear el usuario con los datos básicos
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'], // Ya está encriptado en el registro
                'role' => $userData['role'],
                'created_by' => $userData['created_by'],
            ]);

            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

            // Si se proporciona un payment_method_id, procesa el pago
            if ($request->has('payment_method_id')) {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $plan->precio * 100, // Convertir a centavos
                    'currency' => 'mxn',
                    'payment_method' => $request->payment_method_id, // ID del método de pago
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                ]);

                if ($paymentIntent->status !== 'succeeded') {
                    return back()->with('error', 'Payment failed. Please try again.');
                }

                $user->stripe_subscription_id = $paymentIntent->id; // ID del PaymentIntent como referencia
            }

            // Guardar los datos adicionales en el usuario
            $user->plan_id = $planId; // Asigna el ID del plan
            $user->subscription_ends_at = now()->addMonth(); // Fecha de expiración de la suscripción
            $user->is_active = true; // Marca al usuario como activo
            $user->save();

            session()->forget('user_data');

            return redirect()->route('login')->with('success', 'Registration successful! Please log in.');

        } catch (\Stripe\Exception\CardException $e) {
            // Manejar errores de pago
            return back()->with('error', $e->getError()->message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}