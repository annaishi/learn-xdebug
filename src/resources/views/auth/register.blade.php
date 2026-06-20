@extends('layouts.app')

@section('title', '新規登録')

@section('content')
    <h1>新規登録</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">名前</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>

        <label for="email">メールアドレス</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>

        <label for="password">パスワード（8文字以上）</label>
        <input id="password" type="password" name="password" required>

        <label for="password_confirmation">パスワード（確認）</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">登録する</button>
    </form>

    <div class="links">
        すでにアカウントをお持ちの方は <a href="{{ route('login') }}">ログイン</a>
    </div>
@endsection
