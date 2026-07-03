<?php

namespace App\Models\Billing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionTier extends Model { // Тарифний план
    protected $fillable = ['name', 'price', 'duration_days'];

    public function users(): BelongsToMany { // withPivot = с данними из промежуточной таблиці
        return $this->belongsToMany(User::class, 'subscription_user')->withPivot('expires_at')->withTimestamps();
    } // belongsToMany = бо може належити не тільки одному юзеру. а багатьом
}

