@extends('FrontEnd.master')
@section('title')
Register
@endsection
@section('content')
<style>
    .register-container {
        background-color: #eac3be;
        padding: 20px;
    }

    .register-grid {
        display: grid;
        grid-template-columns: 1fr;
        background: #eac3be;
        gap: 20px;
    }

    .register-left {
        /* background-image: url('https://t4.ftcdn.net/jpg/01/16/88/37/360_F_116883786_wuckft1sNw1ouQfJ6FuquZnxea3qBlxy.jpg'); */
        background-color: #EAC3BE;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        border-radius: 15px;
        position: relative;
        min-height: 400px;
    }

    .register-left::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 15px;
    }

    .register-left>* {
        position: relative;
        z-index: 1;
    }

    .register-right {
        background: white;
        padding: 20px;
        border-radius: 15px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        height: 50px;
        border-radius: 100px;
        padding: 0 20px;
        width: 100%;
        border: 1px solid #ddd;
    }

    .register-logo {
        width: 120px;
        margin-bottom: 20px;
    }

    .register-title {
        margin-bottom: 20px;
        font-weight: 700;
        color: white;
        text-align: center;
        font-size: 24px;
    }

    .register-text {
        font-size: 14px;
        margin-bottom: 20px;
        text-align: center;
        color: white;
    }

    .signin-btn {
        background: transparent;
        color: white;
        padding: 12px 30px;
        border: 2px solid white;
        border-radius: 50px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .submit-btn {
        background: #D83F26;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 10px;
        transition: 0.3s;
        width: 100%;
    }

    .password-hint {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    @media (min-width: 768px) {
        .register-container {
            padding: 50px;
        }

        .register-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .register-logo {
            width: 200px;
        }

        .register-right {
            padding: 40px;
        }

        .register-title {
            font-size: 28px;
        }
    }
</style>

<div class="register-container">
    <div class="register-grid">
        <div class="register-left">
            <img src="http://127.0.0.1:8000/upload/setting/logo/1745404812Asset 2.png" alt="Logo" class="register-logo">
            <h2 class="register-title">Welcome Back!</h2>
            <p class="register-text">To keep connected with us please sign up with your personal info</p>
            <span style="color: white;">Already have an account?</span>
            <a href="{{ route('login') }}" class="signin-btn">Sign In</a>
        </div>

        <div class="register-right">
            <h2 style="margin-bottom:20px; font-weight:700; color:#D83F26; text-align:center;">Create Account</h2>
            <form method="POST" action="{{ route('register') }}" class="form-grid">
                @csrf
                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Full Name *</label>
                    <input class="form-control" type="text" name="name" placeholder="Name" value="{{ old('name') }}">
                    @error('name')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Username *</label>
                    <input class="form-control" name="username" placeholder="Username" value="{{ old('username') }}">
                    @error('username')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Email *</label>
                    <input class="form-control" type="email" name="email" placeholder="Email" value="{{ old('email') }}">
                    @error('email')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Phone *</label>
                    <input class="form-control" type="number" name="phone" placeholder="Phone Number" value="{{ old('phone') }}">
                    @error('phone')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Password *</label>
                    <input class="form-control" type="password" name="password" placeholder="Password" autocomplete="new-password">
                    <span class="password-hint">Password must be at least 8 characters</span>
                    @error('password')
                    <div class="text-danger" style="font-weight: bold;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="mb-2 font-sm color-gray-700">Re-Password *</label>
                    <input class="form-control" type="password" name="password_confirmation" placeholder="Confirm password">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="flexCheckDefault" required>
                        <label class="form-check-label" for="flexCheckDefault">
                            By clicking Register button, you agree our terms and policy,
                        </label>
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1; text-align: center;">
                    <button type="submit" class="submit-btn">Submit & Register</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    (function() {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>
@endpush