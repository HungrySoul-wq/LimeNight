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

            $account = Account::where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $account->status === 'active',
                409,
                'Игровой счёт заблокирован.'
            );

            $amount = (float) $data['amount'];

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
