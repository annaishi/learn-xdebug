<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ログイン／ログアウトの入り口（Controller 層）。
 *
 * Controller は薄く保つ：
 *   1. リクエストを受け取り
 *   2. Service にビジネスロジックを委譲し
 *   3. View またはリダイレクトを返す
 */
class LoginController extends Controller
{
    // AuthService をコンストラクタインジェクションで受け取る（DIコンテナが解決）
    public function __construct(private readonly AuthService $authService) {}

    /** ログインフォームを表示 */
    public function show(): View
    {
        return view('auth.login');
    }

    /** ログイン処理 */
    public function store(LoginRequest $request): RedirectResponse
    {
        // ここにブレークポイント → Service 内に F11 でステップインしてみよう
        $this->authService->login(
            $request,
            $request->only('email', 'password'),
            $request->boolean('remember'),
        );

        return redirect()->intended(route('dashboard'));
    }

    /** ログアウト処理 */
    public function destroy(\Illuminate\Http\Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
