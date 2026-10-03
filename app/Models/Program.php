<?php
namespace App\Models;use Illuminate\Database\Eloquent\Factories\HasFactory;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\HasMany;
class Program extends Model{use HasFactory;protected $fillable=['name','slug','duration','description','image_path','sort_order','is_active'];protected function casts():array{return['is_active'=>'boolean'];}public function fees():HasMany{return $this->hasMany(ProgramFee::class)->orderBy('sort_order');}}
