<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Marca extends Model {
    protected $primaryKey='id_marca';
    protected $fillable=['nombre','estado'];
    protected $casts=['estado'=>'boolean'];
}
