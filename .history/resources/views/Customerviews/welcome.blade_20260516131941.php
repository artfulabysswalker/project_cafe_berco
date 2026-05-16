<nav style="background:#78350F;padding:15px;display:flex;justify-content:space-between;align-items:center;color:white;">

    <div>
        <a href="{{ route('home') }}" style="color:white;font-weight:bold;text-decoration:none;">
            BERCO
        </a>
    </div>

    <div style="display:flex;gap:15px;align-items:center;">

        <a href="{{ route('home') }}" style="color:white;">Home</a>
        <a href="{{ route('menu.index') }}" style="color:white;">Menu</a>
        <a href="{{ route('cart.index') }}" style="color:white;">Cart</a>

        @guest
            <a href="{{ url('/testlogin') }}"
               style="background:white;color:#78350F;padding:6px 12px;border-radius:8px;text-decoration:none;">
                Login
            </a>
        @endguest

        @auth
            <span style="font-size:12px;background:#111;padding:4px 10px;border-radius:10px;">
                {{ Auth::user()->name }}

                @if(Auth::user()->id_role == 1)
                    (ADMIN)
                @elseif(Auth::user()->id_role == 2)
                    (CUSTOMER)
                @elseif(Auth::user()->is_guest)
                    (GUEST)
                @else
                    (USER)
                @endif
            </span>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button style="background:red;color:white;border:none;padding:6px 10px;border-radius:8px;">
                    Logout
                </button>
            </form>
        @endauth

    </div>
@if(session('is_guest'))
    <span style="background:#444;padding:4px 10px;border-radius:10px;">
        GUEST MODE
    </span>
@endif
</nav>