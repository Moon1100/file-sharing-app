<?php

namespace App\Livewire;

use App\Models\File;
use App\Models\Payment;
use Livewire\Component;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;

class UpgradeComponent extends Component
{
    public $fileId = null;
    public $showModal = false;
    public $selectedPlan = null;

    protected $listeners = ['openUpgradeModal'];

    public $retentionPlans = [
        [
            'id' => '10min',
            'name' => '10 Minutes',
            'duration' => 10,
            'price' => 0.99,
            'description' => 'Extend retention by 10 minutes'
        ],
        [
            'id' => '30min',
            'name' => '30 Minutes',
            'duration' => 30,
            'price' => 1.99,
            'description' => 'Extend retention by 30 minutes'
        ],
        [
            'id' => '1hour',
            'name' => '1 Hour',
            'duration' => 60,
            'price' => 2.99,
            'description' => 'Extend retention by 1 hour'
        ],
        [
            'id' => '5hours',
            'name' => '5 Hours',
            'duration' => 300,
            'price' => 4.99,
            'description' => 'Extend retention by 5 hours'
        ],
        [
            'id' => '24hours',
            'name' => '24 Hours',
            'duration' => 1440,
            'price' => 9.99,
            'description' => 'Extend retention by 24 hours'
        ]
    ];

    public function openUpgradeModal($fileId)
    {
        $this->fileId = $fileId;
        $this->showModal = true;
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = collect($this->retentionPlans)->firstWhere('id', $planId);
    }

    public function checkout()
    {
        if (!$this->selectedPlan || !$this->fileId) {
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $file = File::findOrFail($this->fileId);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'File Retention Extension',
                            'description' => $this->selectedPlan['description'],
                        ],
                        'unit_amount' => (int)($this->selectedPlan['price'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel'),
                'metadata' => [
                    'file_id' => $file->id,
                    'duration' => $this->selectedPlan['duration'],
                    'plan_id' => $this->selectedPlan['id'],
                ],
            ]);

            // Create payment record
            Payment::create([
                'file_id' => $file->id,
                'amount' => $this->selectedPlan['price'],
                'duration' => $this->selectedPlan['duration'],
                'payment_intent_id' => $session->payment_intent,
                'status' => 'pending',
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            session()->flash('error', 'Payment setup failed: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedPlan = null;
        $this->fileId = null;
    }

    public function render()
    {
        return view('livewire.upgrade-component');
    }
}
