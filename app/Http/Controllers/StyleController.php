<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Style;
use App\Models\Category;



class StyleController extends Controller
{

    public function index()
    {
        $styles = Style::all();
        return view('admin.style', compact('styles'))->render();
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:categories|max:255',
        ]);

        $style = Style::create($validatedData);
        return redirect()->back()->with('success', 'カテゴリーを追加しました！');
    }

    // カテゴリーの変更
    public function update(Request $request, $styleId) {
        $style = Style::find($styleId);
        if (!$style) {
            return back()->with('error', 'カテゴリが見つかりませんでした。');
        }
    
        $style->name = $request->name;
        $style->save();
    
        return back()->with('success', 'カテゴリが更新されました。');
    }

    // カテゴリーの削除
    public function destroy($styleId) {
        $style = Style::find($styleId);
        if (!$style) {
            return back()->with('error', 'カテゴリが見つかりませんでした。');
        }
    
        $style->delete();
        return back()->with('success', 'カテゴリが削除されました。');
    }
    
    


}
