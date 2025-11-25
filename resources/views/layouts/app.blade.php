<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epoint API Test Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, rgb(255, 216, 235) 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            margin-bottom: 30px;
        }
        .api-selector {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .param-group {
            margin-bottom: 15px;
        }
        .result-container {
            background: #282c34;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        pre {
            margin: 0;
            max-height: 600px;
            overflow: auto;
        }
        .badge-endpoint {
            background: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-family: monospace;
        }
        .btn-execute {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-weight: bold;
        }
        .btn-execute:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .redirect-button {
            margin-top: 15px;
        }
    </style>
    <div class="text-center mb-4">
        <a href="/" style="text-decoration: none;">
            <h1 class="text-white">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="white" style="vertical-align: middle;">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                </svg>
                Epoint API Test Panel
            </h1>
        </a>
        <p class="text-white">Test all Epoint payment gateway APIs</p>

        <!-- Navigation -->
        <div class="btn-group mt-3" role="group">
            <a href="{{ route('epoint.test') }}" class="btn btn-light {{ request()->routeIs('epoint.test') || request()->routeIs('epoint.execute') ? 'active' : '' }}">
                Payment APIs
            </a>
            <a href="{{ route('epoint.checkout') }}" class="btn btn-light {{ request()->routeIs('epoint.checkout*') ? 'active' : '' }}">
                Checkout APIs
            </a>
            <a href="{{ route('epoint.invoice') }}" class="btn btn-light {{ request()->routeIs('epoint.invoice*') ? 'active' : '' }}">
                Invoice APIs
            </a>
            <a href="{{ route('epoint.logs.index') }}" class="btn btn-light {{ request()->routeIs('epoint.logs.index') ? 'active' : '' }}">
                Logs
            </a>
            <a href="{{ route('epoint.logs.dashboard') }}" class="btn btn-light {{ request()->routeIs('epoint.logs.dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('docs.index') }}" class="btn btn-info {{ request()->routeIs('docs.*') ? 'active' : '' }}">
                📚 Dokumentasiya
            </a>
        </div>
    </div>
</head>
<body>
<div class="container">
    @yield('content')

    <!-- User Info & Logout -->
    <div class="mt-3 d-flex align-items-center justify-content-center gap-3">
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" style="display: inline-flex; align-items: center; gap: 5px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                    <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-json.min.js"></script>
@stack('scripts')
</body>
</html>
