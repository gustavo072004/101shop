<?php
namespace App\Http\Controllers;
use App\Models\Marca;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class MarcaController extends Controller {
    public function index(){return view('marcas.index',['marcas'=>Marca::orderBy('nombre')->get(),'marca'=>new Marca]);}
    public function edit(Marca $marca){return view('marcas.index',['marcas'=>Marca::orderBy('nombre')->get(),'marca'=>$marca]);}
    private function data(Request $r,?Marca $marca=null):array {
        if(is_string($r->nombre))$r->merge(['nombre'=>preg_replace('/\s+/u',' ',trim($r->nombre))]);
        return $r->validate(['nombre'=>['required','string','min:2','max:80',Rule::unique('marcas')->ignore($marca),function($a,$v,$fail)use($marca){if(Marca::whereRaw('LOWER(nombre)=LOWER(?)',[$v])->when($marca,fn($q)=>$q->where('id_marca','<>',$marca->id_marca))->exists())$fail('Ya existe una marca con ese nombre.');}]],['nombre.required'=>'Ingrese el nombre de la marca.','nombre.unique'=>'Ya existe una marca con ese nombre.']);
    }
    public function store(Request $r){$m=Marca::create([...$this->data($r),'estado'=>true]);$this->log('CREAR',$m);return redirect()->route('marcas.index')->with('success','Marca registrada.');}
    public function update(Request $r,Marca $marca){$marca->update($this->data($r,$marca));$this->log('MODIFICAR',$marca);return redirect()->route('marcas.index')->with('success','Marca actualizada.');}
    public function destroy(Marca $marca){$marca->update(['estado'=>false]);$this->log('DAR DE BAJA',$marca);return redirect()->route('marcas.index')->with('success','Marca desactivada; los productos conservan su referencia.');}
    public function reactivar(Marca $marca){$marca->update(['estado'=>true]);$this->log('REACTIVAR',$marca);return redirect()->route('marcas.index')->with('success','Marca reactivada.');}
    private function log($a,$m){BitacoraService::registrar($a,'Marcas',"{$a}: {$m->nombre}",['id_marca'=>$m->id_marca]);}
}
