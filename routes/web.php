<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Models\CategoriaProducto;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    return redirect()->route('publico.catalogo.index');
});

Route::get('/publico/catalogo', [CatalogoController::class, 'index'])->name('publico.catalogo.index');
Route::get('/publico/catalogo/{producto}', [CatalogoController::class, 'show'])->name('publico.catalogo.show');
Route::get('/imagenes/productos/{producto}', [CatalogoController::class, 'imagen'])->name('productos.imagen');

Route::middleware('auth')->group(function () {
    Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
    Route::get('/catalogo/{producto}', [CatalogoController::class, 'show'])->name('catalogo.show');

    Route::get('/dashboard', function () {
        return view('dashboard', [
            'categoriasActivas' =>
                CategoriaProducto::where(
                    'estado',
                    true
                )->count(),

            'usuariosActivos' =>
                User::where(
                    'estado',
                    true
                )->count(),

            'eventosBitacora' =>
                \Illuminate\Support\Facades\Schema::hasTable('bitacoras')
                    ? \App\Models\Bitacora::count()
                    : 0,
        ]);
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Perfil
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/perfil',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/perfil',
        [ProfileController::class, 'update']
    )->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Funciones administrativas
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {
        Route::patch('/marcas/{marca}/reactivar', [\App\Http\Controllers\MarcaController::class, 'reactivar'])->name('marcas.reactivar');
        Route::resource('marcas', \App\Http\Controllers\MarcaController::class)->except(['create','show']);
        Route::patch('/productos/{producto}/reactivar', [ProductoController::class, 'reactivar'])->name('productos.reactivar');
        Route::resource('productos', ProductoController::class);

        /*
        |--------------------------------------------------------------------------
        | Categorías
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/categorias/{categoria}/reactivar',
            [
                CategoriaProductoController::class,
                'reactivar',
            ]
        )->name('categorias.reactivar');

        Route::resource(
            'categorias',
            CategoriaProductoController::class
        )->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Personal operativo
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/usuarios/{usuario}/reactivar',
            [
                UsuarioController::class,
                'reactivar',
            ]
        )->name('usuarios.reactivar');

        Route::resource(
            'usuarios',
            UsuarioController::class
        )->except(['show']);

        /*
        |--------------------------------------------------------------------------
        | Bitácora de auditoría
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/bitacora',
            [BitacoraController::class, 'index']
        )->name('bitacora.index');

        Route::get(
            '/bitacora/{bitacora}',
            [BitacoraController::class, 'show']
        )->name('bitacora.show');
    });
});

require __DIR__.'/auth.php';
