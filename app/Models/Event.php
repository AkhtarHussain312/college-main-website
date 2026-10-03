<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class Event extends Model{protected $fillable=['title','slug','excerpt','body','event_date','image_path','is_published'];protected function casts():array{return['event_date'=>'date','is_published'=>'boolean'];}}
