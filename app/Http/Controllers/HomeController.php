<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use App\Models\Style;
use Illuminate\Support\Facades\Storage;



class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, $categoryName = null) {

        $categories = Category::all();
    
        $order = ['gender', 'length', 'color', 'age'];
    
        $sortedStyles = Style::all()->sortBy(function ($style) use ($order) {
            return array_search($style->name, $order) !== false ? array_search($style->name, $order) : count($order);
        })->values();
    
        $styles = Style::with(['categories' => function ($query) use ($categoryName) {
            if ($categoryName) {
                $query->where('name', $categoryName);
            }
        }])
        ->get()
        ->sortBy(function ($style) use ($order) {
            return array_search($style->name, $order);
        })
        ->values();
    
        $limit = 20;
    
        if ($categoryName) {
            $category = Category::with(['images' => function ($query) use ($limit) {
                $query->orderBy('download_count', 'desc')->take($limit);
            }])->where('name', $categoryName)->first();
    
            $images = $category ? $category->images : collect([]);
        } else {
            $images = Image::with('categories')->orderBy('download_count', 'desc')->take($limit)->get();
        }
    
        return view('user.top', compact('categories', 'images', 'styles', 'sortedStyles'));    
    }

    
    public function fetchImages(Request $request)
    {
        $categoryName = $request->input('categoryName');
        $offset = $request->input('offset', 0);  // デフォルトは0です

        $query = Image::with('categories')->orderBy('download_count', 'desc');

        if ($categoryName) {
            $query->whereHas('categories', function ($subQuery) use ($categoryName) {
                $subQuery->where('name', $categoryName);
            });
        }

        $images = $query->skip($offset)->take(20)->get();

        if ($images->isEmpty()) {
            return response()->json(['message' => '以上です', 'images' => []]);
        }

        return response()->json(['images' => $images]);
    }
                        
}



