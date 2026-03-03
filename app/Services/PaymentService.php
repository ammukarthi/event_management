<?php

namespace App\Services;

class PaymentService
{
    public function process(float $amount): array
    {
        // Simulate random success or failure
        $success = rand(0, 1);

        if ($success) {
            return [
                'status' => true,
                'transaction_id' => 'TXN' . rand(10000, 99999),
                'message' => 'Payment successful'
            ];
        }

        return [
            'status' => false,
            'message' => 'Payment failed. Please try again.'
        ];
    }
}