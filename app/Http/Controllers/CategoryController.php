<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Image;
use App\Models\Style;



class CategoryController extends Controller
{

    // カテゴリーの追加
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:categories|max:255',
        ]);

        $category = Category::create($validatedData);
        return redirect()->route('admin.top')->with('success', 'カテゴリーを追加しました！');
    }

    // 画像のカテゴリー変更
    public function updateAllCategories(Request $request) {
        $data = $request->get('category_image');
    
        foreach ($data as $imageId => $categories) {
            $image = Image::find($imageId);
            if ($image) {
                $image->categories()->sync($categories['categories']);
            }
        }
    
        return back()->with('success', 'カテゴリが一括で更新されました');
    }

    // カテゴリーの変更
    public function update(Request $request, $categoryId) {
        $category = Category::find($categoryId);
        if (!$category) {
            return back()->with('error', 'カテゴリが見つかりませんでした。');
        }
    
        // nameの更新
        $category->name = $request->name;
        
        // style_idの更新を追加
        if ($request->has('style_id')) {
            $style_id = $request->style_id;
            // 念のため、stylesテーブルにそのIDが存在するか確認
            if (Style::where('id', $style_id)->exists()) {
                $category->style_id = $style_id;
            } else {
                return back()->with('error', '指定されたスタイルが存在しません。');
            }
        }
        
        $category->save();
    
        return back()->with('success', 'カテゴリが更新されました。');
    }
    
    // カテゴリーの削除
    public function destroy($categoryId) {
        $category = Category::find($categoryId);
        if (!$category) {
            return back()->with('error', 'カテゴリが見つかりませんでした。');
        }
    
        $category->delete();
        return back()->with('success', 'カテゴリが削除されました。');
    }
    
                

}
