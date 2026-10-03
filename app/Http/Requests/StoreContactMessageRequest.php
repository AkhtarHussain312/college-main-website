<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class StoreContactMessageRequest extends FormRequest{public function authorize():bool{return true;}public function rules():array{return['name'=>['required','string','max:100'],'email'=>['required','email','max:150'],'phone'=>['nullable','string','max:40'],'subject'=>['required','string','max:180'],'message'=>['required','string','min:10','max:5000']];}}
