<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as InterventionImage;


class ImageController extends Controller
{


    public function index(Request $request) {
        $categories = Category::orderBy('style_id', 'asc')->get();
        
        $orderBy = $request->input('orderBy', 'created_at');  // デフォルトは 'created_at'
        $orderDirection = $request->input('orderDirection', 'desc');  // デフォルトは 'desc'
    
        $query = Image::with('categories')->orderBy($orderBy, $orderDirection);
                
        // カテゴリーフィルターの取得
        $categoryFilter = $request->input('category');
        
        // カテゴリーフィルターがあればクエリに適用
        if (!empty($categoryFilter)) {
            $query->whereHas('categories', function($q) use ($categoryFilter) {
                $q->where('categories.id', $categoryFilter);
            });
        }
        
        $images = $query->paginate(50);
        
        return view('admin.image_form', compact('categories', 'images'));
    }
        



    
    public function upload(Request $request)
    {
        // 複数画像のバリデーション
        $request->validate([
            'images.*' => 'required|mimes:jpg,jpeg,png|min:800|max:2048',
        ]);
    
        // 複数画像のアップロード処理
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // 画像をIntervention Imageで読み込む
                $image = InterventionImage::make($imageFile->getRealPath());
    
                // 画像に何らかの処理を加える場合はここで行う
                // ...
    
                // Intervention Imageオブジェクトからバイナリデータを取得
                $imageStream = $image->stream()->detach();
    
                $hash = hash_file('sha256', $imageFile->getRealPath());
                $filename = $hash . '.' . $imageFile->getClientOriginalExtension();
    
                // 既に同じハッシュの画像が存在するかを確認
                if (Image::where('filename', $filename)->exists()) {
                    return redirect()->route('admin.top')->with('error', '同じ内容の画像が既に存在します: ' . $imageFile->getClientOriginalName());
                }
    
                // メタデータを削除した画像をS3にアップロード
                Storage::disk('s3')->put('images/' . $filename, $imageStream, 'public');
    
                // 画像をDBに保存
                Image::create(['filename' => $filename]);
            }
        }
    
        return back()->with('success', '画像がアップロードされました！');
    }
    
    
    public function downloadImage($filename) {
        // イメージを検索
        $image = Image::where('filename', $filename)->firstOrFail();
    
        // download_count インクリメント
        $image->increment('download_count');
    
        // ダウンロードレスポンスの返却
        return Storage::disk('s3')->download('images/' . $filename);
    }

    public function destroy(Image $image)
    {
        // S3からの画像ファイルの削除
        Storage::disk('s3')->delete('images/' . $image->filename);
        
        // データベースからの画像の削除
        $image->delete();
    
        return redirect()->route('admin.top')->with('success', '画像が正常に削除されました。');
    }
    
    public function destroyAPI($filename)
    {
        // S3から画像が存在する場合は削除
        if(Storage::disk('s3')->exists('images/' . $filename)) {
            Storage::disk('s3')->delete('images/' . $filename);
        }
        
        // DBから関連するデータを削除（こちらのロジックはあなたのDB設計に依存します）
        $image = Image::where('filename', $filename)->first();  // 例として、filenameをキーにしてDBを検索
        if ($image) {
            $image->delete();
        } else {
            return response()->json(['error' => 'File not found in DB'], 404);
        }
    
        return response()->json(['success' => 'File successfully deleted'], 200);
    }
        
    public function loadMoreImages(Request $request){
        $limit = $request->get('limit', 20);
        $offset = $request->get('offset', 0);

        // ここで画像を取得します。カテゴリーに基づいてフィルタリングする場合は、クエリを調整します。
        $images = Image::with('categories')
            ->orderBy('download_count', 'desc')
            ->take($limit)
            ->skip($offset)
            ->get();

        // 必要に応じて他の関連データを取得することもできます。

        return response()->json([
            'images' => $images
        ]);
    }


    public function incrementDownloadCount($filename) {
        // イメージを検索
        $image = Image::where('filename', $filename)->firstOrFail();
        
        // download_count インクリメント
        $image->increment('download_count');
        
        // インクリメントが成功したことを示す何らかのレスポンスを返す
        return response()->json(['status' => 'success']);
    }
    
    
}
