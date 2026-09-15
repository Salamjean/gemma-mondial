<?php

namespace App\Http\Controllers\Super;

use App\Http\Controllers\Controller;
use App\Rules\CheckIfFavicon;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Image;


class SettingController extends Controller
{
    protected $title;

    public function __construct($title = "Paramettre - ")
    {
        $this->title = $title;
    }

    public function icon()
    {
        $title = $this->title . "Logo";
        return view('users.super.setting.logo', compact('title'));
    }

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
    public function logoIconUpdate(Request $request)
    {
        $request->validate([
            'logo' => ['image',new FileTypeValidate(['png'])],
            'loading' => ['image',new FileTypeValidate(['png'])],
            'favicon' => [new CheckIfFavicon()],
        ]);

        $path = iconsLoad()["path"];

        if ($request->hasFile('logo')) {
            try {

                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $this->uploadLogo("logo", $request->logo);
            } catch (\Exception $exp) {
                return back()->withErrors(['error' => "Le logo n\'a pas pu être téléchargé."]);
            }
        }

        if ($request->hasFile('loading')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $this->uploadLogo("loading", $request->loading);
            } catch (\Exception $exp) {
                return back()->withErrors(['error' => "Le loading n\'a pas pu être téléchargé."]);
            }
        }

        if ($request->hasFile('favicon')) {
            try {
                if (!file_exists($path)) {
                    mkdir($path, 0755, true);
                }
                $this->uploadLogo("favicon", $request->favicon);
            } catch (\Exception $exp) {
                return back()->with('error',"Le favicon n\'a pas pu être téléchargé.");
            }
        }
        return back()->with('success',"Informations modifiées avec succès.");
    }
}
