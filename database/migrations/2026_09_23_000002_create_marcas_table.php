<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up():void {
        Schema::create('marcas',function(Blueprint $t){$t->id('id_marca');$t->string('nombre',80)->unique();$t->boolean('estado')->default(true);$t->timestamps();});
        Schema::table('productos',function(Blueprint $t){$t->unsignedBigInteger('id_marca')->nullable();$t->foreign('id_marca')->references('id_marca')->on('marcas');});
        foreach(DB::table('productos')->whereNotNull('marca')->where('marca','<>','')->get() as $producto) {
            $id=DB::table('marcas')->where('nombre',$producto->marca)->value('id_marca');
            if(!$id)$id=DB::table('marcas')->insertGetId(['nombre'=>$producto->marca,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],'id_marca');
            DB::table('productos')->where('id_producto',$producto->id_producto)->update(['id_marca'=>$id]);
        }
    }
    public function down():void {Schema::table('productos',function(Blueprint $t){$t->dropForeign(['id_marca']);$t->dropColumn('id_marca');});Schema::dropIfExists('marcas');}
};
