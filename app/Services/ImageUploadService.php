<?php
namespace App\Services;use Illuminate\Http\UploadedFile;use Illuminate\Support\Facades\Storage;
class ImageUploadService{public function replace(UploadedFile $image,?string $oldPath,string $folder):string{$path=$image->store('uploads/'.$folder,'public');if($oldPath&&str_starts_with($oldPath,'uploads/')&&Storage::disk('public')->exists($oldPath))Storage::disk('public')->delete($oldPath);return $path;}public function delete(?string $path):void{if($path&&str_starts_with($path,'uploads/'))Storage::disk('public')->delete($path);}}
