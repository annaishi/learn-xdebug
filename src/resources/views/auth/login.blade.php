@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <h1>ログイン</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label for="password">パスワード</label>
        <input id="password" type="password" name="password" required>

        <label class="checkbox">
            <input type="checkbox" name="remember"> ログイン状態を保持する
        </label>

        <button type="submit">ログイン</button>
    </form>

    <div class="links">
        アカウントがない方は <a href="{{ route('register') }}">新規登録</a>
    </div>
@endsection
