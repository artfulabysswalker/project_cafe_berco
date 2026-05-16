<div style="display:flex;gap:15px;align-items:center;">

    {{-- ALWAYS SHOW LINKS --}}
    <a href="{{ route('home') }}" style="color:white;">Home</a>
    <a href="{{ route('menu.index') }}" style="color:white;">Menu</a>
    <a href="{{ route('cart.index') }}" style="color:white;">Cart</a>

    {{-- 1. AUTH USER (REAL LOGIN) --}}
    @auth
        <span style="background:#111;padding:6px 12px;border-radius:10px;font-size:12px;display:flex;gap:6px;align-items:center;">
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

    {{-- 2. GUEST MODE (ONLY IF NOT AUTH) --}}
    @if(!Auth::check() && session('is_guest'))
        <span style="background:#444;padding:6px 10px;border-radius:10px;">
            GUEST MODE
        </span>
    @endif

    {{-- 3. NOT LOGGED IN AT ALL --}}
    @if(!Auth::check() && !session('is_guest'))
        <a href="{{ url('/testlogin') }}"
           style="background:white;color:#78350F;padding:6px 12px;border-radius:8px;text-decoration:none;">
            Login
        </a>

        <a href="{{ url('/testregister') }}"
           style="background:#f3f3f3;color:#78350F;padding:6px 12px;border-radius:8px;text-decoration:none;">
            Register
        </a>
    @endif

</div>