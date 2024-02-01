<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use App\Models\Style;
use App\Models\User;

use Illuminate\Support\Facades\Storage;


class AdminController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user() || !auth()->user()->is_admin) {
                return redirect('/home'); // 通常のユーザーのダッシュボードへのパス
            }

            return $next($request);
        });
    }


    public function index()
    {
        $categories = Category::all();
        return view('admin.top', ['categories' => $categories]);
    }


    // ほぼいらないかも
    // public function getContent($content) {
    //     switch ($content) {
    //         case 'user':
    //             $users = User::orderBy('is_admin', 'desc')->get();
    //             return view('admin.user', compact('users'));

    //         case 'image':
    //             $categories = Category::orderBy('style_id', 'asc')->get();  // 'asc'は昇順、'desc'は降順
    //             $images = Image::with('categories')->orderBy('created_at', 'desc')->paginate(20);
    //             return view('admin.image_form', compact('categories', 'images'));
            
    //         case 'category':
    //             $categories = Category::all();
    //             $styles = Style::all();
    //             return view('admin.category_form', compact('categories', 'styles'))->render();

    //         case 'style':
    //             $styles = Style::all();
    //             return view('admin.style', compact('styles'))->render();
                
    //         default:
    //             abort(404);
    //     }
    // }
    
}
