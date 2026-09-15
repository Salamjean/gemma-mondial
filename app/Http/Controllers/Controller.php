<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\File;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function uploadLogo($name, $image)
    {
        $pathDir = public_path("assets/iconFavicon");
        if (!File::exists($pathDir)) {
            File::makeDirectory($pathDir, 0755, true);
        }

        foreach (['ico', 'png', 'jpg', 'jpeg', 'svg'] as $ext) {
            $oldFile = $pathDir . '/' . $name . '.' . $ext;
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }

        $ext = strtolower($image->getClientOriginalExtension());
        $filename = $name . '.' . $ext;
        $image->move($pathDir, $filename);

        return "success";
    }

    public function uploadImage($image, $path)
    {
        $uploadDirectory = public_path('assets/uploads/' . $path . '/');
        if (!File::exists($uploadDirectory)) {
            File::makeDirectory($uploadDirectory, 0755, true);
        }

        $file = $image;
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $ext;
        $file->move($uploadDirectory, $filename);

        return $filename;
    }

    public function deleteUploadImage($image, $link)
    {
        $uploadDirectory = public_path('assets/uploads/' . $link . '/');
        if (!File::exists($uploadDirectory)) {
            File::makeDirectory($uploadDirectory, 0755, true);
        }

        if ($image) {
            $path = $uploadDirectory . $image;
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $file = $image;
        $ext = $file->getClientOriginalExtension();
        $filename = time() . '.' . $ext;
        $file->move($uploadDirectory, $filename);

        return $filename;
    }

    public function deleteImage($image, $link)
    {
        $path = public_path("assets/uploads/$link/$image");
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
