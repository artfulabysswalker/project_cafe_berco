<head>
    @csrf
    <link rel="stylesheet" href="/css/auth.css">
    <style>
        /* --- RESET --- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        /* --- LOGIN PAGE --- */
        .login-page {
            background-color: #FFFCEC;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .login-logo-circle {
            background-color: #5d2e1a;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 35px;
        }

        .login-header h1 {
            color: #5d2e1a;
            margin-bottom: 5px;
        }

        .login-header p {
            color: #718096;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .login-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E8F0;
            background: #F7FAFC;
            border-radius: 10px;
        }

        .btn-login-submit {
            width: 100%;
            background: #5d2e1a;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-guest {
            width: 100%;
            background: white;
            border: 1px solid #E2E8F0;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
        }

        .alert-warning {
            background-color: #FFFBEB;
            border: 1px solid #FEF3C7;
            color: #92400E;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .back-link {
            display: block;
            margin-top: 25px;
            color: #B7791F;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<div class="login-page">
    <div class="login-container">

        <!-- Logo (optional) -->
        <div class="login-logo-circle">
            🔐
        </div>

        <div class="login-header">
            <h1>Staff Login</h1>
            <p>Enter your credentials to continue</p>
        </div>

        <!-- Global Error -->
        @if(session('error'))
            <div class="alert-warning">
                {{ session('error') }}
            </div>
        @endif

        <div class="login-card">

            <form method="POST" action="{{ route('staff.login') }}">
                @csrf

                <!-- Username -->
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required>

                    @error('username')
                        <small style="color:red;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>

                    @error('password')
                        <small style="color:red;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login-submit">
                    Login
                </button>

                <!-- Reset -->
                <a href="/staff/reset-request" class="btn-guest"
              
                    style="display:block; margin-top:10px; text-decoration:none; text-align:center;">
                    Reset password request
                      @csrf
                </a>

            </form>

        </div>

        <!-- Back link -->
        <a href="/" class="back-link">← Back to Home</a>

    </div>
</div>