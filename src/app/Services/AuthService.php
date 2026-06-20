<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 認証まわりのビジネスロジックを担う Service 層。
 *
 * Controller は「入力を受け取り、Service を呼び、結果を View/リダイレクトに渡す」だけ。
 * 実際の判定や永続化はこの Service が担当する（= MVCS の S）。
 *
 * --- Xdebug 学習のポイント ---
 * login() / register() の中はステップ実行（F10/F11）の練習に最適。
 * $credentials や $user の中身をブレークポイントで覗いてみよう。
 */
class AuthService
{
    /**
     * ユーザー登録を行い、そのままログイン状態にする。
     */
    public function register(array $data): User
    {
        // ここにブレークポイントを置くと、フォームから渡ってきた値を確認できる
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // User モデルの cast で自動的にハッシュ化される
        ]);

        Auth::login($user);

        return $user;
    }

    /**
     * メールアドレス・パスワードでログインを試みる。
     *
     * @throws ValidationException 認証に失敗した場合
     */
    public function login(Request $request, array $credentials, bool $remember = false): void
    {
        // attempt() の戻り値（true/false）をステップ実行で確認するのに良い箇所
        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'メールアドレスまたはパスワードが正しくありません。',
            ]);
        }

        // セッション固定攻撃対策。ログイン成功時にセッションIDを振り直す
        $request->session()->regenerate();
    }

    /**
     * ログアウトしてセッションを破棄する。
     */
    public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
