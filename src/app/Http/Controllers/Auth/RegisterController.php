<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ユーザー登録の入り口（Controller 層）。
 */
class RegisterController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /** 登録フォームを表示 */
    public function show(): View
    {
        return view('auth.register');
    }

    /** 登録処理 */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // $request->validated() でバリデーション済みの値だけを取り出せる
        $this->authService->register($request->validated());

        return redirect()->route('dashboard');
    }
}
