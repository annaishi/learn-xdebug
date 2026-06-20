<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ログイン後に表示するダッシュボード。
 * auth ミドルウェアで保護されるため、未ログインだと login にリダイレクトされる。
 */
class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        // $request->user() で現在ログイン中のユーザーを取得できる
        return view('dashboard', [
            'user' => $request->user(),
        ]);
    }
}
