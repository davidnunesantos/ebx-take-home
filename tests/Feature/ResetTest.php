<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResetTest extends TestCase
{
    /**
     * Test reset
     * @test
     */
    public function it_reset_state_before_starting()
    {
        $response = $this->post('/reset');
        $response->assertStatus(200);
        $response->assertContent('OK');
    }
}
