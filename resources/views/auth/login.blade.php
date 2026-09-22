@extends('FrontEnd.master')
@section('content')
@section('title')
Login
@endsection

<style>
    body {
        background: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
        min-height: 100vh;
    }

    .login-bg {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #EAC3BE;
        background-size: cover;
        padding: 40px 0;
    }

    .login-card-container {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        background: rgba(255,255,255,0.15);
        border-radius: 30px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        padding: 0;
        max-width: 900px;
        margin: auto;
        width: 100%;
    }

    .login-left {
        flex: 1 1 300px;
        min-width: 300px;
        background: linear-gradient(135deg, #e96443 0%, #904e95 100%);
        color: #fff;
        border-radius: 30px 0 0 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 30px;
        text-align: center;
    }

    .login-logo {
        width: 120px;
        margin-bottom: 25px;
        
        background: rgba(255,255,255,0.2);
        padding: 10px;
    }

    .login-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 18px;
        color: #fff;
        letter-spacing: 1px;
    }

    .login-text {
        font-size: 1rem;
        margin-bottom: 25px;
        color: #f8e9e9;
    }

    .signup-btn {
        background: #fff;
        color: #e96443;
        padding: 12px 36px;
        border: none;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1rem;
        margin-top: 18px;
        transition: background 0.2s, color 0.2s;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .signup-btn:hover {
        background: #e96443;
        color: #fff;
    }

    .login-right {
        flex: 1 1 350px;
        min-width: 350px;
        background: #fff;
        border-radius: 0 30px 30px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 50px 40px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.10);
    }

    .login-form {
        width: 100%;
        max-width: 350px;
    }

    .form-label {
        font-weight: 600;
        color: #904e95;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        height: 48px;
        border-radius: 25px;
        border: 1.5px solid #e96443;
        padding: 0 18px;
        margin-bottom: 18px;
        font-size: 1rem;
        background: #f9f9f9;
        transition: border 0.2s;
    }
    .form-input:focus {
        border: 2px solid #904e95;
        outline: none;
        background: #fff;
    }

    .login-btn {
        background: linear-gradient(90deg, #e96443 0%, #904e95 100%);
        border: none;
        color: white;
        border-radius: 25px;
        padding: 12px 0;
        font-size: 1.1rem;
        font-weight: 700;
        width: 100%;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(233,100,67,0.08);
        transition: background 0.2s;
    }
    .login-btn:hover {
        background: linear-gradient(90deg, #904e95 0%, #e96443 100%);
    }

    .form-check-label {
        color: #904e95;
        font-size: 0.98rem;
    }

    .forgot-link {
        color: #e96443;
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s;
    }
    .forgot-link:hover {
        color: #904e95;
        text-decoration: underline;
    }

    @media (max-width: 900px) {
        .login-card-container {
            flex-direction: column;
            border-radius: 30px;
        }
        .login-left, .login-right {
            border-radius: 30px 30px 0 0;
            min-width: unset;
            padding: 40px 20px;
        }
        .login-right {
            border-radius: 0 0 30px 30px;
        }
    }
</style>

<div class="login-bg">
    <div class="login-card-container">
        <div class="login-left">
            <img src="http://127.0.0.1:8000/upload/setting/logo/1745404812Asset 2.png" alt="Logo" class="login-logo">
            <h2 class="login-title">Welcome Back!</h2>
            <p class="login-text">To keep connected with us, please login with your personal info.</p>
            <span style="color:#fff;">Don't have an account?</span>
            <a href="{{ route('register') }}" class="signup-btn">Sign Up</a>
        </div>

        <div class="login-right">
            <h2 class="login-title" style="color:#e96443;">User Login</h2>
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" 
                           class="form-input" 
                           name="email" 
                           placeholder="Login@gmail.com" 
                           value="{{ old('email') }}" 
                           required>
                    @error('email')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" 
                           class="form-input" 
                           name="password" 
                           placeholder="Your password" 
                           required>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link"><small>Forgot your password?</small></a>
                </div>

                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</div>

@endsection