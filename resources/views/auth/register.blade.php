@extends('FrontEnd.master')

@section('title', 'Create account')

@section('content')
@php
    $authLogo = asset(get_setting('site_logo')->value ?: 'upload/setting/logo/1790046067Screenshot_1.png');
@endphp

@include('auth.partials.auth-styles')

<section class="rf-auth" aria-labelledby="rf-auth-title">
    <div class="rf-auth__card rf-auth__card--register">
        <aside class="rf-auth__intro">
            <a class="rf-auth__logo" href="{{ route('home') }}" aria-label="Roh Fashion home">
                <img src="{{ $authLogo }}" alt="Roh Fashion">
            </a>
            <span class="rf-auth__eyebrow">ROH FASHION</span>
            <h1>Make it yours</h1>
            <p>Create an account to save your details and enjoy a smoother shopping experience.</p>
            <div class="rf-auth__switch">
                <span>Already have an account?</span>
                <a href="{{ route('login') }}">Sign in <span aria-hidden="true">→</span></a>
            </div>
        </aside>

        <div class="rf-auth__form-panel">
            <div class="rf-auth__form-heading">
                <span class="rf-auth__eyebrow">JOIN ROH FASHION</span>
                <h2 id="rf-auth-title">Create your account</h2>
                <p>Fill in your details to get started.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="rf-auth__form rf-auth__form--register">
                @csrf
                <div class="rf-auth__field">
                    <label for="register-name">Full name</label>
                    <input id="register-name" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" autocomplete="name" required>
                    @error('name')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="register-username">Username</label>
                    <input id="register-username" type="text" name="username" value="{{ old('username') }}" placeholder="Choose a username" autocomplete="username" required>
                    @error('username')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="register-email">Email address</label>
                    <input id="register-email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" autocomplete="email" required>
                    @error('email')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="register-phone">Phone number</label>
                    <input id="register-phone" type="tel" name="phone" value="{{ old('phone') }}" placeholder="Your phone number" autocomplete="tel" required>
                    @error('phone')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="register-password">Password</label>
                    <input id="register-password" type="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
                    <span class="rf-auth__hint">Use at least 8 characters.</span>
                    @error('password')<span class="rf-auth__error">{{ $message }}</span>@enderror
                </div>

                <div class="rf-auth__field">
                    <label for="register-password-confirmation">Confirm password</label>
                    <input id="register-password-confirmation" type="password" name="password_confirmation" placeholder="Enter your password again" autocomplete="new-password" required>
                </div>

                <label class="rf-auth__terms">
                    <input type="checkbox" required>
                    <span>By creating an account, you agree to our terms and privacy policy.</span>
                </label>

                <button class="rf-auth__submit" type="submit">Create account <span aria-hidden="true">→</span></button>
            </form>

            <p class="rf-auth__mobile-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </div>
</section>
@endsection
