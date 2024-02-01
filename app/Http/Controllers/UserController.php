<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Style;
use App\Models\Category;
use App\Models\UserCategoryPreferences;



use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{

    public function index()
    {
        $users = User::orderBy('is_admin', 'desc')->get();
        $styles = Style::all();
        $categories = Category::all();

        return view('admin.user', compact('users', 'styles', 'categories'));
    }

    // ユーザの保存
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            // 必要に応じて他のバリデーションルールを追加
        ]);

        $data['password'] = bcrypt($data['password']);
        
        User::create($data);

        return redirect()->route('admin.top')->with('message', 'User created successfully.');
    }

    // 特定のユーザのステータスとban情報を更新するメソッド
    public function updateStatusAndBanInfo(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $user->status = $request->input('status', 'active'); 
        $user->banned_until = $request->input('banned_until');
        $user->ban_reason = $request->input('ban_reason');

        $user->save();

        return redirect()->route('admin.top')->with('message', 'User status and ban information updated successfully.');
    }

    // ユーザーの削除
    public function destroy($userId)
    {
        $user = User::findOrFail($userId);
        $user->delete();

        return redirect()->route('admin.top')->with('message', 'User deleted successfully.');
    }


    public function updateBanInfo(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
    
        $bannedUntil = Carbon::now();  // 現在の日時を取得
        $user->banned_until = $bannedUntil;
    
        $user->ban_reason = $request->input('ban_reason');
    
        $user->save();
    
        return redirect()->back()->with('message', 'Ban information updated successfully.');
    }

    // UserController.php
    public function updatePlan(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $data = $request->validate([
            'plan_type' => 'required|in:personal,corporate',
        ]);

        $user->plan_type = $data['plan_type'];
        $user->save();

        return back()->with('message', 'User plan updated successfully.');
    }


}
