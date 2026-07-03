<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseSubscriptionRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'subscription_tier_id' => 'required|exists:subscription_tiers,id'
        ];
    }
}
