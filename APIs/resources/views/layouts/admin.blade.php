<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - WhatsApp Bot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">WhatsApp Bot</a>
        <div class="ms-auto">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-outline-light">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <aside class="col-md-2 bg-light min-vh-100 p-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Bots</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Contacts</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Campaigns</a></li>
                    <li class="nav-item"><a href="#" class="nav-link">Billing</a></li>
                </ul>
            </aside>

            <main class="col-md-10 p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
