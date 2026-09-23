<?php
namespace Tests\Feature\Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class EmailVerificationTest extends TestCase {
    use RefreshDatabase;
    public function test_unconfigured_breeze_verification_routes_are_not_exposed(): void {
        $this->actingAs(User::factory()->create())->get('/verify-email')->assertNotFound();
        $this->post('/email/verification-notification')->assertNotFound();
    }
}
