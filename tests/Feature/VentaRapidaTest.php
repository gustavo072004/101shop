<?php
namespace Tests\Feature;
use App\Models\Producto;
use App\Models\User;
use Database\Seeders\ProductoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class VentaRapidaTest extends TestCase {
    use RefreshDatabase;
    public function test_private_sale_decrements_one_and_cannot_go_negative():void {
        $this->seed(ProductoSeeder::class);
        $p=Producto::first();$p->update(['stock'=>1]);
        $this->actingAs(User::factory()->create(['id_rol'=>2]));
        $this->get('/catalogo')->assertSee('Vender ahora')->assertDontSee('wa.me');
        $this->get('/catalogo/'.$p->id_producto)->assertSee('Vender ahora')->assertDontSee('wa.me');
        $this->from('/catalogo')->post('/catalogo/'.$p->id_producto.'/vender')->assertRedirect('/catalogo')->assertSessionHas('success','Venta exitosa');
        $this->assertSame(0,$p->fresh()->stock);
        $this->post('/catalogo/'.$p->id_producto.'/vender')->assertSessionHas('error');
        $this->assertSame(0,$p->fresh()->stock);
    }
    public function test_guest_cannot_sell_and_public_keeps_whatsapp():void {
        $this->seed(ProductoSeeder::class);config(['catalogo.whatsapp'=>'50370627910']);
        $p=Producto::first();$p->update(['stock'=>2]);
        $this->post('/catalogo/'.$p->id_producto.'/vender')->assertRedirect('/login');
        $this->assertSame(2,$p->fresh()->stock);
        $this->get('/publico/catalogo')->assertSee('wa.me')->assertDontSee('Vender ahora');
        $this->actingAs(User::factory()->create());$p->update(['estado'=>false]);
        $this->post('/catalogo/'.$p->id_producto.'/vender')->assertSessionHas('error');
        $this->assertSame(2,$p->fresh()->stock);
    }
}

