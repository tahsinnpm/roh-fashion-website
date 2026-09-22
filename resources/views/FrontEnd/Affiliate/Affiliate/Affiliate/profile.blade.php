@extends('FrontEnd.affiliate.master')

@section('title', 'Profile')

@section('content')
    <!-- Main Content -->
    <div class="col-lg-9">
        <div class="profile-card p-0" style="overflow:hidden;">
            <div class="row g-0 align-items-center">
                <div class="col-md-4 text-center py-4" style="background:#f8f9fa;">
                    <div class="avatar-container mb-2">
                        <img src="https://letsenhance.io/static/73136da51c245e80edc6ccfe44888a99/1015f/MainBefore.jpg"
                            alt="Profile Avatar" class="profile-avatar" style="box-shadow:none;border:4px solid #fff;">
                    </div>
                    <div class="profile-name mb-1">Sakib Sheikh</div>
                    <a href="update-profile.html" class="edit-profile-link mt-3 w-75">
                        <i class="bi bi-pencil-fill me-2"></i>Edit Profile
                    </a>
                </div>
                <div class="col-md-8 py-4 px-4">
                    <div class="row profile-info-list" style="max-width:none;margin:0;">
                        <div class="col-sm-6 mb-4">
                            <div class="profile-info-item">
                                <i class="bi bi-envelope text-success"></i>
                                <div>
                                    <div class="fw-bold small text-muted">Email</div>
                                    <div>sakib.sheikh@email.com</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <div class="profile-info-item">
                                <i class="bi bi-telephone text-success"></i>
                                <div>
                                    <div class="fw-bold small text-muted">Phone</div>
                                    <div>+1 234 567 8901</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <div class="profile-info-item">
                                <i class="bi bi-geo-alt text-success"></i>
                                <div>
                                    <div class="fw-bold small text-muted">Address</div>
                                    <div>Hossain Market, Uttar Badda.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <div class="profile-info-item">
                                <i class="bi bi-calendar-event text-success"></i>
                                <div>
                                    <div class="fw-bold small text-muted">Joined</div>
                                    <div>JUN 2025</div>
                                </div>
                            </div>
                        </div>
                        <!-- Add more info fields here if needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
 