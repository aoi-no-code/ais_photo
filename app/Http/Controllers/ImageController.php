<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;


class ImageController extends Controller
{

    public function upload(Request $request)
    {
        // 複数画像のバリデーション
        $request->validate([
            'images.*' => 'required|mimes:jpg,jpeg,png|max:2048', // 2MBまで
        ]);

        // 複数画像のアップロード処理
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // 画像をS3にアップロード
                $path = $imageFile->store('images', 's3');

                // S3のパスからファイル名のみを取得
                $filename = basename($path);

                // 画像をDBに保存
                Image::create(['filename' => $filename]);
            }
        }

        return redirect()->route('admin.top')->with('success', '画像がアップロードされました！');
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
    
    public function destroyAPI(Image $image)
    {
        // S3からの画像ファイルの削除
        Storage::disk('s3')->delete('images/' . $image->filename);
        
        // データベースからの画像の削除
        $image->delete();
        
        return response()->json(['message' => '画像が正常に削除されました。']);
    }

    
    public function images() {
        $categories = Category::all();
        $images = Image::with('categories')->orderBy('download_count', 'desc')->get();

        return view('admin.image_form', compact('categories', 'images'));
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



            
}
