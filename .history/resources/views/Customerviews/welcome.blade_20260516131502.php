<nav class="navbar">

    <a href="{{ route('home') }}" class="logo">
        BERCO
    </a>

    <div class="nav-links">

        <a href="{{ route('home') }}">Beranda</a>

        <a href="{{ route('menu.index') }}">Menu</a>

        <a href="{{ route('cart.index') }}">Keranjang</a>

        @guest
            <a href="{{ url('/testlogin') }}" class="login-btn">
                Masuk
            </a>
        @endguest


        @auth

            <div class="user-box">

                <div class="user-info">
                    <span>{{ Auth::user()->name }}</span>

                    @if(Auth::user()->id_role == 1)
                        <small class="role admin">ADMIN</small>

                    @elseif(Auth::user()->id_role == 2 && !Auth::user()->is_guest)
                        <small class="role customer">CUSTOMER</small>

                    @elseif(Auth::user()->id_role == 2 && Auth::user()->is_guest)
                        <small class="role guest">GUEST MODE</small>

                    @elseif(Auth::user()->id_role == 3)
                        <small class="role staff">STAFF</small>
                    @endif
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="logout-btn">
                        Logout
                    </button>
                </form>

            </div>

        @endauth

    </div>

</nav>