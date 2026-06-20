<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'learn-xdebug')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
            background: #f3f4f6; margin: 0; color: #111827;
            display: flex; min-height: 100vh; align-items: center; justify-content: center;
        }
        .card {
            background: #fff; padding: 2rem; border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,.08); width: 100%; max-width: 380px;
        }
        h1 { font-size: 1.25rem; margin: 0 0 1.5rem; }
        label { display: block; font-size: .85rem; margin: .75rem 0 .25rem; color: #374151; }
        input[type=text], input[type=email], input[type=password] {
            width: 100%; padding: .6rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: .95rem;
        }
        button {
            margin-top: 1.25rem; width: 100%; padding: .65rem; border: 0; border-radius: 8px;
            background: #2563eb; color: #fff; font-size: .95rem; cursor: pointer;
        }
        button:hover { background: #1d4ed8; }
        .links { margin-top: 1rem; font-size: .85rem; text-align: center; }
        a { color: #2563eb; text-decoration: none; }
        .errors { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c;
            padding: .6rem .75rem; border-radius: 8px; font-size: .85rem; margin-bottom: 1rem; }
        .errors ul { margin: 0; padding-left: 1.1rem; }
        .checkbox { display: flex; align-items: center; gap: .4rem; margin-top: .75rem; font-size: .85rem; }
        .checkbox input { width: auto; }
    </style>
</head>
<body>
    <div class="card">
        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
