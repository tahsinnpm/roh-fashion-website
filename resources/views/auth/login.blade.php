@extends('FrontEnd.master')

@section('title', 'Login')

@section('content')
@php
    $authLogo = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
@endphp

@include('auth.partials.auth-styles')

<section class="rf-auth" aria-labelledby="rf-auth-title">
    <div class="rf-auth__card rf-auth__card--login">
        <aside class="rf-auth__intro">
            <a class="rf-auth__logo" href="{{ route('home') }}" aria-label="Roh Fashion home">
                <img src="{{ $authLogo }}" alt="Roh Fashion">
            </a>
            <span class="rf-auth__eyebrow">ROH FASHION</span>
            <h1>Welcome back</h1>
            <p>Sign in to continue to your account and pick up where you left off.</p>
            <div class="rf-auth__switch">
                <span>New to Roh Fashion?</span>
                <a href="{{ route('register') }}">Create an account <span aria-hidden="true">→</span></a>
            </div>
        </aside>

        <div class="rf-auth__form-panel">
            <div class="rf-auth__form-heading">
                <span class="rf-auth__eyebrow">YOUR ACCOUNT</span>
                <h2 id="rf-auth-title">Sign in</h2>
                <p>Enter your email or phone number and password.</p>
            </div>

            @if(session('status'))
                <div class="rf-auth__notice" role="status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="rf-auth__form">
                @csrf
                <div class="rf-auth__field">
                    <label for="login-email">Email or phone number</label>
                    <input id="login-email" type="text" name="email" value="{{ old('email') }}" placeholder="Enter your email or phone" autocomplete="username" required autofocus>
                    @error('email')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="login-password">Password</label>
                    <input id="login-password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                    @error('password')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__form-options">
                    <label class="rf-auth__remember" for="remember-me">
                        <input id="remember-me" type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>

                <button class="rf-auth__submit" type="submit">Sign in <span aria-hidden="true">→</span></button>
            </form>

            <p class="rf-auth__mobile-switch">Don't have an account? <a href="{{ route('register') }}">Sign up</a></p>
        </div>
    </div>
</section>
@endsection
