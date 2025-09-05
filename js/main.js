(function ($) {
  "use strict";

  $(document).ready(function () {
    /**-----------------------------
     *  Navbar fix
     * ---------------------------*/
    $(document).on(
      "click",
      ".navbar-area .navbar-nav li.menu-item-has-children>a",
      function (e) {
        e.preventDefault();
      }
    );

    /*-------------------------------------
            menu
        -------------------------------------*/
    $(".navbar-area .menu").on("click", function () {
      $(this).toggleClass("open");
      $(".navbar-area .navbar-collapse").toggleClass("sopen");
    });

    // mobile menu
    if ($(window).width() < 992) {
      $(".in-mobile").clone().appendTo(".sidebar-inner");
      $(".in-mobile ul li.menu-item-has-children").append(
        '<i class="fas fa-chevron-right"></i>'
      );
      $('<i class="fas fa-chevron-right"></i>').insertAfter("");

      $(".menu-item-has-children a").on("click", function (e) {
        // e.preventDefault();

        $(this).siblings(".sub-menu").animate(
          {
            height: "toggle",
          },
          300
        );
      });
    }

    var menutoggle = $(".menu-toggle");
    var mainmenu = $(".navbar-nav");

    menutoggle.on("click", function () {
      if (menutoggle.hasClass("is-active")) {
        mainmenu.removeClass("menu-open");
      } else {
        mainmenu.addClass("menu-open");
      }
    });

    /*--------------------------------------------------
            select onput
        ---------------------------------------------------*/
    if ($(".single-select").length) {
      $(".single-select").niceSelect();
    }

    /*--------------------------------------------
            Search Popup
        ---------------------------------------------*/
    var bodyOvrelay = $("#body-overlay");
    var searchPopup = $("#td-search-popup");
    var sidebarMenu = $("#sidebar-menu");
    var sidebarMenuMobile = $("#sidebar-menu-mobile");

    $(document).on("click", "#body-overlay", function (e) {
      e.preventDefault();
      bodyOvrelay.removeClass("active");
      searchPopup.removeClass("active");
      sidebarMenu.removeClass("active");
      sidebarMenuMobile.removeClass("active");
    });
    $(document).on("click", ".search-bar-btn", function (e) {
      e.preventDefault();
      searchPopup.addClass("active");
      bodyOvrelay.addClass("active");
    });

    // sidebar menu
    $(document).on("click", ".sidebar-menu-close", function (e) {
      e.preventDefault();
      bodyOvrelay.removeClass("active");
      sidebarMenu.removeClass("active");
      sidebarMenuMobile.removeClass("active");
    });
    $(document).on("click", "#navigation-button", function (e) {
      e.preventDefault();
      sidebarMenu.addClass("active");
      bodyOvrelay.addClass("active");
    });

    //sidebar menu mobile
    $(document).on("click", "#navigation-button-mobile", function (e) {
      e.preventDefault();
      sidebarMenuMobile.addClass("active");
      bodyOvrelay.addClass("active");
    });

    /* -----------------------------------------------------
            Variables
        ----------------------------------------------------- */
    var leftArrow = '<i class="fa fa-angle-left"></i>';
    var rightArrow = '<i class="fa fa-angle-right"></i>';
    var bannerleftArrow = '<img src="../images/img/banner/left.png" alt="img">';
    var bannerrightArrow =
      '<img src="../images/img/banner/right.png" alt="img">';

    /*--------------------------------------------
            banner slider
        ---------------------------------------------*/
    $(".banner-slider").owlCarousel({
      loop: true,
      nav: false,
      dots: true,
      autoplay: true,
      smartSpeed: 1500,
      navText: [leftArrow, rightArrow],
      responsive: {
        0: {
          items: 1,
        },
        600: {
          items: 1,
        },
        992: {
          items: 1,
        },
      },
    });

    /*--------------------------------------------
            inplay slider
        ---------------------------------------------*/
    $(".inplay-slider").owlCarousel({
      loop: true,
      nav: true,
      margin: 0,
      dots: false,
      smartSpeed: 1500,
      navText: [leftArrow, rightArrow],
      responsive: {
        0: {
          items: 1,
        },
        370: {
          items: 2,
        },
        600: {
          items: 3,
        },
        992: {
          items: 2,
        },
        1100: {
          items: 2,
        },
        1300: {
          items: 4,
        },
        1600: {
          items: 7,
        },
      },
    });

    /*--------------------------------------------
            testimonial slider 2
        ---------------------------------------------*/
    $(".testimonial-slider-2").owlCarousel({
      loop: true,
      margin: 10,
      nav: false,
      dots: true,
      smartSpeed: 1500,
      navText: [leftArrow, rightArrow],
      responsive: {
        0: {
          items: 1,
        },
        600: {
          items: 2,
        },
        992: {
          items: 2,
        },
      },
    });

    /*--------------------------------------------
            client slider
        ---------------------------------------------*/
    $(".client-slider").owlCarousel({
      loop: true,
      margin: 10,
      nav: false,
      dots: false,
      smartSpeed: 1500,
      navText: [leftArrow, rightArrow],
      responsive: {
        0: {
          items: 2,
        },
        600: {
          items: 4,
        },
        992: {
          items: 6,
        },
      },
    });

    /*--------------------------------------------
            slots slider 1
        ---------------------------------------------*/

    $(".owl-carousel-slots-r1").owlCarousel({
      loop: true,
      margin: 12,
      nav: false,
      dots: false,
      stagePadding: 30,
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
      },
    });

    /*--------------------------------------------
            slots slider 2
        ---------------------------------------------*/

    $(".owl-carousel-slots-r2").owlCarousel({
      loop: true,
      margin: 12,
      nav: false,
      dots: false,
      stagePadding: 30,
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
      },
    });

    /*------------------------------------------------
            Magnific JS
        ------------------------------------------------*/
    $(".video-play-btn-hover").magnificPopup({
      type: "iframe",
      removalDelay: 260,
      mainClass: "mfp-zoom-in",
    });
    $.extend(true, $.magnificPopup.defaults, {
      iframe: {
        patterns: {
          youtube: {
            index: "youtube.com/",
            id: "v=",
            src: "https://www.youtube.com/embed/Wimkqo8gDZ0",
          },
        },
      },
    });

    /* -----------------------------------------
            fact counter
        ----------------------------------------- */
    $(".counter").counterUp({
      delay: 15,
      time: 2000,
    });

    /*-------------------------------------------------
            wow js init
        --------------------------------------------------*/
    new WOW().init();

    /*-------------------------------------------------
            chart js init
        --------------------------------------------------*/
    // var ctx = document.getElementById('myChart').getContext('2d');
    // var chart = new Chart(ctx, {
    // type: 'line', // also try bar or other graph types

    // data: {
    //     labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
    //     datasets: [{
    //         backgroundColor: '#4d7cfe0d',
    //         borderColor: '#4D7CFE',
    //         data: [26.4, 39.8, 66.8, 66.4, 40.6, 55.2, 77.4],
    //     }]
    // },
    // // Configuration options
    // options: {
    //     layout: {
    //         padding: 10,
    //     },
    //     legend: {
    //         position: 'bottom',
    //     },
    //     title: {
    //         display: true,
    //     },
    // }
    // });

    /*----------------------------------------
           back to top
        ----------------------------------------*/
    $(document).on("click", ".back-to-top", function () {
      $("html,body").animate(
        {
          scrollTop: 0,
        },
        2000
      );
    });
  });

  $(window).on("scroll", function () {
    /*---------------------------------------
            back-to-top
        -----------------------------------------*/
    var ScrollTop = $(".back-to-top");
    if ($(window).scrollTop() > 1000) {
      //ScrollTop.fadeIn(1000);
    } else {
      ScrollTop.fadeOut(1000);
    }

    /*---------------------------------------
            sticky-active
        -----------------------------------------*/
    var scroll = $(window).scrollTop();
    if (scroll < 445) {
      $(".navbar").removeClass("sticky-active");
    } else {
      $(".navbar").addClass("sticky-active");
    }
  });

  $(window).on("load", function () {
    /*-----------------
            preloader
        ------------------*/
    var preLoder = $("#preloader");
    preLoder.fadeOut(0);

    /*-----------------
            back to top
        ------------------*/
    var backtoTop = $(".back-to-top");
    backtoTop.fadeOut();

    /*---------------------
            Cancel Preloader
        ----------------------*/
    $(document).on("click", ".cancel-preloader a", function (e) {
      e.preventDefault();
      $("#preloader").fadeOut(2000);
    });
  });
})(jQuery);
