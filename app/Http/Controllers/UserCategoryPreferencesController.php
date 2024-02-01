<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Style;
use App\Models\Category;
use App\Models\UserStylePreference; // このモデルを使用する場合、適切にインポートする

class UserCategoryPreferencesController extends Controller
{
    public function update(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        // 'style_preferences' から 'preferences' に変更
        $preferences = $request->input('preferences', []);
    
        // 既存の選好をクリア
        $user->stylePreferences()->delete();
    
        // 新しい選好を保存
        foreach ($preferences as $styleId => $categoryId) {
    
            if (!empty($categoryId)) {
                UserStylePreference::create([
                    'user_id' => $user->id,
                    'style_id' => $styleId,
                    'category_id' => $categoryId,
                ]);
            }
        }
    
        // フラッシュメッセージと共に元のページに戻る
        return back()->with('message', 'Style preferences updated successfully.');
    }
}
