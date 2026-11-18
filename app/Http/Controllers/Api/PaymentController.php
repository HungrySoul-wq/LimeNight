<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Account;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $payments = Payment::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return PaymentResource::collection($payments);
    }

    public function store(StorePaymentRequest $request): PaymentResource
    {
        $payment = DB::transaction(function () use ($request) {
            $user = $request->user();
            $data = $request->validated();

            // Создаём игровой счёт, если его ещё нет.
            // Блокируем пользователя, чтобы избежать одновременного создания счёта.
            $user->newQuery()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            Account::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'status' => 'active',
                ]
            );

            $account = Account::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $account->status === 'active',
                409,
                'Игровой счёт заблокирован.'
            );

            $amount = round((float) $data['amount'], 2);

            abort_if(
                $amount <= 0,
                422,
                'Сумма пополнения должна быть больше нуля.'
            );

            $account->increment('balance', $amount);

            return Payment::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'balance_top_up',
                'payment_method' => $data['payment_method'],
                'status' => 'success',
            ]);
        }, 3);

        return new PaymentResource($payment);
    }
}
