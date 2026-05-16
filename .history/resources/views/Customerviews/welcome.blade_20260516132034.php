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

        {{-- NOT LOGGED IN --}}
        @guest
            <a href="{{ url('/testlogin') }}"
               style="background:white;color:#78350F;padding:6px 12px;border-radius:8px;text-decoration:none;">
                Login
            </a>
        @endguest

        {{-- LOGGED IN USER --}}
        @auth
            <span style="font-size:12px;background:#111;padding:6px 12px;border-radius:10px;display:flex;gap:6px;align-items:center;">

                {{ Auth::user()->name }}

                @if(Auth::user()->id_role == 1)
                    <span style="color:gold;">ADMIN</span>

                @elseif(Auth::user()->id_role == 2)
                    <span style="color:#4ade80;">CUSTOMER</span>

                @else
                    <span style="color:#fbbf24;">USER</span>
                @endif

            </span>

            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button style="background:red;color:white;border:none;padding:6px 10px;border-radius:8px;">
                    Logout
                </button>
            </form>
        @endauth

        {{-- GUEST MODE (SESSION BASED) --}}
        @if(session('is_guest') && !Auth::check())
            <span style="background:#444;padding:6px 10px;border-radius:10px;">
                GUEST MODE
            </span>
        @endif

    </div>

</nav>