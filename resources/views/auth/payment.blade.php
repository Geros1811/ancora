@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('css/auth_register.css') }}">
@endsection

@section('content')
    <div class="auth-container">
        <h2>Selecciona un plan</h2>
        <form action="{{ route('payment.submit') }}" method="POST">
            @csrf
            <input type="hidden" name="name" value="{{ session('user_data.name') }}">
            <input type="hidden" name="email" value="{{ session('user_data.email') }}">
            <input type="hidden" name="password" value="{{ session('user_data.password') }}">
            <input type="hidden" name="role" value="{{ session('user_data.role') }}">

            <div class="product-list">
                @foreach ($plans as $plan)
                    <div class="product-item" onclick="selectPlan('{{ $plan->id }}', this)" id="product_{{ $plan->id }}">
                        <label for="plan_{{ $plan->id }}">
                            {{ $plan->nombre }} - ${{ number_format($plan->precio, 2) }}
                        </label>
                        <p>{{ $plan->descripcion }}</p>
                    </div>
                @endforeach
            </div>
            <input type="hidden" id="selected_plan" name="plan" value="">
            <button type="button" onclick="showPaymentOptions()">Pagar</button>

            <div id="payment_options" class="payment-options" style="display: none;">
                <h3>Opciones de Pago</h3>
                <p>Implement payment options here (e.g., Stripe Elements, PayPal, etc.)</p>
                <div id="card-element">
                    <!-- A Stripe Element will be inserted here. -->
                </div>

                <!-- Used to display form errors. -->
                <div id="card-errors" role="alert"></div>
                <button type="submit">Confirmar Pago</button>
            </div>
        </form>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('js/payment.js') }}"></script>
    <script>
        var stripe = Stripe('pk_test_51R9s3fQHwUxpM816hdg1Rb5Gyp7SQbAFQSYArFBs6LufxSQmXK9QiNJ8CnPEmIV3B2pXjapZFn5AAP0zfvyyyXT100QO9mPnCI');
        var elements = stripe.elements();
        var style = {
            base: {
                color: '#fff',
                lineHeight: '18px',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };
        var card = elements.create('card', { style: style });
        card.mount('#card-element');

        var form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            stripe.createToken(card).then(function(result) {
                if (result.error) {
                    // Inform the user if there was an error
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    // Send the token to your server
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.querySelector('form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
    </script>
@endsection
