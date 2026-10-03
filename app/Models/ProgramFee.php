<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ProgramFee extends Model{protected $fillable=['program_id','fee_type','amount','currency','billing_basis','notes','sort_order','is_active'];protected function casts():array{return['amount'=>'decimal:2','is_active'=>'boolean'];}public function program():BelongsTo{return $this->belongsTo(Program::class);}}
