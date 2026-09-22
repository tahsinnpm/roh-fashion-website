document.addEventListener("DOMContentLoaded", function () {
    window.addEventListener("scroll", function () {
        if (window.scrollY > 50) {
            document.getElementById("navbar_top").classList.add("fixed-top");
            // add padding top to show content behind navbar
            navbar_height = document.querySelector(".navbar").offsetHeight;
            document.body.style.paddingTop = navbar_height + "px";
        } else {
            document.getElementById("navbar_top").classList.remove("fixed-top");
            // remove padding top from body
            document.body.style.paddingTop = "0";
        }
    });
});

// services
$(".services").owlCarousel({
    loop: true,
    dots: false,
    autoplay: true,
    nav: false,
    autoplayTimeout: 2000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 3,
            margin: 12,
        },
        600: {
            items: 4,
            margin: 12,
        },
        1000: {
            items: 6,
            margin: 12,
        },
        1400: {
            items: 9,
            margin: 12,
        },
    },
});

// flash sale
$(".flash-sale").owlCarousel({
    loop: true,
    dots: false,
    autoplay: true,
    nav: false,
    autoplayTimeout: 5000,
    responsiveClass: true,
    responsive: {
        0: {
            items: 2,
        },
        600: {
            items: 3,
        },
        1000: {
            items: 5,
        },
        1400: {
            items: 6,
        },
    },
});

$(document).ready(function () {
    // Check if jQuery and slick are loaded
    if (typeof jQuery != "undefined" && typeof jQuery.fn.slick != "undefined") {
        $(".category-slider").slick({
            dots: false,
            infinite: true,
            speed: 500,
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            prevArrow:
                '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
            nextArrow:
                '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 5,
                    },
                },
                {
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 4,
                    },
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3,
                    },
                },
                {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2,
                    },
                },
            ],
        });
    } else {
        console.error("Slick slider or jQuery is not loaded");
    }
});
$(document).ready(function () {
    if (typeof jQuery != "undefined" && typeof jQuery.fn.slick != "undefined") {
        if ($(".featured-slider").length && !$(".featured-slider").hasClass("slick-initialized")) {
            $(".featured-slider").slick({
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                dots: false,
                prevArrow: '<button type="button" class="slick-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>',
                nextArrow: '<button type="button" class="slick-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>',
                responsive: [
                    {
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 4,
                        },
                    },
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 3,
                        },
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 2,
                        },
                    },
                ],
            });

            // Progress bar animation
            var $slider = $(".featured-slider");
            var $progressBar = $(".slider-progress-bar");

            if ($progressBar.length) {
                $slider.on(
                    "beforeChange",
                    function (event, slick, currentSlide, nextSlide) {
                        var calc = ((nextSlide + 1) / slick.slideCount) * 100;
                        $progressBar.css("width", calc + "%");
                    }
                );
            }
        }
    }
});


// $('.services').owlCarousel({
//   loop: true,
//   dots: false,
//   autoplay: true,
//   nav: false,
//   autoplayTimeout: 2000,
//   responsiveClass: true,
//   responsive: {
//     0: {
//       items: 3,
//       margin: 12
//     },
//     600: {
//       items: 4,
//       margin: 12
//     },
//     1000: {
//       items: 6,
//       margin: 12
//     },
//     1400: {
//       items: 8,
//       margin: 12
//     }
//   }
// })

// $('.owl-carousel').owlCarousel({
//     loop: true,
//     dots: false,
//     autoplay: true,
//     nav: false,
//     // autoplayTimeout: 2000,
//     responsiveClass: true,
//     responsive: {
//         0: {
//             items: 2,
//             margin: 12
//         },
//         600: {
//             items: 4,
//             margin: 12
//         },
//         1000: {
//             items: 5,
//             margin: 12
//         },
//         1200: {
//             items: 5,
//             margin: 12
//         }
//     }
// })
