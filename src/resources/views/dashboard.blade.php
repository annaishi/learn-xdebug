@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('content')
    <h1>ようこそ、{{ $user->name }} さん</h1>

    <p style="font-size:.9rem; color:#374151;">
        ログイン中のメールアドレス：<br>
        <strong>{{ $user->email }}</strong>
    </p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="background:#dc2626;">ログアウト</button>
    </form>
@endsection
