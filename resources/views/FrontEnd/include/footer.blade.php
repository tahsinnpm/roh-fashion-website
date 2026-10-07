<style>
    footer p,a{
        font-size:14px !important;

    }


    .app-section a img{
        width: 75%;
    }

    .payment-section img{
        width: 15%;
    }

    
</style>
<footer class="bg-white fotter_bg footer wow fadeIn mb-lg-0 mb-5" data-wow-delay="0.1s">
    <div class="custom_container py-5">
        <div class="row row-cols-lg-5  md:g-5 gy-3 ">
            <div class="col col-12" >
                <a class="navbar-brand" href="{{route('home')}}">
                    <img id="my-img" src="{{asset(get_setting('site_logo')->value)}}" alt="logo" class="main-img"
                          style="width: 200px;">
                </a>
                
                <p class="mt-2">{{get_setting('short_description')->value}}</p>
                <p class="mb-1"><i class="fa fa-map-marker-alt me-3"></i>{{get_setting('business_address')->value}}</p>
                <p class="mb-1"><i class="fa fa-phone-alt me-2"></i>{{get_setting('phone')->value}}</p>
                <p class="mb-1"><i class="fa fa-envelope me-2"></i>{{get_setting('email')->value}}</p>




            </div>

            <div class="col col-6">
                @if(session()->get('language') == 'bangla')
                <h4 class=" mb-3">দ্রুত দেখুন</h4>
                @else
                <h4 class=" mb-3">Quick Links</h4>
                @endif
                <a class="btn btn-link" href="@if(Auth::user() && Auth::user()->role == 3) {{route('dashboard')}} @else {{route('login')}} @endif">
                    @if(session()->get('language') == 'bangla')
                    @if(Auth::user() && Auth::user()->role == 3)ইওর একাউন্ট @else লগইন @endif
                    @else
                    @if(Auth::user() && Auth::user()->role == 3)Your Account @else Log in @endif
                    @endif
                </a>
                
                
                
                
                
                    <a class="btn btn-link" href="{{route('seller.apply')}}">@if(session()->get('language') == 'bangla')
                            সেল করুন
                        @else
                            Become a Seller
                        @endif</a>


                        <!-- @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="{{route('page.affiliate')}}">আফিলিয়েট হিসেবে জয়েন </a>
                @else
                <a class="btn btn-link" href="{{route('page.affiliate')}}">Join as Affiliate</a>
                @endif -->

                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="{{route('page.about')}}">আমাদের সম্পর্কে</a>
                @else
                <a class="btn btn-link" href="{{route('page.about')}}">About Us</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="{{route('page.policy')}}">গোপনীয়তা নীতি</a>
                @else
                <a class="btn btn-link" href="{{route('page.policy')}}">Privacy Policy</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="{{route('page.terms')}}">বিধি-নিষেধ এবং শর্তাবলী</a>
                @else
                <a class="btn btn-link" href="{{route('page.terms')}}">Terms & Condition</a>
                @endif


            </div>
            <div class="col col-6">
                @if(session()->get('language') == 'bangla')
                <h4 class="mb-3">সাহায্য</h4>
                @else
                <h4 class=" mb-3">Help</h4>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="#">পেমেন্ট</a>
                @else
                <a class="btn btn-link" href="#">Payment</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="#">পরিবহন</a>
                @else
                <a class="btn btn-link" href="#">Shipping</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="#">প্রত্যাবর্তন এবং প্রতিস্থাপনি</a>
                @else
                <a class="btn btn-link" href="#">Return & Replacement</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="#">চ্যাট করুন</a>
                @else
                <a class="btn btn-link" href="#">Chat With Us</a>
                @endif
                @if(session()->get('language') == 'bangla')
                <a class="btn btn-link" href="#">{{get_setting('site_name')->value ?? ''}} সাপোর্ট</a>
                @else
                <a class="btn btn-link" href="#">{{get_setting('site_name')->value ?? ''}} Support</a>
                @endif
            </div>

            <div class="col col-6" >
                @if(session()->get('language') == 'bangla')
                <h4 class=" mb-3">যোগাযোগ
                </h4>
                @else
                <h4 class=" mb-3">Contact</h4>
                @endif

                <p><a class="text-dark btn btn-link" href="{{get_setting('facebook_url')->value}}">{{session()->get('language') == 'bangla' ? 'ফেসবুক':'Facebook'}}</a></p>
                <p><a class="text-dark btn btn-link" href="{{get_setting('instagram_url')->value}}">{{session()->get('language') == 'bangla' ? 'ইনস্টাগ্রাম':'Instagram'}}</a></p>
                <p><a class="text-dark btn btn-link" href="{{get_setting('twitter_url')->value}}">{{session()->get('language') == 'bangla' ? 'টুইটার':'Twitter'}}</a></p>
                <p><a class="text-dark btn btn-link" href="{{get_setting('youtube_url')->value}}">{{session()->get('language') == 'bangla' ? 'ইউটিউব':'Youtube'}}</a></p>
                <p><a class="text-dark btn btn-link" href="{{get_setting('linkedin_url')->value}}">{{session()->get('language') == 'bangla' ? 'লিঙ্কডইন':'LinkedIn'}}</a></p>



            </div>

            <div class="col">
                @if(session()->get('language') == 'bangla')
                <h4 class="mb-3">ডাউনলোড করুন</h4>
                @else
                <h4 class="mb-3">Download</h4>
                @endif
                
                
                
                
                
                
                <div class="app-section d-flex">
                    
                    
                    
                    
                    <div class="">
                        <a href="#"><img class="bg-light w-50 btn " src="{{asset('FrontEnd')}}/assect/img/resources/play.png"
                                         alt=""></a>
                        <a href="#"><img class="bg-light mt-2 w-50 btn " src="{{asset('FrontEnd')}}/assect/img/resources/appstore.png"
                                         alt=""></a>
                        <a href="#"><img class="bg-light mt-2 w-50 btn "
                                         src="{{asset('FrontEnd')}}/assect/img/resources/appgallery.png" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="copyright">
            <div class="row">
                <div class="col-md-7 text-center text-md-start mb-3 mb-md-0">
                    &copy; <a class="border-bottom" href="{{route('home')}}">{{get_setting('business_name')->value}}</a> | All Right Reserved 
                </div>
                <div class="col-md-3 text-center text-md-end">
                    <div class="d-flex payment-section justify-content-md-end justify-content-center">
                        <img class="bg-light btn " src="{{asset('FrontEnd')}}/assect/img/resources/visa.png"
                                         alt="">
                        <img class="bg-light btn ms-2" src="{{asset('FrontEnd')}}/assect/img/resources/card.png"
                                         alt="">
                        <img class="bg-light ms-2 btn "
                                         src="{{asset('FrontEnd')}}/assect/img/resources/apple-pay.png" alt="">
                        <img class="bg-light ms-2 btn "
                                         src="{{asset('FrontEnd')}}/assect/img/resources/google-pay.png" alt="">
                    </div>
                </div>
                <div class="col-md-2 text-center text-md-end">
                    <div class="footer-menu">
                        
                        
                        <a href="{{route('page.help')}}">Help</a>
                        <a href="{{route('page.faq')}}">FAQs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
