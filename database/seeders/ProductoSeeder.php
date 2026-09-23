<?php
namespace Database\Seeders;
use App\Models\Producto;
use App\Models\CategoriaProducto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        // Nombres y precios de la captura de referencia. Costos internos pendientes.
        $rows=[
            ['PR-001','Audífonos Bluetooth','Electrónica',24.99,'Audífonos con conexión Bluetooth.'],
            ['PR-002','Teclado Mecánico RGB','Computación',49.99,'Teclado mecánico con iluminación RGB.'],
            ['PR-003','Mouse Inalámbrico','Computación',19.99,'Mouse con conexión inalámbrica.'],
            ['PR-004','Smartwatch Deportivo','Tecnología',59.99,'Reloj inteligente deportivo.'],
            ['PR-005','Cargador USB-C','Accesorios',14.99,'Cargador con conexión USB-C.'],
            ['PR-006','Bocina Bluetooth','Electrónica',34.99,'Bocina con conexión Bluetooth.'],
            ['PR-007','Cámara Web HD','Computación',39.99,'Cámara web de alta definición.'],
            ['PR-008','Power Bank 10000mAh','Accesorios',29.99,'Batería portátil de 10000 mAh.'],
        ];
        foreach($rows as [$codigo,$nombre,$categoria,$precio,$descripcion]) {
            $category=CategoriaProducto::firstOrCreate(['nombre'=>$categoria],['estado'=>true]);
            Producto::firstOrCreate(['codigo'=>$codigo],[
                'nombre'=>$nombre,'descripcion'=>$descripcion,'id_categoria'=>$category->id_categoria,
                'valor_final'=>$precio,'stock'=>0,'estado'=>true,'publicado'=>true,
            ]);
        }
    }
}
