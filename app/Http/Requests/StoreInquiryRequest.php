<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class StoreInquiryRequest extends FormRequest{public function authorize():bool{return true;}public function rules():array{return['name'=>['required','string','max:100'],'email'=>['required','email:rfc','max:150'],'phone'=>['nullable','string','max:30'],'program'=>['required','string','max:100']];}}
