<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LedgerTransaction extends Model {
    protected $fillable = ['user_id', 'amount', 'type'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class); // належить тільки одному юзеру
    }
}
