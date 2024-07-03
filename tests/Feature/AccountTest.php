<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccountTest extends TestCase
{
    /**
     * Test get balance for non-existing account
     * @test
     */
    public function it_get_balance_for_non_existing_account(): void
    {
        $response = $this->get('/balance?account_id=1234');
        $response->assertStatus(404);
        $response->assertContent('0');
    }

    /**
     * Test create account with initial balance
     * @test
     */
    public function it_create_account_with_initial_balance(): void
    {
        $response = $this->post('/event', [
            'type' => 'deposit',
            'destination' => 100,
            'amount' => 10
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'destination' => [
                'id' => 100,
                'balance' => 10
            ]
        ]);
    }

    /**
     * Test deposit into existing account
     * @test
     */
    public function it_deposit_into_existing_account(): void
    {
        $response = $this->post('/event', [
            'type' => 'deposit',
            'destination' => 100,
            'amount' => 10
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'destination' => [
                'id' => 100,
                'balance' => 20
            ]
        ]);
    }

    /**
     * Test get balance for existing account
     * @test
     */
    public function it_get_balance_for_existing_account(): void
    {
        $response = $this->get('/balance?account_id=100');
        $response->assertStatus(200);
        $response->assertContent('20');
    }

    /**
     * Test withdraw from non existing account
     * @test
     */
    public function it_withdraw_from_non_existing_account(): void
    {
        $response = $this->post('/event', [
            'type' => 'withdraw',
            'origin' => 200,
            'amount' => 10
        ]);

        $response->assertStatus(404);
        $response->assertContent('0');
    }

    /**
     * Test withdraw from existing account
     * @test
     */
    public function it_withdraw_from_existing_account(): void
    {
        $response = $this->post('/event', [
            'type' => 'withdraw',
            'origin' => 100,
            'amount' => 5
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => [
                'id' => 100,
                'balance' => 15
            ]
        ]);
    }

    public function it_transfer_from_existing_account(): void
    {
        $response = $this->post('/event', [
            'type' => 'transfer',
            'origin' => 100,
            'amount' => 15,
            'destination' => 300
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'origin' => [
                'id' => 100,
                'balance' => 0
            ],
            'destination' => [
                'id' => 300,
                'balance' => 15
            ]
        ]);
    }
}
