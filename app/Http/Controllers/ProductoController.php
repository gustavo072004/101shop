<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\CategoriaProducto;
use App\Models\Producto;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $filters=$request->validate(['q'=>'nullable|string|max:150','estado'=>'nullable|in:activos,inactivos,todos']);
        $estado=$filters['estado'] ?? 'activos';
        $productos=Producto::with('categoria')
            ->when($estado !== 'todos', fn($q)=>$q->where('estado',$estado==='activos'))
            ->when($filters['q'] ?? null, fn($q,$s)=>$q->where(fn($q)=>$q->whereRaw('LOWER(nombre) LIKE ?',['%'.mb_strtolower($s).'%'])->orWhere('codigo','like','%'.mb_strtoupper($s).'%')))
            ->orderByDesc('id_producto')->paginate(15)->withQueryString();
        return view('productos.index',compact('productos','estado'));
    }

    public function create()
    {
        return view('productos.form',['producto'=>new Producto(['stock'=>0,'publicado'=>false]),'marcas'=>\App\Models\Marca::where('estado',true)->orderBy('nombre')->get(),'categorias'=>CategoriaProducto::where('estado',true)->orderBy('nombre')->get()]);
    }

    public function store(ProductoRequest $request)
    {
        $producto=DB::transaction(function() use($request) {
            $producto=Producto::create([...$this->attributes($request),'estado'=>true]);
            $this->log('CREAR',$producto);
            return $producto;
        });
        return redirect()->route('productos.show',$producto)->with('success','Producto registrado correctamente.');
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('productos.show',compact('producto'));
    }

    public function edit(Producto $producto)
    {
        abort_unless($producto->estado,409,'Reactive el producto antes de modificarlo.');
        return view('productos.form',['producto'=>$producto,'marcas'=>\App\Models\Marca::where('estado',true)->orderBy('nombre')->get(),'categorias'=>CategoriaProducto::where('estado',true)->orderBy('nombre')->get()]);
    }

    public function update(ProductoRequest $request,Producto $producto)
    {
        abort_unless($producto->estado,409,'Reactive el producto antes de modificarlo.');
        DB::transaction(function() use($request,$producto) {
            $producto->update($this->attributes($request));
            $this->log('MODIFICAR',$producto);
        });
        return redirect()->route('productos.show',$producto)->with('success','Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->update(['estado'=>false,'publicado'=>false]);
        $this->log('DAR DE BAJA',$producto);
        return redirect()->route('productos.index')->with('success','Producto dado de baja; su historial se conserva.');
    }

    public function reactivar(Producto $producto)
    {
        $producto->update(['estado'=>true]);
        $this->log('REACTIVAR',$producto);
        return redirect()->route('productos.edit',$producto)->with('success','Producto reactivado. Revise sus datos antes de publicarlo.');
    }

    private function attributes(ProductoRequest $request): array
    {
        $data=$request->safe()->except('imagen');
        $data['marca']=\App\Models\Marca::findOrFail($data['id_marca'])->nombre;
        if($request->hasFile('imagen')) {
            $data['imagen_datos']=base64_encode(file_get_contents($request->file('imagen')->getRealPath()));
            $data['imagen_tipo']=$request->file('imagen')->getMimeType();
        }
        return $data;
    }

    private function log(string $accion,Producto $producto): void
    {
        BitacoraService::registrar($accion,'Productos',"{$accion}: {$producto->codigo}",['id_producto'=>$producto->id_producto,'codigo'=>$producto->codigo]);
    }
}
