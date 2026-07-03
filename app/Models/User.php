<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Billing\LedgerTransaction;
use App\Models\Billing\SubscriptionTier;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
/**
 * @property float $wallet_balance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Billing\LedgerTransaction[] $ledgerTransactions
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Billing
    public function ledgerTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(LedgerTransaction::class); // это уже окончательно проведенная банком операция
    }

    // Billing
    public function activeSubscriptions(): \Illuminate\Database\Eloquent\Relations\BelongsToMany {
        return $this->belongsToMany(SubscriptionTier::class, 'subscription_user')
            ->withPivot('expires_at')->withTimestamps();
    } // 'subscription_user' - 3-тя звязуюча таблиця

    // Billing
    // Accessor to dynamically calculate real-time wallet balance safely
    public function walletBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->ledgerTransactions()->sum('amount'),
        );
    }
}
