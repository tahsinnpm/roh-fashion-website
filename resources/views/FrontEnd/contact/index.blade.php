@extends('FrontEnd.master')
@section('title')
    Contact Us
@endsection
@section('content')
    <!-- Header Start -->
    <div class="container-fluid py-5 page-header">
        <div class="container ">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <h2 class="display-3 fw-bold">{{get_setting('site_name')->value ?? ''}}</h2>
                    <h5 class="display-6 fw-semibold">Happy Shopping</h5>
                    <div class="d-flex justify-content-center mt-3">
                        <p class="m-0"><a href="{{route('home')}}">Home</a></p>
                        <p class="m-0 px-2">-</p>
                        <p class="m-0">Contact Us</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->


    <!-- Contact Information Start -->
    <section class="container my-5">
        <div class="pt-5">
            <div class="text-center">
                <h1 class="mb-5">Contact For Any Query</h1>
                <h4 class="text-success">{{session('message')}}</h4>
            </div>
            <div class="row mt-5 g-5">
                <div class="col-lg-4 col-md-6">
                    <h3>Get In Touch</h3>
                    <p class="contact-text mb-4 mt-4">{{get_setting('short_description')->value}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary"
                             style="width: 50px; height: 50px;">
                            <i class="fa fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p>{{get_setting('business_address')->value}}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary"
                             style="width: 50px; height: 50px;">
                            <i class="fa fa-phone-alt text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p>{{get_setting('phone')->value}}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center flex-shrink-0 bg-primary"
                             style="width: 50px; height: 50px;">
                            <i class="fa fa-envelope-open text-white"></i>
                        </div>
                        <div class="ms-3">
                            <p>{{get_setting('email')->value}}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-12">
                    <form action="{{route('message.submit')}}" method="post">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="name" class="form-control" id="name" placeholder="Your Name">
                                    <label for="name">Your Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Your Email" required>
                                    <label for="email">Your Email</label>
                                    @error('email')
                                        <div class="invalid-feedback" role="alert">
                                            {{$message}}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" id="subject" placeholder="Subject" required>
                                    <label for="subject">Subject</label>
                                    @error('subject')
                                    <div class="invalid-feedback" role="alert">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" placeholder="Leave a message here" id="message"
                                                  style="height: 150px" required></textarea>
                                    <label for="message">Message</label>
                                    @error('message')
                                    <div class="invalid-feedback" role="alert">
                                        {{$message}}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-primary py-3 px-5 text-white" type="submit">Send Message</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Information End-->
@endsection

