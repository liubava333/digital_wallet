<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\PurchaseSubscriptionRequest;
use App\Jobs\GenerateSubscriptionInvoiceJob;
use App\Models\Billing\SubscriptionTier;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller {

    // Explicit eager loading utilizing with() to prevent N+1 queries entirely
    public function getDashboardData(): JsonResponse {
        $user = auth()->user();

        $userData = $user->load([
            'ledgerTransactions',
            'activeSubscriptions'
        ]);

        return response()->json([
            'wallet_balance' => $user->wallet_balance,
            'transactions' => $userData->ledgerTransactions,
            'subscriptions' => $userData->activeSubscriptions,
            'available_tiers' => SubscriptionTier::all()
        ]);
    }

    public function purchaseSubscription(PurchaseSubscriptionRequest $request): JsonResponse {
        $user = auth()->user();
        $tier = SubscriptionTier::findOrFail($request->validated()['subscription_tier_id']);

        try {
            // Оборачиваем всё в транзакцию
            DB::transaction(function () use ($user, $tier) {

                // Блокируем строку пользователя в БД для чтения и обновления (Защита от Race Condition)
                // Это обновит данные модели $user актуальными значениями из базы
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();
                // Бизнес-проверка внутри транзакции на основе заблокированных данных
                if ($lockedUser->wallet_balance < $tier->price) {
                    // Выбрасываем исключение, чтобы транзакция автоматически откатилась
                    throw new \Exception('Insufficient funds in your wallet ledger.', 400);
                }
                // 2. Создаем запись в бухгалтерской книге (Ledger)
                $lockedUser->ledgerTransactions()->create([
                    'amount' => -$tier->price,
                    'type' => 'subscription_purchase'
                ]);
                // 3. Привязываем подписку
                $lockedUser->activeSubscriptions()->attach($tier->id, [
                    'expires_at' => now()->addDays($tier->duration_days)
                ]);
            });

        } catch (\Exception $e) {
            // Если это наша контролируемая ошибка нехватки средств
            if ($e->getCode() === 400) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
            // Для любых других системных сбоев
            return response()->json(['error' => 'Something went wrong.'], 500);
        }


        // 3. Dispatch heavy asynchronous worker job to Redis queue
        GenerateSubscriptionInvoiceJob::dispatch($user, $tier);

        return response()->json(['message' => 'Subscription processed successfully!']);
    }
}

