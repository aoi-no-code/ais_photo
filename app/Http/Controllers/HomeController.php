<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use App\Models\Style;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;



class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    

    public function index(Request $request, $categoryName = null)
    {
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
    
        $query = $this->buildImageQuery($request, $categoryName);
        $images = $query->take($limit)->get();
    
        return view('user.top', compact('categories', 'images', 'styles', 'sortedStyles'));
    }
    
    public function fetchImages(Request $request)
    {
        $categoryName = $request->input('categoryName');
        $offset = $request->input('offset', 0);  // デフォルトは0です
    
        $query = $this->buildImageQuery($request, $categoryName);
        $images = $query->skip($offset)->take(20)->get();
    
        if ($images->isEmpty()) {
            return response()->json(['message' => '以上です', 'images' => []]);
        }
    
        return response()->json(['images' => $images]);
    }
    
    private function buildImageQuery(Request $request, $categoryName = null)
    {
        $sortOption = $request->input('sort', 'downloads');  // デフォルトはダウンロード数の多い順
    
        $query = Image::with('categories');
    
        if ($sortOption === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('download_count', 'desc');
        }
    
        if ($categoryName) {
            $query->whereHas('categories', function ($subQuery) use ($categoryName) {
                $subQuery->where('name', $categoryName);
            });
        }
    
        return $query;
    }
    

        public function contact()
    {
        return view('user.contact');
    }


    // public function terms()
    // {
    //     return view('user.terms');
    // }

    public function submitContact(Request $request) {
        $data = $request->validate([
            'name' => 'required|string',
            'hurigana' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'referenceImage' => 'required|image|max:2048', // 2MBの制限
            'imageURL' => 'required|url',
        ]);
    
        // メール送信
        Mail::to('ai.s.photo.official@gmail.com')->send(new ContactMail($data));
    
        return back()->with('message', '作成リクエストを送信しました！');
    }

                        
}



