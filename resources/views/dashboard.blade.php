<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #FFFCEC;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: #5d2e1a;
            color: white;
            padding: 20px 15px;
        }

        .sidebar-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-circle {
            width: 60px;
            height: 60px;
            background: #FFFCEC;
            color: #5d2e1a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            font-size: 20px;
        }

        .menu {
            padding: 12px;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
            display: block;
            text-decoration: none;
            color: white;
        }

        .menu:hover {
            background: #B7791F;
        }

        .menu.active {
            background: #B7791F;
        }

        .menu.logout {
            margin-top: 30px;
            background: #92400E;
        }

        /* LAYOUT */
        .container {
            display: flex;
            min-height: 100vh;
        }

        .main {
            flex: 1;
            padding: 20px;
            padding-top: 80px;
            /* IMPORTANT */
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }

        .modal-actions {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }

        .btn-danger {
            background: #e53935;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }

        .btn-cancel {
            background: #ccc;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }

        .topbar {
            position: absolute;
            top: 0;
            left: 240px;
            /* push after sidebar */
            right: 0;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #bbaa3a;
            border-radius: 10px;
        }

        .sidebar {
            position: relative;
            z-index: 999;
            height: 100vh;
        }

        .top-icons {
            display: flex;
            gap: 10px;
        }

        .icon {
            width: 40px;
            height: 40px;
            background: #5d2e1a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .icon:hover {
            background: #B7791F;
        }
    </style>
</head>

<body>




    <div class="container">

        <div class="topbar">
            <div></div>

            <div class="top-icons">
                <div class="icon">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <a href="{{ route('admin.password.edit') }}" class="menu">
                    <i class="fa-solid fa-gear"></i>
                </a>


            </div>
        </div>
        <!-- SIDEBAR -->
        <div class="sidebar">

            <div class="sidebar-header">
                <div class="logo-circle">A</div>
                <h2>Admin</h2>
            </div>
            <a href="{{ route('admin.menu') }}" class="menu {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
                Menu Tab
            </a>
            <a href="{{ route('admin.staffoption.index') }}"
                class="menu {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
                staff
            </a>
            <a href="{{ route('admin.orders') }}" class="menu {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                Orders
            </a>
            <a href="{{ route('admin.history') }}"
                class="menu {{ request()->routeIs('admin.history') ? 'active' : '' }}">
                Order History
            </a>

            <a href="{{ route('admin.requests') }}"
                class="menu {{ request()->routeIs('admin.requests') ? 'active' : '' }}">
                Requests
            </a>
            <a href="{{ route('admin.receipt.edit') }}"
                class="menu {{ request()->routeIs('admin.receipt.edit') ? 'active' : '' }}">
                Edit Receipt
            </a>

            <a href="{{ route('admin.stats') }}" class="menu {{ request()->routeIs('admin.stats') ? 'active' : '' }}">
                Stats
            </a>

            <div onclick="openLogout()" class="menu logout">
                Logout
            </div>

        </div>

        <!-- MAIN CONTENT -->
        <div class="main">
            @yield('content')
        </div>

    </div>

    <!-- LOGOUT MODAL -->
    <div id="logout-modal" class="modal">
        <div class="modal-box">

            <h3>Are you sure you want to logout?</h3>

            <div class="modal-actions">

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn-danger" type="submit">
                        Yes, Logout
                    </button>
                </form>

                <button class="btn-cancel" onclick="closeLogout()">
                    Cancel
                </button>

            </div>

        </div>
    </div>

    <script>
        function openLogout() {
            document.getElementById('logout-modal').style.display = 'flex';
        }

        function closeLogout() {
            document.getElementById('logout-modal').style.display = 'none';
        }

        window.onclick = function (e) {
            let modal = document.getElementById('logout-modal');
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>


</body>

</html>