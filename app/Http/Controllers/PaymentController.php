<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('home')->with('error', 'Invalid payment session');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                // Update payment status
                $payment = Payment::where('payment_intent_id', $session->payment_intent)->first();
                
                if ($payment) {
                    $payment->update(['status' => 'completed']);

                    // Extend file expiration
                    $file = $payment->file;
                    $file->update([
                        'expires_at' => $file->expires_at->addMinutes($payment->duration),
                        'is_premium' => true,
                    ]);

                    return redirect()->route('home')->with('success', 'Payment successful! File retention has been extended.');
                }
            }

            return redirect()->route('home')->with('error', 'Payment verification failed');

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('home')->with('info', 'Payment was cancelled');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                
                // Update payment status
                $payment = Payment::where('payment_intent_id', $session->payment_intent)->first();
                
                if ($payment) {
                    $payment->update(['status' => 'completed']);

                    // Extend file expiration
                    $file = $payment->file;
                    $file->update([
                        'expires_at' => $file->expires_at->addMinutes($payment->duration),
                        'is_premium' => true,
                    ]);
                }
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                
                // Update payment status
                $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();
                
                if ($payment) {
                    $payment->update(['status' => 'failed']);
                }
                break;

            default:
                // Unexpected event type
                return response()->json(['error' => 'Unexpected event type'], 400);
        }

        return response()->json(['status' => 'success']);
    }
}
