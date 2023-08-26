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
            'images.*' => 'required|mimes:jpg,jpeg,png|min:1024|max:2048',
        ]);
    
        // 複数画像のアップロード処理
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $hash = hash_file('sha256', $imageFile->getRealPath());
                $filename = $hash . '.' . $imageFile->getClientOriginalExtension();
    
                // 既に同じハッシュの画像が存在するかを確認
                if (Image::where('filename', $filename)->exists()) {
                    // エラーメッセージを返すか、このファイルをスキップして次の画像へ
                    return redirect()->route('admin.top')->with('error', '同じ内容の画像が既に存在します: ' . $imageFile->getClientOriginalName());
                }
    
                // 画像をS3にアップロード
                Storage::disk('s3')->putFileAs('images', $imageFile, $filename, 'public');
    
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

    public function downloadCount($filename) {
        $image = Image::where('filename', $filename)->firstOrFail();
        $image->increment('download_count');
        return response()->json(['message' => 'Download count incremented']);
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
