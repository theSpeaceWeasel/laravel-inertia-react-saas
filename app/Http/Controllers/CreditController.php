<?php

namespace App\Http\Controllers;

use App\Http\Resources\FeatureResource;
use App\Http\Resources\PackageResource;
use App\Models\Feature;
use App\Models\Package;
use App\Models\Transaction;
// use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CreditController extends Controller
{
    public function index()
    {
        $packages = Package::all();
        $features = Feature::all();

        return inertia("Credit/index", [
            'packages' => PackageResource::collection($packages),
            'features' => FeatureResource::collection($features),
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function buyCredits(Package $package)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
        $checkout_session = $stripe->checkout->sessions->create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'unit_amount' => $package->price * 100,
                    'product_data' => [
                        'name' => $package->name,
                        ],
                        ],
                        'quantity' => 1,
                        ],
                        ],
                        'mode' => 'payment',
                        'success_url' => route('credit.success', [], true), //true for absolute urls
                        'cancel_url' => route('credit.cancel', [], true),
        ]);

        Transaction::create([
            'package_id' => $package->id,
            'user_id' => Auth::id(),
            'credits' => $package->credits,
            'status' => 'pending',
            'price' => $package->price,
            'session_id' => $checkout_session->id
        ]);
        return redirect($checkout_session->url);
    }

    public function success()
    {
        return to_route('credit.index')
        ->with('success', 'You have successfully purchased credits!');
    }
    public function cancel()
    {
        return to_route('credit.index')
        ->with('error', 'There was an error during the purchase of credits!');
    }
    public function webhook()
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_KEY');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            // Invalid payload
            // Return a 400 error response
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid payload
            // Return a 400 error response
            return response()->json(['error' => 'Invalid payload'], 400);
        }
        // Handle the event
        if ($event->type === 'checkout.session.completed') {
            $checkout_session = $event->data->object;

            $transaction = Transaction::where('session_id', $checkout_session->id)->first();
            if($transaction && $transaction->status === "pending") {
                $transaction->status = 'paid';
                $transaction->save();
                $transaction->user->available_credits += $transaction->credits;
                $transaction->user->save();
            }


        }
        return response()->json(['success' => 'Webhook received'], 200);
    }
}