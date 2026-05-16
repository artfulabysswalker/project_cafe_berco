<style>
    .nav {
        background: #78350F;
        padding: 14px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        font-family: Arial, sans-serif;
    }

    .nav a {
        color: white;
        text-decoration: none;
        margin-right: 14px;
        font-size: 14px;
        transition: 0.2s;
    }

    .nav a:hover {
        opacity: 0.8;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .badge {
        background: #111;
        padding: 6px 10px;
        border-radius: 10px;
        font-size: 12px;
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .badge-admin { color: gold; }
    .badge-customer { color: #4ade80; }
    .badge-guest { background: #444; }

    .btn {
        padding: 6px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        border: none;
        cursor: pointer;
    }

    .btn-login {
        background: white;
        color: #78350F;
    }

    .btn-register {
        background: #f3f3f3;
        color: #78350F;
    }

    .btn-logout {
        background: #dc2626;
        color: white;
    }
</style>

<nav class="nav">

    {{-- LEFT --}}
    <div>
        <a href="{{ route('home') }}" style="font-weight:bold;">BERCO</a>
    </div>

    {{-- RIGHT --}}
   <div class="nav-right">

    <a href="{{ route('home') }}">Home</a>
    <a href="{{ route('menu.index') }}">Menu</a>
    <a href="{{ route('cart.index') }}">Cart</a>

    {{-- AUTH USER --}}
    @if(Auth::check())
        <span class="badge">
            {{ Auth::user()->name }}

            @if(Auth::user()->id_role == 1)
                <span style="color:gold;">ADMIN</span>
            @elseif(Auth::user()->id_role == 2)
                <span style="color:#4ade80;">CUSTOMER</span>
            @else
                <span>USER</span>
            @endif
        </span>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-logout">Logout</button>
        </form>

    {{-- GUEST --}}
    @elseif(session('is_guest'))
        <span class="badge" style="background:;">
            GUEST MODE
        </span>

    {{-- PUBLIC ONLY --}}
    @else
        <a href="{{ url('/testlogin') }}" class="btn btn-login">Login</a>
        <a href="{{ url('/testregister') }}" class="btn btn-register">Register</a>
    @endif

</div>
    </div>

</nav>