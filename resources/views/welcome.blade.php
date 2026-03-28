<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'FriendsFinance') }}</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            body {
                font-family: 'Figtree', sans-serif;
            }
            .hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
            }
            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
            }
            .hero p {
                font-size: 1.5rem;
                margin-bottom: 2rem;
                opacity: 0.9;
            }
            .btn-lg {
                padding: 0.8rem 2rem;
                font-size: 1.1rem;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="hero">
            <div class="text-center">
                <h1>{{ config('app.name', 'FriendsFinance') }}</h1>
                <p>Financial Management System for Cooperative Groups</p>

                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg me-3">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Register</a>
                @endauth
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
