<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            margin: 0;
            display: flex;
            font-family: Arial;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 10px;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>Berco Admin</h2>
        

        <a href="/control/dashboard">Dashboard</a>
        <a href="/control/staff">Staff Management</a>
        <a href="/control/reset">Reset Requests</a>
        <a href="/control/orders">Orders</a>
        <a href="/control/history">Order History</a>

        <form method="POST" action="/logout">
            @csrf
            <button style="width:100%; padding:10px; background:red; color:white; border:none;">
                Logout
            </button>
        </form>
    </div>

    <div class="content">
        @yield('content')
    </div>

</body>
</html>