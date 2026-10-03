<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class Faculty extends Model{protected $table='faculty';protected $fillable=['name','title','qualification','image_path','sort_order','is_active'];protected function casts():array{return['is_active'=>'boolean'];}}
