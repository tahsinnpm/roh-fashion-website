@extends('FrontEnd.master')
@section('title')
Become A Seller
@endsection
@section('content')
<div class="seller-app-wrapper">
    <aside class="seller-app-sidebar">
        <div class="seller-app-logo">
            <i class="fas fa-store"></i>
            <span>Online Hut BD</span>
        </div>
        <div class="seller-app-illustration">
            <img src="https://img.freepik.com/free-vector/online-shop-application-smartphone-concept_23-2148505081.jpg?w=740" alt="Seller Illustration">
        </div>
        <div class="seller-app-sidebar-footer">
            <span>Already a seller?</span>
            <a href="{{ route('login') }}">Sign In</a>
        </div>
    </aside>
    <main class="seller-app-main">
        <div class="seller-app-card">
            <div class="seller-app-header">
                <h1>Become a Seller</h1>
                <p>Grow your business with us. Fill out the form to join our seller community.</p>
            </div>
            <form method="post" action="{{ route('become.a.seller') }}" enctype="multipart/form-data" class="seller-app-form">
                @csrf
                <!-- Step 1: Basic Info -->
                <div class="seller-app-step">
                    <div class="seller-app-step-title">
                        <span class="step-circle">1</span>
                        <span>Basic Information</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="shop_owner_name" value="{{old('shop_owner_name')}}" required class="form-control" id="shop_owner_name" placeholder="Shop Owner Name" autocomplete="off">
                                <label for="shop_owner_name">Shop Owner Name <span class="text-danger">*</span></label>
                                @error('shop_owner_name')
                                <div class="modern-error">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="shop_name" value="{{old('shop_name')}}" required class="form-control" id="shop_name" placeholder="Shop Name" autocomplete="off">
                                <label for="shop_name">Shop Name <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="phone" value="{{old('phone')}}" required class="form-control" id="phone" placeholder="Phone" autocomplete="off">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" value="{{old('email')}}" required class="form-control" id="email" placeholder="Email" autocomplete="off">
                                <label for="email">Email <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="address" type="text" name="address" required placeholder="Address" value="{{old('address')}}" autocomplete="off">
                                <label for="address">Address <span class="text-danger">*</span></label>
                                @error('address')
                                <div class="modern-error">{{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input class="form-control" id="description" type="text" name="description" value="{{old('description')}}" placeholder="Description" autocomplete="off">
                                <label for="description">Description</label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 2: Documents -->
                <div class="seller-app-step mt-4">
                    <div class="seller-app-step-title">
                        <span class="step-circle">2</span>
                        <span>Upload Documents</span>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="modern-file-upload upload-container text-center">
                                <img id="showImage" src="{{url('upload/no_image.jpg')}}" alt="Shop Profile"
                                    class="seller-app-upload-img">
                                <label class="modern-file-label" for="shop_profile">Shop Owner <span class="text-danger">*</span></label>
                                <input type="file" name="shop_profile" class="modern-file-input" id="shop_profile" required>
                                <span class="modern-file-name" id="fileName1"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-file-upload upload-container text-center">
                                <img id="showImage2" src="{{url('upload/no_image.jpg')}}" alt="Shop Cover"
                                    class="seller-app-upload-img">
                                <label class="modern-file-label" for="shop_cover">Shop Cover <span class="text-danger">*</span></label>
                                <input type="file" name="shop_cover" class="modern-file-input" id="shop_cover" required>
                                <span class="modern-file-name" id="fileName2"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-file-upload upload-container text-center">
                                <img id="showImage3" class="seller-app-upload-img" src="{{url('upload/no_image.jpg')}}" alt="Card image cap">
                                <label class="modern-file-label" for="image3">NID Card</label>
                                <input name="nid" class="modern-file-input" type="file" id="image3">
                                <span class="modern-file-name" id="fileName3"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modern-file-upload upload-container text-center">
                                <img id="showImage4" class="seller-app-upload-img" src="{{url('upload/no_image.jpg')}}" alt="Card image cap">
                                <label class="modern-file-label" for="image4">Trade License</label>
                                <input name="trade_license" class="modern-file-input" type="file" id="image4">
                                <span class="modern-file-name" id="fileName4"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Step 3: Password -->
                <div class="seller-app-step mt-4">
                    <div class="seller-app-step-title">
                        <span class="step-circle">3</span>
                        <span>Set Password</span>
                    </div>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-floating position-relative">
                                <input type="password" name="password" required class="form-control" id="password" placeholder="Password" autocomplete="new-password">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <button type="button" class="password-toggle" onclick="togglePassword()" tabindex="-1">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                                <small class="text-muted d-block mt-1">(Min 6 characters)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="password" name="password_confirmation" required class="form-control" id="password_confirmation" placeholder="Confirm Password" autocomplete="new-password">
                                <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn seller-app-btn mt-4">
                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                </button>
            </form>
        </div>
    </main>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --accent: #f7f7fa;
        --danger: #d83f26;
        --card-radius: 18px;
        --shadow: 0 6px 32px rgba(80, 80, 120, 0.09);
    }
    body {
        background: linear-gradient(120deg, #f7f7fa 0%, #e3e6f3 100%);
    }
    .seller-app-wrapper {
        display: flex;
        min-height: 90vh;
        max-width: 1100px;
        margin: 2.5rem auto;
        background: #fff;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .seller-app-sidebar {
        flex: 0 0 320px;
        background: linear-gradient(135deg, var(--primary) 60%, var(--secondary) 100%);
        color: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 2.5rem 1.5rem 1.5rem 1.5rem;
        position: relative;
    }
    .seller-app-logo {
        font-size: 2.1rem;
        font-weight: 700;
        letter-spacing: 1px;
        margin-bottom: 2.5rem;
        display: flex;
        align-items: center;
        gap: 0.7rem;
    }
    .seller-app-logo i {
        font-size: 2.3rem;
        color: #fff;
    }
    .seller-app-logo span {
        color: #fff;
    }
    .seller-app-illustration img {
        width: 100%;
        max-width: 220px;
        border-radius: 12px;
        margin-bottom: 2.5rem;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    }
    .seller-app-sidebar-footer {
        margin-top: auto;
        text-align: center;
        font-size: 1rem;
    }
    .seller-app-sidebar-footer a {
        color: #fff;
        font-weight: 600;
        text-decoration: underline;
        margin-left: 0.3rem;
    }
    .seller-app-main {
        flex: 1 1 0;
        padding: 3.5rem 2.5rem;
        background: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .seller-app-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(80, 80, 120, 0.07);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 650px;
    }
    .seller-app-header h1 {
        color: var(--primary);
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .seller-app-header p {
        color: #555;
        font-size: 1.08rem;
        margin-bottom: 2.2rem;
    }
    .seller-app-step {
        margin-bottom: 0.5rem;
    }
    .seller-app-step-title {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-size: 1.12rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1.1rem;
    }
    .step-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: var(--secondary);
        color: #fff;
        font-size: 1.1rem;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(80,80,120,0.08);
    }
    .seller-app-upload-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 0.7rem;
        border: 2px solid #e3e6f3;
        background: #f7f7fa;
    }
    .modern-file-upload .modern-file-label {
        display: inline-block;
        background: #f7f7fa;
        color: var(--primary);
        border: 2px dashed var(--primary);
        border-radius: 8px;
        padding: 0.5rem 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
        margin-bottom: 0.2rem;
        font-size: 1rem;
    }
    .modern-file-upload .modern-file-label:hover {
        background: var(--primary);
        color: #fff;
    }
    .modern-file-upload .modern-file-input {
        display: none;
    }
    .modern-file-upload .modern-file-name {
        font-size: 0.92rem;
        color: #666;
        margin-top: 0.1rem;
    }
    .modern-error {
        color: var(--danger);
        font-size: 0.92rem;
        margin-top: 0.3rem;
    }
    .seller-app-btn {
        background: linear-gradient(90deg, var(--primary) 60%, var(--secondary) 100%);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
        padding: 0.85rem;
        width: 100%;
        box-shadow: 0 4px 16px rgba(80, 80, 120, 0.08);
        transition: background 0.2s, box-shadow 0.2s;
        position: relative;
    }
    .seller-app-btn:hover {
        background: linear-gradient(90deg, #3e429c 60%, #6f74c9 100%);
        box-shadow: 0 8px 24px rgba(80, 80, 120, 0.13);
    }
    .password-toggle {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #bbb;
        cursor: pointer;
        z-index: 10;
    }
    .password-toggle:hover {
        color: var(--primary);
    }
    @media (max-width: 1100px) {
        .seller-app-wrapper {
            max-width: 98vw;
        }
    }
    @media (max-width: 900px) {
        .seller-app-wrapper {
            flex-direction: column;
            min-height: unset;
        }
        .seller-app-sidebar {
            flex: unset;
            width: 100%;
            padding: 1.5rem 1rem 1rem 1rem;
            border-radius: 0 0 18px 18px;
        }
        .seller-app-main {
            padding: 2rem 0.5rem;
        }
    }
    @media (max-width: 576px) {
        .seller-app-card {
            padding: 1.2rem 0.5rem;
        }
        .seller-app-header h1 {
            font-size: 1.3rem;
        }
        .seller-app-sidebar {
            padding: 1rem 0.2rem 0.7rem 0.2rem;
        }
        .seller-app-logo {
            font-size: 1.2rem;
        }
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
    // Image preview functionality
    function readURL(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    document.querySelectorAll('.modern-file-input').forEach((input, idx) => {
        input.addEventListener('change', function() {
            const previewId = this.closest('.upload-container').querySelector('img').id;
            readURL(this, previewId);
            const fileNameSpan = this.closest('.upload-container').querySelector('.modern-file-name');
            fileNameSpan.textContent = this.files[0] ? this.files[0].name : '';
        });
    });
    document.querySelectorAll('.modern-file-label').forEach(label => {
        label.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.modern-file-input');
            if (input) input.click();
        });
    });
    // Toggle password visibility
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const toggleIcon = document.getElementById("toggleIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
</script>
@endsection