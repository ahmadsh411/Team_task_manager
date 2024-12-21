<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Http\Request;

trait UploadPhoto
{
    /**
     * تحميل صورة أو عدة صور
     */
    public function uploadImages(Request $request, $inputname, $foldername, $disk, $imageable_id, $imageable_type)
    {
        // التحقق من وجود صور في الحقل
        if ($request->hasFile($inputname)) {

            // إذا كانت الصور عبارة عن مجموعة
            $files = is_array($request->file($inputname)) ? $request->file($inputname) : [$request->file($inputname)];

            foreach ($files as $file) {
                // التحقق من صلاحية الصورة
                if (!$file->isValid()) {
                    return response()->json([
                        "message" => "Error with uploaded image",
                    ], 422);
                }

                // إنشاء اسم فريد للصورة
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // حفظ السجل في قاعدة البيانات
                $image = new Image();
                $image->filename = $filename;
                $image->imageable_id = $imageable_id;
                $image->imageable_type = $imageable_type;
                $image->save();

                // تخزين الصورة في المسار المحدد
                $file->storeAs($foldername, $filename, $disk);
            }
        }

        return null;
    }

    /**
     * حذف صورة أو مجموعة صور
     */
    public function deleteImages($path, $filenames, $imageable_type, $imageable_id, $disk)
    {
        $filenames = is_array($filenames) ? $filenames : [$filenames];

        foreach ($filenames as $filename) {
            $fullPath = $path . '/' . $filename;


            Image::where('imageable_type', $imageable_type)
                ->where('imageable_id', $imageable_id)
                ->where('filename', $filename)
                ->delete();
            // التحقق من وجود الملف قبل الحذف
            if (\Storage::disk($disk)->exists($fullPath)) {
                \Storage::disk($disk)->delete($fullPath);

            } else {
                // في حالة عدم العثور على الملف
                \Log::warning("File not found: " . $fullPath);
            }
        }

        return null;
    }

}
