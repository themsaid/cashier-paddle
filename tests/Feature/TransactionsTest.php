<?php

namespace Tests\Feature;

use Laravel\Paddle\Transaction;
use Money\Currency;
use Tests\Fixtures\User;

class TransactionsTest extends FeatureTestCase
{
    public function test_we_can_retrieve_all_transactions_for_billable_customers()
    {
        $customer = $this->createCustomer();

        $transactions = $customer->transactions();

        $this->assertCount(1, $transactions);
        $this->assertSame('0.00', $transactions->first()->amount);
    }

    public function test_it_can_returns_its_amount_and_currency()
    {
        $customer = new User(['paddle_id' => 1]);
        $transaction = new Transaction($customer, [
            'user' => ['user_id' => 1],
            'amount' => '12.45',
            'currency' => 'EUR',
        ]);

        $this->assertSame('€12.45', $transaction->amount());
        $this->assertSame('12.45', $transaction->rawAmount());
        $this->assertInstanceOf(Currency::class, $transaction->currency());
        $this->assertSame('EUR', $transaction->currency()->getCode());
    }

    public function test_it_can_returns_its_subscription()
    {
        $customer = $this->createCustomer();
        $subscription = $customer->subscriptions()->create([
            'name' => 'default',
            'paddle_id' => 244,
            'paddle_plan' => 2323,
            'paddle_status' => 'active',
            'quantity' => 1,
        ]);
        $transaction = new Transaction($customer, [
            'user' => ['user_id' => $customer->paddleId()],
            'is_subscription' => true,
            'subscription' => [
                'subscription_id' => 244,
            ],
        ]);

        $this->assertTrue($subscription->is($transaction->subscription()));
    }
}
