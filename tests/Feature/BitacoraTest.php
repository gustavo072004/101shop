<?php

namespace Tests\Feature;

use App\Models\Bitacora;
use App\Models\CategoriaProducto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BitacoraTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Role::where('nombre', 'Administrador')->exists()) {
            Role::create(['id_rol' => 1, 'nombre' => 'Administrador']);
        }
        if (! Role::where('nombre', 'Vendedor')->exists()) {
            Role::create(['id_rol' => 2, 'nombre' => 'Vendedor']);
        }
    }

    public function test_invitado_no_puede_ver_la_bitacora(): void
    {
        $response = $this->get('/bitacora');

        $response->assertRedirect('/login');
    }

    public function test_vendedor_no_puede_acceder_a_la_bitacora_y_recibe_403(): void
    {
        $vendedor = User::factory()->create([
            'id_rol' => 2,
        ]);

        $response = $this->actingAs($vendedor)->get('/bitacora');

        $response->assertStatus(403);
    }

    public function test_administrador_puede_ver_la_bitacora(): void
    {
        $admin = User::factory()->create([
            'id_rol' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/bitacora');

        $response->assertStatus(200);
        $response->assertSee('Bitácora de auditoría');
    }

    public function test_crear_categoria_registra_en_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $response = $this->actingAs($admin)->post('/categorias', [
            'nombre'      => 'Electrónica y Hogar',
            'descripcion' => 'Aparatos electrónicos y del hogar',
        ]);

        $response->assertRedirect('/categorias');

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'CREAR',
            'modulo' => 'Categorías',
        ]);

        $registro = Bitacora::where('accion', 'CREAR')->first();
        $this->assertNotNull($registro);
        $this->assertStringContainsString('Electrónica y Hogar', $registro->descripcion);
        $this->assertEquals($admin->id_usuario, $registro->id_usuario);
    }

    public function test_modificar_categoria_registra_cambios_en_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $categoria = CategoriaProducto::create([
            'nombre'      => 'Bebidas Frías',
            'descripcion' => 'Gaseosas y jugos',
            'estado'      => true,
        ]);

        $response = $this->actingAs($admin)->put("/categorias/{$categoria->id_categoria}", [
            'nombre'      => 'Bebidas Frías y Calientes',
            'descripcion' => 'Gaseosas, jugos y cafés',
        ]);

        $response->assertRedirect('/categorias');

        $registro = Bitacora::where('accion', 'MODIFICAR')
            ->where('modulo', 'Categorías')
            ->first();

        $this->assertNotNull($registro);
        $this->assertEquals('Bebidas Frías', $registro->detalles['antes']['nombre']);
        $this->assertEquals('Bebidas Frías y Calientes', $registro->detalles['despues']['nombre']);
    }

    public function test_dar_de_baja_categoria_registra_en_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $categoria = CategoriaProducto::create([
            'nombre'      => 'Limpieza',
            'descripcion' => 'Productos de limpieza',
            'estado'      => true,
        ]);

        $response = $this->actingAs($admin)->delete("/categorias/{$categoria->id_categoria}");

        $response->assertRedirect(route('categorias.index', ['estado' => 'inactivas']));

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'DAR DE BAJA',
            'modulo' => 'Categorías',
        ]);
    }

    public function test_crear_colaborador_registra_en_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $response = $this->actingAs($admin)->post('/usuarios', [
            'nombres'               => 'Ana Maria',
            'apellidos'             => 'Flores Martinez',
            'telefono'              => '7890-1234',
            'email'                 => 'ana.flores@101shop.com',
            'username'              => 'ana.flores',
            'id_rol'                => 2,
            'password'              => 'Password123*',
            'password_confirmation' => 'Password123*',
        ]);

        $response->assertRedirect('/usuarios');

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'CREAR',
            'modulo' => 'Personal Operativo',
        ]);

        $registro = Bitacora::where('accion', 'CREAR')
            ->where('modulo', 'Personal Operativo')
            ->first();

        $this->assertNotNull($registro);
        $this->assertStringContainsString('ana.flores', $registro->descripcion);
    }

    public function test_reactivar_categoria_registra_en_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $categoria = CategoriaProducto::create([
            'nombre'      => 'Papelería',
            'descripcion' => 'Útiles de oficina',
            'estado'      => false,
        ]);

        $response = $this->actingAs($admin)->patch("/categorias/{$categoria->id_categoria}/reactivar");

        $response->assertRedirect(route('categorias.index', ['estado' => 'activas']));

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'REACTIVAR',
            'modulo' => 'Categorías',
        ]);
    }

    public function test_inicio_de_sesion_registra_en_bitacora(): void
    {
        $user = User::factory()->create([
            'id_rol'   => 1,
            'username' => 'carlos.admin',
            'password' => 'Secret123*',
            'estado'   => true,
        ]);

        $response = $this->post('/login', [
            'login'    => 'carlos.admin',
            'password' => 'Secret123*',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('bitacoras', [
            'accion' => 'INICIO DE SESIÓN',
            'modulo' => 'Autenticación',
        ]);
    }

    public function test_administrador_puede_ver_detalle_de_bitacora(): void
    {
        $admin = User::factory()->create(['id_rol' => 1]);

        $bitacora = Bitacora::create([
            'id_usuario'     => $admin->id_usuario,
            'usuario_nombre' => 'Admin Test',
            'accion'         => 'MODIFICAR',
            'modulo'         => 'Categorías',
            'descripcion'    => 'Se modificó categoría prueba',
            'detalles'       => [
                'antes'   => ['nombre' => 'Nombre Antiguo'],
                'despues' => ['nombre' => 'Nombre Nuevo'],
            ],
            'ip'             => '127.0.0.1',
            'created_at'     => now(),
        ]);

        $response = $this->actingAs($admin)->get("/bitacora/{$bitacora->id_bitacora}");

        $response->assertStatus(200);
        $response->assertSee('Detalle de auditoría');
        $response->assertSee('Nombre Antiguo');
        $response->assertSee('Nombre Nuevo');
    }
}
