<?php
namespace App\Http\Controllers\Api;use App\Http\Controllers\Controller;use App\Http\Requests\StoreInquiryRequest;use App\Models\Inquiry;use Illuminate\Http\JsonResponse;
class InquiryController extends Controller{public function store(StoreInquiryRequest $request):JsonResponse{$inquiry=Inquiry::create($request->validated());return response()->json(['message'=>'Your request has been received.','data'=>['id'=>$inquiry->id]],201);}}
