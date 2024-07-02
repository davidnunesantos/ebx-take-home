<?php

namespace Tests\Feature;

use Tests\TestCase;

class ResetTest extends TestCase
{
    /**
     * Test reset
     */
    public function it_reset_state_before_starting()
    {
        $response = $this->post('/reset');
        $response->assertStatus(200);
    }
}
