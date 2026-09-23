<?php
namespace Tests\Feature;
use App\Models\Producto;
use App\Models\User;
use App\Models\CategoriaProducto;
use Database\Seeders\ProductoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductoTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp():void { parent::setUp(); $this->seed(ProductoSeeder::class); \App\Models\Marca::create(['nombre'=>'Marca','estado'=>true]); config(['catalogo.whatsapp'=>'50370627910']); }
    private function data():array { return ['codigo'=>'TEST-001','nombre'=>'Producto de prueba','id_marca'=>\App\Models\Marca::first()->id_marca,'modelo'=>'Modelo','descripcion'=>'Descripción de prueba','id_categoria'=>CategoriaProducto::first()->id_categoria,'stock'=>4,'precio_ingreso'=>'17.31','valor_real'=>'20.42','valor_final'=>'35.67','publicado'=>1]; }
    public function test_admin_crud_and_all_views():void {
        $this->actingAs(User::factory()->create());
        foreach(['/productos','/productos/create','/categorias','/categorias/create'] as $path) $this->get($path)->assertOk();
        $this->post('/productos',$this->data())->assertSessionHasNoErrors()->assertRedirect();
        $p=Producto::where('codigo','TEST-001')->firstOrFail();
        $this->get('/productos/'.$p->id_producto)->assertOk()->assertSee('17.31')->assertSee('20.42');
        $this->get('/productos/'.$p->id_producto.'/edit')->assertOk();
        $this->put('/productos/'.$p->id_producto,[...$this->data(),'nombre'=>'Producto actualizado'])->assertSessionHasNoErrors();
        $this->assertSame('Producto actualizado',$p->fresh()->nombre);
        $this->delete('/productos/'.$p->id_producto)->assertRedirect();
        $this->assertFalse($p->fresh()->estado);
        $this->get('/publico/catalogo/'.$p->id_producto)->assertNotFound();
        $this->patch('/productos/'.$p->id_producto.'/reactivar')->assertRedirect();
        $this->assertTrue($p->fresh()->estado);
        $this->assertFalse($p->fresh()->publicado);
        $this->assertDatabaseHas('bitacoras',['modulo'=>'Productos','accion'=>'CREAR']);
    }
    public function test_validation_rejects_bad_values_and_duplicates():void {
        $this->actingAs(User::factory()->create());
        $this->post('/productos',[...$this->data(),'precio_ingreso'=>-1,'valor_real'=>'abc','valor_final'=>'1.234','stock'=>1.5,'id_categoria'=>99999])->assertSessionHasErrors(['precio_ingreso','valor_real','valor_final','stock','id_categoria']);
        $this->post('/productos',[...$this->data(),'codigo'=>'PR-001'])->assertSessionHasErrors('codigo');
        $this->post('/productos',[])->assertSessionHasErrors(['codigo','nombre','descripcion','stock']);
        $this->post('/categorias',['nombre'=>'Electrónica'])->assertSessionHasErrors('nombre');
    }
    public function test_public_only_sees_final_price_and_has_no_write_access():void {
        $p=Producto::create($this->data());
        foreach(['/publico/catalogo','/publico/catalogo/'.$p->id_producto] as $path) {
            $this->get($path)->assertOk()->assertSee('35.67')->assertDontSee('17.31')->assertDontSee('20.42')->assertDontSee('Precio de ingreso')->assertDontSee('Panel privado');
        }
        foreach(['/productos','/productos/create','/catalogo','/categorias','/usuarios','/bitacora'] as $path) $this->get($path)->assertRedirect('/login');
        $this->post('/productos',$this->data())->assertRedirect('/login');
        $this->delete('/publico/catalogo/'.$p->id_producto)->assertStatus(405);
    }
    public function test_vendedor_cannot_view_costs_or_administer():void {
        $p=Producto::create($this->data());
        $this->actingAs(User::factory()->create(['id_rol'=>2]));
        $this->get('/catalogo/'.$p->id_producto)->assertOk()->assertSee('35.67')->assertDontSee('17.31')->assertDontSee('20.42');
        $this->get('/productos/'.$p->id_producto)->assertForbidden();
        $this->post('/productos',$this->data())->assertForbidden();
        $this->put('/productos/'.$p->id_producto,$this->data())->assertForbidden();
        $this->delete('/productos/'.$p->id_producto)->assertForbidden();
    }
    public function test_disabled_user_is_logged_out():void {
        $this->actingAs(User::factory()->create(['estado'=>false]))->get('/catalogo')->assertRedirect('/login'); $this->assertGuest();
    }
    public function test_search_categories_and_each_whatsapp_link():void {
        $this->get('/publico/catalogo?q=PR-002')->assertOk()->assertSee('Teclado Mecánico RGB')->assertDontSee('Cargador USB-C');
        $p=Producto::where('codigo','PR-005')->first();
        $this->get('/publico/catalogo?categoria='.$p->id_categoria)->assertSee('Cargador USB-C')->assertDontSee('Teclado Mecánico RGB');
        foreach(Producto::all() as $p) {
            $this->get('/publico/catalogo/'.$p->id_producto)->assertOk()->assertSee($p->whatsappUrl());
            $this->get('/imagenes/productos/'.$p->id_producto)->assertOk();
            $this->assertStringContainsString('https://wa.me/50370627910?', $p->whatsappUrl());
            $this->assertStringContainsString($p->nombre,rawurldecode($p->whatsappUrl()));
            $this->assertStringContainsString($p->codigo,rawurldecode($p->whatsappUrl()));
        }
        $p->update(['publicado'=>false]); $this->get('/publico/catalogo/'.$p->id_producto)->assertNotFound();
        $this->get('/imagenes/productos/'.$p->id_producto)->assertNotFound();
    }
    public function test_inactive_category_hides_products():void {
        $p=Producto::first();$p->categoria->update(['estado'=>false]);
        $this->get('/publico/catalogo/'.$p->id_producto)->assertNotFound();
        $this->get('/publico/catalogo')->assertDontSee($p->nombre);
    }
    public function test_complete_category_crud_and_reactivation():void {
        $this->actingAs(User::factory()->create());
        $this->post('/categorias',['nombre'=>'Accesorios nuevos','descripcion'=>'Descripción'])->assertSessionHasNoErrors()->assertRedirect();
        $c=CategoriaProducto::where('nombre','Accesorios nuevos')->firstOrFail();
        $this->get('/categorias/'.$c->id_categoria.'/edit')->assertOk();
        $this->put('/categorias/'.$c->id_categoria,['nombre'=>'Accesorios editados','descripcion'=>'Actualizada'])->assertSessionHasNoErrors();
        $this->assertSame('Accesorios editados',$c->fresh()->nombre);
        $this->delete('/categorias/'.$c->id_categoria)->assertRedirect();
        $this->assertFalse($c->fresh()->estado);
        $this->patch('/categorias/'.$c->id_categoria.'/reactivar')->assertRedirect();
        $this->assertTrue($c->fresh()->estado);
    }
    public function test_image_validation_and_storage():void {
        $this->actingAs(User::factory()->create());
        $bad=\Illuminate\Http\UploadedFile::fake()->createWithContent('script.svg','<svg><script>alert(1)</script></svg>');
        $this->post('/productos',[...$this->data(),'imagen'=>$bad])->assertSessionHasErrors('imagen');
        $bytes=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aT1kAAAAASUVORK5CYII=');
        $image=\Illuminate\Http\UploadedFile::fake()->createWithContent('image.png',$bytes);
        $this->post('/productos',[...$this->data(),'imagen'=>$image])->assertSessionHasNoErrors();
        $p=Producto::where('codigo','TEST-001')->firstOrFail();
        $this->get('/imagenes/productos/'.$p->id_producto)->assertOk()->assertHeader('Content-Type','image/png')->assertContent($bytes);
    }
}
