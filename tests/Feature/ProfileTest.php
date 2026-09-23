<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ProfileTest extends TestCase {
    use RefreshDatabase;
    public function test_profile_page_is_displayed(): void {
        $this->actingAs(User::factory()->create())->get('/perfil')->assertOk();
    }
    public function test_profile_information_can_be_updated_without_changing_role(): void {
        $user=User::factory()->create(['id_rol'=>2]);
        $this->actingAs($user)->patch('/perfil', [
            'nombres'=>'Nombre', 'apellidos'=>'Apellido', 'telefono'=>'70123456',
            'username'=>'usuario.prueba', 'email'=>'profile@example.com', 'id_rol'=>1,
        ])->assertSessionHasNoErrors()->assertRedirect('/perfil');
        $this->assertSame('Nombre', $user->fresh()->nombres);
        $this->assertSame('profile@example.com', $user->fresh()->email);
        $this->assertEquals(2, $user->fresh()->id_rol);
    }
    public function test_account_deletion_is_not_a_self_service_route(): void {
        $user=User::factory()->create();
        $this->actingAs($user)->delete('/perfil')->assertStatus(405);
        $this->assertNotNull($user->fresh());
    }
}
