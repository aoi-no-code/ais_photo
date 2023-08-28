<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use App\Models\Style;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


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

        $order = ['Gender', 'Length', 'Color', 'Background wall', 'Angle', 'Age'];
    
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
    
        $query = $this->buildImageQuery();
        $images = $query->take($limit)->get();

        $totalImagesCount = Image::count(); 
    
        return view('user.top', compact('categories', 'images', 'styles', 'sortedStyles', 'totalImagesCount'));
    }
    
    public function fetchImages(Request $request)
    {
        $categoryName = $request->input('categoryName');
        Log::info('Category Name: ' . $categoryName);
        $offset = $request->input('offset', 0);  // デフォルトは0です
    
        $query = $this->buildImageQuery();
    
        if ($categoryName) {
            $categories = explode(',', $categoryName); // カンマで区切って配列にする
        
            $query->whereHas('categories', function ($subQuery) use ($categories) {
                $subQuery->whereIn('name', $categories);
            }, '=', count($categories)); // count($categories) で指定することで、その数だけ一致するものを取得
        }
            
        // 画像の総数を取得
        $totalImages = $query->count();
    
        $images = $query->skip($offset)->take(20)->get();
    
        if ($images->isEmpty()) {
            return response()->json(['message' => '以上です', 'images' => [], 'totalImages' => 0]);
        }
    
        return response()->json(['images' => $images, 'totalImages' => $totalImages]);
    }
        
    
    private function buildImageQuery()
    {
        $query = Image::with('categories');
        // ダウンロード数の多い順に並べ替え
        $query->orderBy('download_count', 'desc');
        return $query;
    }
        

    public function contact()
    {
        return view('user.contact');
    }

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



