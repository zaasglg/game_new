<?php
require_once 'auth_check.php';
require_once 'utm_r_tracker.php';

// Инициализация переменных для предотвращения ошибок
$deposit = isset($deposit) ? $deposit : '';
$currency = isset($currency) ? $currency : '';
$bonificaciones = isset($bonificaciones) ? $bonificaciones : '';

// Логирование ошибок в файл
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Устанавливаем заголовок для HTML
header('Content-Type: text/html; charset=UTF-8');

    // Получаем IP-адрес клиента
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Для локальной разработки устанавливаем тестовый IP
    if ($ip === '127.0.0.1' || $ip === '::1' || strpos($ip, '192.168.') === 0) {
        $country_code = 'AR'; // Аргентина по умолчанию для тестирования
    } else {
        try {
            // Запрос к API для определения страны
            $url = "http://ip-api.com/json/{$ip}?fields=status,message,countryCode";
            $response = @file_get_contents($url);
            
            if ($response === false) {
                throw new Exception('Failed to fetch IP data from API');
            }

            $data = json_decode($response, true);
            
            if ($data['status'] !== 'success') {
                throw new Exception('API error: ' . ($data['message'] ?? 'Unknown error'));
            }

            $country_code = $data['countryCode'];
        } catch (Exception $e) {
            // В случае ошибки используем Аргентину по умолчанию
            error_log("IP API Error: " . $e->getMessage());
            $country_code = 'AR';
        }
    }



    $script_tag = <<<EOD
      <script src="https://livechatv2.chat2desk.com/packs/ie-11-support.js"></script>
      <script>
        window.chat24_token = "0f7f5dc5cec44ea9b6e7fe014e4f4af2";
        window.chat24_url = "https://livechatv2.chat2desk.com";
        window.chat24_socket_url ="wss://livechatv2.chat2desk.com/widget_ws_new";
        window.chat24_static_files_domain = "https://storage.chat2desk.com/";
        window.lang = "ru";
        window.fetch("".concat(window.chat24_url, "/packs/manifest.json?nocache=").concat(new Date().getTime())).then(function (res) {
          return res.json();
        }).then(function (data) {
          var chat24 = document.createElement("script");
          chat24.type = "text/javascript";
          chat24.async = true;
          chat24.src = "".concat(window.chat24_url).concat(data["application.js"]);
          document.body.appendChild(chat24);
        });
      </script>
    EOD;



    ?>

<!DOCTYPE html>
<html translate="no" lang="en" prefix="og: https://ogp.me/ns#"
  style="--tg-viewport-height: 100vh; --tg-viewport-stable-height: 100vh">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Cache-Control" content="no-cache" />
  <link rel="icon" href="favicon.ico" />
  <meta name="viewport"
    content="width=device-width,initial-scale=1,minimum-scale=1,user-scalable=no,viewport-fit=cover,shrink-to-fit=no" />
  <meta name="theme-color" content="#000000" />

  <link rel="apple-touch-icon" href="./images/logo192.png" />

  <link crossorigin="use-credentials" rel="manifest" href="manifest.json" />
  <title data-translate="head.title">
    Sitio web oficial de Valor Casino: Las mejores tragamonedas en línea y registro rápido 🎰
  </title>

  <link rel="stylesheet" href="./css/styles.css?v=<?php echo time(); ?>" />
  
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  
  <style>
    /* Принудительные стили для языкового dropdown */
    .language-dropdown .language-dropdown__menu {
      max-height: none !important;
      height: auto !important;
      overflow: visible !important;
    }
    
    /* ПРИНУДИТЕЛЬНАЯ ФИКСАЦИЯ ВЫСОТЫ БАННЕРА */
  ._slider_5jhgj_1,
  .swiper-slide ._slider_5jhgj_1,
  article ._slider_5jhgj_1,
  .swiper-wrapper .swiper-slide ._slider_5jhgj_1,
  [class*="_slider_5jhgj_"] {
    height: 350px !important;
    max-height: 350px !important;
    min-height: 350px !important;
  }

  .swiper-slide ._slider_5jhgj_1 ._img_5jhgj_11,
  ._slider_5jhgj_1 ._img_5jhgj_11 {
    height: 350px !important;
  }
    
  .swiper-slide ._slider_5jhgj_1 ._img_5jhgj_11 img,
  ._slider_5jhgj_1 ._img_5jhgj_11 img {
    height: 350px !important;
    object-fit: cover !important;
    object-position: center !important;
    width: 100% !important;
  }

  @media screen and (max-width: 768px) {
    ._slider_5jhgj_1,
    .swiper-slide ._slider_5jhgj_1,
    article ._slider_5jhgj_1,
    .swiper-wrapper .swiper-slide ._slider_5jhgj_1,
    [class*="_slider_5jhgj_"] {
      height: 220px !important;
      max-height: 220px !important;
      min-height: 220px !important;
    }
        
    .swiper-slide ._slider_5jhgj_1 ._img_5jhgj_11,
    ._slider_5jhgj_1 ._img_5jhgj_11 {
      height: 220px !important;
    }
        
    .swiper-slide ._slider_5jhgj_1 ._img_5jhgj_11 img,
    ._slider_5jhgj_1 ._img_5jhgj_11 img {
      height: 220px !important;
    }
  }
    
    .language-dropdown .language-dropdown__menu-list {
      max-height: 400px !important;
      overflow-y: auto !important;
      height: auto !important;
      min-height: 150px !important;
    }
    
    .language-dropdown .language-dropdown__option {
      min-height: 35px !important;
      height: auto !important;
      padding: 8px 12px !important;
    }
    
    .language-dropdown--open .language-dropdown__menu {
      display: block !important;
      max-height: none !important;
    }
    
    /* Фиксация баннера - убираем инлайн стили свайпера */
    .swiper-slide[style*="width"] {
      width: 100% !important;
    }
    
    ._slider_5jhgj_1, ._banner_o6otq_1 {
      /* height: 600px !important;
      max-height: 600px !important;
      min-height: 600px !important; */
      /* border-radius: 12px !important; */
      overflow: hidden !important;
      width: 100% !important;
      position: relative !important;
    }
    
    .swiper-slide, ._banner_o6otq_1 .swiper-slide {
      height: 600px !important;
      max-height: 600px !important;
      min-height: 600px !important;
      width: 100% !important;
    }
    
    ._img_5jhgj_11, ._banner_o6otq_1 img {
      height: 100% !important;
      object-fit: cover !important;
      object-position: center center !important;
      max-height: none !important;
      display: block !important;
      min-height: 600px !important;
    }
    
    /* Принудительная фиксация изображений */
    ._img_5jhgj_11 img {
      height: 100% !important;
      object-fit: cover !important;
      object-position: center center !important;
      max-height: none !important;
      display: block !important;
      min-height: 600px !important;
    }
    
    @media (max-width: 768px) {
      ._slider_5jhgj_1, ._banner_o6otq_1 {
        height: 350px !important;
        max-height: 350px !important;
        min-height: 350px !important;
        border-radius: 12px !important;
        overflow: hidden !important;
      }
      .swiper-slide, ._banner_o6otq_1 .swiper-slide {
        height: 350px !important;
        max-height: 350px !important;
        min-height: 350px !important;
        width: 100% !important;
      }
      ._img_5jhgj_11, ._banner_o6otq_1 img {
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        min-height: 350px !important;
      }
      ._img_5jhgj_11 img {
        height: 100% !important;
        object-fit: cover !important;
        object-position: center !important;
        min-height: 350px !important;
      }
    }
    
    /* Owl Carousel стили */
    .owl-carousel {
      height: 400px !important;
      max-height: 400px !important;
      min-height: 400px !important;
      border-radius: 12px;
      overflow: hidden;
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      width: 100% !important;
      position: relative !important;
    }
    
    .owl-carousel .owl-item {
      height: 400px !important;
      max-height: 400px !important;
      min-height: 400px !important;
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      display: flex !important;
      align-items: stretch !important;
    }
    
    .owl-carousel .owl-item img {
      height: 400px !important;
      max-height: 400px !important;
      min-height: 400px !important;
      width: 100% !important;
      object-fit: cover !important;
      object-position: center center !important;
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
      transform: none !important;
      border-radius: 13px;
    }
    
    /* Принудительная фиксация для Owl Carousel */
    .owl-carousel .owl-stage-outer,
    .owl-carousel .owl-stage {
      height: 400px !important;
      max-height: 400px !important;
      min-height: 400px !important;
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    
    
    /* Принудительное отображение для всех состояний */
    #bannerCarousel,
    #bannerCarousel.owl-loading,
    #bannerCarousel.owl-loaded {
      display: block !important;
      visibility: visible !important;
      opacity: 1 !important;
    }
    
    /* Скрытие стрелок навигации */
    .owl-nav {
      display: block !important;
      position: absolute;
      width: 100%;
      bottom: 20px;
      left: 0;
      top: auto;
      transform: none;
      pointer-events: none;
      z-index: 10;
    }
    .owl-prev,
    .owl-next {
      display: block !important;
      position: absolute;
      bottom: 0;
      top: auto;
      transform: none;
      pointer-events: auto;
      background: rgba(255,255,255,0.8);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      border: none;
      z-index: 11;
    }
    .owl-prev {
      left: 10px;
    }
    .owl-next {
      right: 10px;
    }
    
    /* Скрытие точек навигации */
    .owl-dots {
      display: none !important;
    }
    
    .owl-dot {
      display: none !important;
    }
    
    .owl-dot.active {
      display: none !important;
    }
    .owl-carousel .item {
      width: 100%;
    }
    
    /* Стили для больших экранов */
  /* Удалён медиа-запрос для больших экранов, чтобы не было конфликтов */
    
    @media (max-width: 768px) {
      /* Принудительные стили для баннера на мобильных */
      ._banner_o6otq_1 {
        height: 300px !important;
        max-height: 300px !important;
        min-height: 300px !important;
        overflow: hidden !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        position: relative !important;
      }
      .owl-carousel,
      .owl-carousel.owl-loaded {
        height: 300px !important;
        max-height: 300px !important;
        min-height: 300px !important;
        overflow: hidden !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        border-radius: 12px !important;
        width: 100% !important;
        position: relative !important;
      }
      .owl-carousel .owl-item,
      .owl-carousel.owl-loaded .owl-item {
        height: 300px !important;
        max-height: 300px !important;
        min-height: 300px !important;
        overflow: hidden !important;
        display: flex !important;
        align-items: stretch !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      .owl-carousel .owl-item img,
      .owl-carousel.owl-loaded .owl-item img { 
        max-height: none !important;
        object-fit: cover !important;
        object-position: center center !important;
        overflow: hidden !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      .owl-carousel .owl-item img,
      .owl-carousel.owl-loaded .owl-item img { 
        height: 300px !important;
        max-height: 300px !important;
        min-height: 300px !important;
        width: 100% !important;
        object-fit: cover !important;
        object-position: center center !important;
        overflow: hidden !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      .owl-carousel .owl-stage-outer,
      .owl-carousel .owl-stage,
      .owl-carousel.owl-loaded .owl-stage-outer,
      .owl-carousel.owl-loaded .owl-stage {
        height: 300px !important;
        max-height: 300px !important;
        min-height: 300px !important;
        overflow: hidden !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
      
      /* Принудительное отображение всех элементов слайдера */
      #bannerCarousel,
      #bannerCarousel * {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
      }
    }
  </style>

   <meta data-translate="footer.description" name="description"
    content="Descubre una amplia gama de tragamonedas online en el sitio web oficial de Valor Casino. Disfruta de opciones de juego gratis y con dinero real tras registrarte. ¡Únete hoy mismo a Valor Casino para disfrutar de la mejor experiencia de juego! ✨"
    data-react-helmet="true" />
  <meta data-translate="footer.title" property="og:title"
    content="Sitio web oficial de Valor Casino: Las mejores tragamonedas en línea y registro rápido 🎰"
    data-react-helmet="true" />
  <meta data-translate="footer.valor" property="og:site_name" content="Valor" data-react-helmet="true" />
  <meta data-translate="footer.description" property="og:description"
    content="Descubre una amplia gama de tragamonedas online en el sitio web oficial de Valor Casino. Disfruta de opciones de juego gratis y con dinero real tras registrarte. ¡Únete hoy mismo a Valor Casino para disfrutar de la mejor experiencia de juego! ✨"
    data-react-helmet="true" />

  <!-- Подключение Notiflix -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.css">
  <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-3.2.6.min.js"></script>
</head>

<body class="<?php echo $body_class; ?>">
  <div id="root">
    <header class="_header_1v35z_1">
      <div class="_referral_j8fbr_1 _referral_desktop_j8fbr_29" bis_skin_checked="1">
        <svg width="52" height="48" viewBox="0 0 52 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M29.8211 12.7716C29.8144 12.723 29.8114 12.6743 29.8002 12.6259L30.2749 12.7716H33.6265V13.8006L35.9854 14.5248C35.4516 15.0347 35.3251 15.8727 35.7241 16.5316C36.0892 17.1344 36.7823 17.3968 37.4239 17.2417L35.4838 23.7952C35.2814 22.6977 34.6165 21.6401 33.6265 20.8089V27.7406C33.9623 27.5387 34.2663 27.3016 34.5265 27.029L33.6265 30.0692V35.6149L40 14.0855L26.6929 10L25.8724 12.7716H29.8211Z"
            fill="#FDA700"></path>
          <path
            d="M22.1789 12.7716C22.1856 12.723 22.1886 12.6743 22.1998 12.6259L21.7251 12.7716H18.3735V13.8006L16.0146 14.5248C16.5483 15.0347 16.6749 15.8727 16.2758 16.5316C15.9108 17.1344 15.2177 17.3968 14.5761 17.2417L16.5162 23.7952C16.7186 22.6977 17.3834 21.6401 18.3735 20.8089V27.7406C18.0376 27.5387 17.7337 27.3016 17.4735 27.029L18.3735 30.0692V35.6149L12 14.0855L25.307 10L26.1275 12.7716H22.1789Z"
            fill="#FDA700"></path>
          <path d="M19.0012 38V13.3746H32.8998V38H19.0012ZM20.573 14.9753V36.3993H31.328V14.9753H20.573Z"
            fill="#FDA700"></path>
          <path
            d="M29.4993 16.9735C30.1489 17.56 31.1423 17.4991 31.7182 16.8376C32.2941 16.1761 32.2344 15.1644 31.5848 14.5779C30.9352 13.9914 29.9417 14.0523 29.3658 14.7138C28.7899 15.3753 28.8497 16.387 29.4993 16.9735Z"
            fill="#FDA700"></path>
          <path
            d="M29.4993 34.4011C30.1489 33.8146 31.1423 33.8754 31.7182 34.537C32.2941 35.1985 32.2344 36.2102 31.5848 36.7967C30.9352 37.3832 29.9417 37.3223 29.3658 36.6608C28.7899 35.9993 28.8497 34.9876 29.4993 34.4011Z"
            fill="#FDA700"></path>
          <path
            d="M22.4018 16.9735C21.7522 17.56 20.7587 17.4991 20.1828 16.8376C19.6069 16.1761 19.6667 15.1644 20.3163 14.5779C20.9659 13.9914 21.9593 14.0523 22.5352 14.7138C23.1111 15.3753 23.0513 16.387 22.4018 16.9735Z"
            fill="#FDA700"></path>
          <path
            d="M22.4018 34.4011C21.7522 33.8146 20.7587 33.8754 20.1828 34.537C19.6069 35.1985 19.6667 36.2102 20.3163 36.7967C20.9659 37.3832 21.9593 37.3223 22.5352 36.6608C23.1111 35.9993 23.0513 34.9876 22.4018 34.4011Z"
            fill="#FDA700"></path>
          <path
            d="M31.7445 25.6873C31.7445 23.1732 29.1505 21.1351 25.9505 21.1351C22.7506 21.1351 20.1565 23.1732 20.1565 25.6873C20.1565 28.2014 22.7506 30.2395 25.9505 30.2395C29.1505 30.2395 31.7445 28.2014 31.7445 25.6873ZM23.1499 26.1069H22.2166V25.1935H23.0859C23.1133 24.5687 23.2779 23.9631 23.4792 23.6089L24.5503 23.8885C24.3398 24.2802 24.1474 24.83 24.1474 25.4356C24.1474 25.9671 24.3489 26.3302 24.715 26.3302C25.0627 26.3302 25.2825 26.0323 25.5112 25.3427C25.8406 24.3454 26.2982 23.6647 27.1861 23.6647C27.9913 23.6647 28.6229 24.2426 28.815 25.24H29.6845V26.1534H28.879C28.8518 26.7778 28.7235 27.1971 28.5773 27.5049L27.5429 27.2347C27.6435 26.9921 27.8541 26.5634 27.8541 25.8921C27.8541 25.2865 27.5979 25.0908 27.3416 25.0908C27.0396 25.0908 26.8475 25.4173 26.5545 26.2092C26.1701 27.3186 25.6667 27.7657 24.843 27.7657C24.0284 27.7657 23.333 27.1784 23.1499 26.1069Z"
            fill="#FDA700"></path>
        </svg><button type="button" data-translate="header.recommend"
          class="_button_1qy1r_1 _button_color_white_1qy1r_45 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_j8fbr_25">
          Recomendar
        </button>
      </div>
      <div class="_container_1v35z_10" bis_skin_checked="1">
        <div class="_navigation_1v35z_22" bis_skin_checked="1" style="cursor: pointer;">
          <a aria-label="Valor" href="index.php" class="_logo_q85o9_1">
            <div class="_logo-icon_q85o9_29" bis_skin_checked="1">
              <svg width="86" height="38" viewBox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z"
                  fill="#302FA0"></path>
                <path
                  d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z"
                  fill="#FDA700"></path>
              </svg>
            </div>
            <div class="_casino-icon_q85o9_142" bis_skin_checked="1">
              <div class="_defaultIcon_q85o9_9" bis_skin_checked="1">
                <p data-translate="header.casino" class="_text_q85o9_20">CASINO</p>
              </div>
            </div>
          </a>
          <menu class="_menu_ohy0w_1">
            <div class="_nav_ohy0w_11" bis_skin_checked="1">
              <nav class="_navigation_1992l_1">
                <ul class="_list_1992l_6">
                  <li class="_item_1992l_14" aria-hidden="true">
                    <a href="./index.php" type="link" class="_link_p19s5_1 _link_active_p19s5_42"
                      aria-current="page"><svg width="22" height="20" viewBox="0 0 22 20" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M4.78021 8L9.5929 20L0 8H4.78021ZM12.4072 20L22 8H17.2198L12.4072 20ZM11 8H6.21985L11 20L15.7802 8H11ZM7.53589 0H4.97864L0 7H4.78015L7.53589 0ZM11 7H15.7802L13.1313 0H8.86871L6.21985 7H11ZM22 7L17.0214 0H14.4641L17.2198 7H22Z"
                          fill="#0F9658"></path>
                      </svg>
                      <p data-translate="header.home" class="_label_p19s5_35">Inicio</p>
                    </a>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true">
                    <div class="_link_p19s5_1" bis_skin_checked="1">
                      <div class="_aviator_kuupf_1 games__href" aria-hidden="true" bis_skin_checked="1">
                        <div class="_loader_r2b2v_1 _loader_kuupf_21" bis_skin_checked="1">
                          <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <svg width="28" height="16" viewBox="0 0 28 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                          class="_logo_kuupf_8">
                          <path
                            d="M3.24974 14.8199H3.25674L3.24974 14.8244V14.8199ZM16.2499 8.22863L19.8042 8.92072L20.4999 7.60897L19.8032 6.79701L16.2499 8.22863ZM24.3195 5.61953C24.3077 5.62165 24.294 5.62378 24.2823 5.62826C24.2702 5.63275 24.2592 5.63949 24.25 5.64808L24.375 5.61717C24.354 5.61717 24.338 5.61953 24.3195 5.61953ZM27.8618 12.1573C27.7988 12.2483 27.6545 12.2455 27.59 12.1559C27.28 11.7268 26.4748 10.6081 26.3605 10.4493C26.1335 10.1284 25.8698 9.81221 25.7573 9.44009L25 7.1243C25.0446 7.1225 25.0885 7.11307 25.1295 7.09645C25.164 7.08111 25.1938 7.06247 25.222 7.041C25.3728 6.90979 25.5303 6.72929 25.6645 6.56104L27.0698 8.90939C27.378 9.56987 27.9115 11.3703 27.9843 11.6421V11.6419C28.0293 11.8026 27.9743 11.9968 27.8618 12.1573ZM27.7375 11.5109C27.5635 11.2117 26.223 9.5852 26.165 9.51748L26.1533 9.50333L26.1368 9.51276C26.1368 9.51276 25.991 9.60386 26.1153 9.78579C26.5263 10.3892 27.663 11.9475 27.6745 11.9631L27.6928 11.9876L27.7093 11.9631C27.716 11.9536 27.87 11.7346 27.7375 11.5109ZM24.6735 7.18589C25.2775 8.42849 25.0508 8.76145 23.9387 9.40682C22.8117 10.0633 21.0045 10.7632 19.6972 11.27C17.3817 12.1705 7.04329 15.0743 4.74451 15.8051C3.932 16.0649 3.932 16.0635 3.80799 15.8098C3.76149 15.7166 3.71674 15.6295 3.67874 15.5469C3.03999 15.8579 3.12948 15.4552 2.75373 15.222L3.39398 14.9636L3.39899 14.9606C6.40453 15.0665 18.6562 10.4769 18.6562 10.4769C18.6562 10.4769 18.6627 10.4068 18.4922 10.4068C18.1214 10.4099 17.3537 10.3773 17.2694 10.4163C15.7599 11.1332 10.8643 12.9015 10.7668 12.7585L10.0786 12.2453C10.0544 12.2278 10.0223 12.2237 9.99407 12.2344C9.58357 12.4008 6.65278 13.5424 4.27301 14.4598C4.27301 14.4598 4.08575 14.1441 4.31575 14.0245C4.78101 13.785 5.17325 13.6075 5.17325 13.6075C5.17325 13.6075 5.16826 13.519 4.86051 13.5438C3.937 13.6217 2.98199 12.8954 2.70898 12.7009C2.38472 12.4676 2.15448 12.5343 1.96272 12.6264C1.95556 12.6298 1.94896 12.6341 1.94313 12.6393C1.90953 12.6691 1.90788 12.719 1.93947 12.7507L3.81774 14.6278C3.68306 14.6832 3.54353 14.7274 3.40074 14.76L1.14922 12.7455C1.13446 12.7316 1.09471 12.7269 1.06322 12.7408L0.0126997 13.1986C0.0126997 13.1986 -0.0545466 12.6448 0.120443 12.5268C0.239952 12.4458 1.01772 12.1254 1.55547 11.805C1.70947 11.7148 1.57197 11.5657 1.57197 11.5657L0.461451 12.0943C0.461451 12.0943 0.334206 11.8127 0.542705 11.6962C1.30896 11.2731 1.87323 10.9357 2.39798 11.1426C3.02023 11.3882 4.74126 13.0011 5.65301 12.9794C5.99902 12.9711 10.0206 11.0444 10.4553 10.7879C10.7368 10.6247 10.5778 10.4772 10.5778 10.4772L8.0278 11.6122C8.0278 11.6122 7.88654 11.3526 8.02555 11.2747C8.20106 11.1766 8.42931 11.0631 8.65106 10.9529C8.65872 10.9491 8.66573 10.9442 8.67179 10.9384C8.70374 10.9077 8.7033 10.8584 8.67081 10.8283C8.5798 10.7429 8.53581 10.7117 8.40181 10.5765C8.377 10.552 8.33842 10.5451 8.3058 10.5593C7.36754 10.9807 5.83502 11.7026 5.83502 11.7026C5.83502 11.7026 5.85377 11.5636 5.89128 11.4116C5.98053 11.0487 5.97377 10.791 6.24878 10.6541C6.93229 10.3169 7.6388 9.98069 8.3688 9.60432C8.48106 9.54532 8.36055 9.19539 8.36055 9.19539L7.4818 9.5753C7.45029 9.58945 7.4123 9.58309 7.3873 9.55831L6.27028 8.46436L6.26853 8.45351C6.61279 8.03182 7.4088 7.62926 8.2463 7.70855C10.2188 7.89048 18.0849 8.98065 18.3777 9.01487C18.4539 9.02432 18.6227 9.06159 18.6309 9.22654C18.6327 9.25911 18.6209 9.31054 18.5997 9.35562C18.4969 9.54839 18.4557 9.5812 18.4557 9.5812L19.5562 9.96677C19.574 9.97428 19.5942 9.97489 19.6124 9.96842C19.8357 9.89386 21.3897 9.34948 23.5445 8.10049C23.5761 8.08202 23.6167 8.08448 23.6455 8.10663C23.773 8.21259 23.978 8.39145 24.079 8.38956C24.2082 8.38343 24.276 8.26213 24.276 8.26213C23.8922 7.75056 23.1655 6.2045 22.9172 5.52018C22.8842 5.42863 22.7915 5.36798 22.6857 5.3602C22.555 5.35241 22.3762 5.34462 22.2487 5.35548C21.7025 5.40361 21.269 5.47535 19.4717 6.1455C18.1577 6.6323 15.9832 7.71468 15.3839 8.01814C15.3541 8.03207 15.3409 8.0585 15.3409 8.08493C15.3409 8.11592 15.3595 8.14423 15.3889 8.15807C15.2929 8.14557 13.1944 7.97426 13.0026 7.95538C12.9971 7.95487 12.9916 7.95384 12.9863 7.95229C12.9425 7.93958 12.9179 7.89574 12.9314 7.85439C13.0041 7.63351 12.9561 7.29938 13.3701 7.01953C14.6921 6.11128 15.9104 5.57304 16.5029 5.50932C16.8852 5.46898 17.1267 5.67427 17.9244 5.93077C18.2254 6.02893 18.7717 5.87815 19.0612 5.80335C21.0305 5.31205 21.466 5.12683 22.583 4.94631C23.0017 4.87788 23.49 4.85004 23.8175 5.37105C23.93 5.55134 24.2445 6.3043 24.673 7.18589H24.6735ZM16.5722 6.38807C16.5722 6.38807 15.9921 6.17712 15.7054 6.20921C14.7414 6.31753 13.7279 7.16394 13.7279 7.16394C13.7279 7.16394 14.6439 7.32464 14.7829 7.23118C15.2794 6.89541 16.5722 6.38807 16.5722 6.38807ZM17.8382 6.20921C17.4512 6.05677 16.8619 5.82341 16.7212 5.78305C16.4779 5.71463 16.2231 5.78778 16.0591 5.91047L17.0289 6.27622C17.0553 6.28601 17.0771 6.30449 17.0903 6.32826C17.1204 6.38267 17.0981 6.44981 17.0404 6.47821C16.8784 6.55774 16.6782 6.6651 16.5904 6.70216C16.2494 6.84703 16.5524 7.01953 16.5524 7.01953L17.8564 6.39114C17.9292 6.35716 17.9574 6.25593 17.8382 6.20921ZM18.1249 9.24022L8.4563 7.98676C8.34555 7.97426 8.33731 8.0972 8.39856 8.11277C8.42006 8.11418 17.1069 9.77068 17.3202 9.80465C17.7574 9.87191 18.0549 9.54203 18.1824 9.36457H18.1827C18.2192 9.31479 18.1859 9.24966 18.1249 9.24022ZM22.085 0.622188C22.0377 0.448524 22.0915 0.232374 22.2147 0.0601216C22.2248 0.0457336 22.2378 0.0333332 22.2529 0.0235828C22.3178 -0.0184289 22.4065 -0.00280232 22.451 0.0584653C22.8749 0.648947 23.2977 1.24012 23.7195 1.832C23.9522 2.16212 24.2205 2.48446 24.3772 2.98187H24.3775L25.2075 5.45364C24.963 5.44892 24.6933 5.44726 24.521 5.45835C24.4873 5.46051 24.4543 5.46802 24.4232 5.48055C24.3775 5.49966 24.3372 5.52797 24.305 5.56101L22.9995 3.40827C22.6857 2.73245 22.161 0.898742 22.085 0.622188ZM22.3262 0.769207C22.5065 1.07714 23.8695 2.74024 23.927 2.8115L23.9387 2.82566L23.9557 2.81623V2.81598C23.9575 2.81457 24.1057 2.72279 23.9795 2.53636C23.5625 1.92049 22.4037 0.323699 22.392 0.306474L22.3732 0.282868L22.3565 0.306474C22.3497 0.315907 22.1945 0.538654 22.3262 0.769207ZM25.846 5.68064H25.8463C25.995 5.70991 26.0338 5.80735 25.9713 5.93101C25.7663 6.32412 25.4413 6.72032 25.0963 7.00018C25.0847 7.00829 25.0722 7.01518 25.059 7.02071C24.9945 7.0443 24.905 7.03534 24.8745 6.98083C24.6474 6.59127 24.4411 6.1912 24.2565 5.7821C24.2362 5.73798 24.265 5.68064 24.3107 5.65114C24.3181 5.64462 24.3268 5.63956 24.3363 5.63628C24.3462 5.63344 24.3565 5.63203 24.365 5.63038C24.3802 5.62896 24.392 5.62896 24.409 5.62896C24.8135 5.60678 25.489 5.61126 25.846 5.68064ZM24.536 5.7689C24.5275 5.77031 24.5207 5.77338 24.514 5.77621C24.429 5.80878 24.4665 5.87059 24.4665 5.87059L24.673 6.28424L25.8055 5.85714C25.8055 5.85714 25.8003 5.81303 25.6583 5.78801C25.3908 5.74247 24.536 5.7689 24.536 5.7689ZM21.5095 5.61717C21.5095 5.61717 21.3565 6.21488 21.1157 6.90508L20.6697 6.37202L20.3082 6.52753L20.9657 7.31449C20.7607 7.84541 20.5132 8.37351 20.2499 8.68475L20.9089 8.40159C21.059 8.26992 21.1802 8.00375 21.2745 7.68473L21.6365 8.11819L22 7.96174L21.3862 7.22813C21.519 6.56506 21.557 5.8437 21.5095 5.61717Z"
                            fill="#E50539"></path>
                        </svg><svg width="55" height="14" viewBox="0 0 55 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg" class="_name_kuupf_33">
                          <path
                            d="M16.1372 4.27695L14.0604 12.7197C14.5819 12.7197 15.0783 12.5311 15.5486 12.1566C16.019 11.7831 16.3189 11.333 16.453 10.81L18.0567 4.27788H20.5555L20.5378 4.34885H24.8136L22.9509 11.6234C22.9481 11.6383 22.9397 11.6617 22.9267 11.7009C22.9155 11.7336 22.9081 11.7616 22.9025 11.785L22.8997 11.7962C22.8103 12.335 23.0189 12.6441 23.5237 12.7235L23.5423 12.7263L23.2117 14H22.8242C21.9283 14 21.2549 13.8058 20.8023 13.421C20.3487 13.0335 20.1764 12.5115 20.2873 11.8541C20.2966 11.7868 20.3124 11.7205 20.3338 11.6561L20.3441 11.6234L21.8957 5.6226H20.2267L19.314 9.35886C19.0179 10.5924 18.3324 11.6673 17.2558 12.5834L17.2176 12.6151C16.1186 13.5378 14.961 14 13.7437 14H11.2477L13.3051 5.55817L13.5705 4.27788H16.1372L16.1372 4.27695ZM34.1306 4.27695L32.2978 11.8578C32.294 11.883 32.2866 11.9082 32.2773 11.9325C32.267 11.9568 32.2587 11.9829 32.254 12.0091C32.1842 12.4554 32.3872 12.6908 32.864 12.7169L32.5511 14H31.6794C30.816 14 30.2293 13.7385 29.9154 13.2146C29.2886 13.7376 28.6125 13.9991 27.8842 13.9991H27.4967C26.6557 13.9991 25.9768 13.7656 25.459 13.3006C24.9393 12.8355 24.7391 12.2453 24.8536 11.5309C24.8629 11.4469 24.8816 11.3629 24.9095 11.2825L25.9423 6.9944C26.1267 6.21932 26.6017 5.57124 27.3691 5.05389C28.1356 4.53749 28.9636 4.27788 29.8539 4.27788H34.1297L34.1306 4.27695ZM44.2561 4.27695C45.1008 4.27695 45.7788 4.51134 46.2957 4.97732C46.8033 5.4349 47.0054 6.01574 46.903 6.71798L46.8983 6.7488L46.8583 6.99533L45.8021 11.2816C45.6187 12.0352 45.1446 12.6768 44.3772 13.2062C43.6219 13.7255 42.8014 13.9907 41.9101 14H41.4938C40.6388 14 39.9478 13.7703 39.4262 13.3118C38.9019 12.8505 38.6998 12.2584 38.8162 11.5309C38.8255 11.459 38.8413 11.3871 38.8618 11.3171L38.8721 11.2825L39.931 6.99533C40.1145 6.23146 40.5857 5.58618 41.3466 5.0623C42.0936 4.54869 42.9178 4.28722 43.8203 4.27788H44.2561L44.2561 4.27695ZM9.38041 0C10.3583 0 11.1332 0.240928 11.7097 0.720918C12.2815 1.20464 12.505 1.84338 12.3756 2.63901C12.3709 2.66702 12.3625 2.71651 12.343 2.79402C12.3225 2.86873 12.315 2.91729 12.3104 2.9453L10.0258 12.1398C9.96436 12.504 10.1152 12.7001 10.4766 12.7253L10.1553 14H9.15689C8.545 14 8.08585 13.8487 7.78596 13.5452C7.48142 13.2417 7.37617 12.8159 7.46558 12.2705C7.47583 12.1865 7.49539 12.1043 7.52239 12.0249L8.32986 8.79669H6.05368L4.7554 14H2.25569L3.55025 8.79669H0C0 7.8862 0.721785 7.14567 1.62239 7.14567C2.22962 7.14567 3.00169 7.14194 3.83803 7.14101H3.96376L5.01058 2.9453C5.23038 2.05723 5.74634 1.34378 6.56126 0.80683C7.37617 0.269877 8.31403 0 9.38041 0ZM39.4346 1.94424L38.8721 4.14154H39.9478L39.6311 5.39101H38.5759L37.0951 11.2984C37.0942 11.3134 37.0858 11.3358 37.0755 11.3731C37.0625 11.4105 37.0541 11.4422 37.0504 11.4674C36.9628 12.0025 37.1752 12.2948 37.6855 12.3434L37.368 13.6106H36.4962C35.7931 13.6106 35.2678 13.4266 34.9185 13.0615C34.5721 12.6936 34.4501 12.1949 34.5535 11.5618C34.5609 11.5169 34.5702 11.473 34.5823 11.431C34.5991 11.3675 34.6103 11.3236 34.614 11.2984L36.0948 5.39101H34.8748L35.1914 4.14154H36.4087L36.9526 1.94424C36.9526 1.94424 39.4346 1.94424 39.4346 1.94424ZM51.6425 4.02481L51.4488 4.83351C52.2032 4.29469 53.101 4.02481 54.1376 4.02481H55L54.3136 6.63861H52.9119L53.2165 5.39848C52.5906 5.61139 52.0318 5.99613 51.5345 6.55456C51.0381 7.11019 50.7056 7.70784 50.5352 8.34752L49.1987 13.5704H46.6264L49.0832 4.02481H51.6425V4.02481ZM31.3348 5.56003H29.5569C29.0381 5.56003 28.6982 5.88033 28.5361 6.51721L27.3468 11.4348C27.3431 11.46 27.3347 11.4992 27.3198 11.5506L27.2946 11.6439C27.1829 12.3611 27.4837 12.7178 28.1999 12.7178C28.5817 12.7178 28.945 12.5778 29.2849 12.2976C29.6248 12.0156 29.8456 11.6766 29.9452 11.2825L31.3348 5.56003ZM43.7318 5.57871C43.4338 5.57871 43.1637 5.71038 42.9243 5.97185C42.6915 6.22585 42.521 6.55456 42.4158 6.95704L42.4065 6.9944L41.369 11.2825C41.3625 11.318 41.3541 11.3526 41.3438 11.3871C41.3299 11.432 41.3205 11.4665 41.3159 11.4927C41.26 11.8503 41.2963 12.1426 41.4277 12.3714C41.559 12.603 41.749 12.7169 41.9976 12.7169C42.2705 12.7169 42.5322 12.5927 42.7818 12.3434C43.0258 12.1015 43.2037 11.7597 43.3155 11.319L43.3248 11.2816L44.3623 6.99533L44.3958 6.78522C44.4582 6.40328 44.4228 6.10726 44.2952 5.89528C44.1676 5.68423 43.9804 5.57964 43.7318 5.57964L43.7318 5.57871ZM9.06003 1.27468C8.72289 1.27468 8.41461 1.39795 8.14266 1.64354C7.87164 1.89007 7.69283 2.20478 7.60715 2.58671L6.45136 7.14007L6.4467 7.16809H8.71916L8.72754 7.14007L9.88333 2.58391L9.9122 2.41209C9.96435 2.08431 9.9122 1.80976 9.76225 1.59685C9.60765 1.37927 9.37668 1.27281 9.06003 1.27281V1.27468ZM24.4736 0C24.835 0 25.1256 0.127935 25.3435 0.381003C25.5596 0.634071 25.6378 0.9385 25.5782 1.29242C25.5195 1.64728 25.336 1.95451 25.0362 2.21411C24.7335 2.47372 24.4001 2.60352 24.0359 2.60352C23.6736 2.60352 23.384 2.47559 23.1688 2.22252C22.9509 1.96852 22.8717 1.66035 22.936 1.29242C22.9947 0.938501 23.1753 0.634071 23.4762 0.381003C23.7789 0.127935 24.1095 9.33831e-08 24.4727 9.33831e-08L24.4736 0Z"
                            fill="#E50539"></path>
                        </svg><span class="aviator__text"></span>
                      </div>
                    </div>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true">
                    <div class="_link_p19s5_1" bis_skin_checked="1">
                      <div class="_aviator_kuupf_1 games__href" aria-hidden="true" bis_skin_checked="1">
                        <div class="_loader_r2b2v_1 _loader_kuupf_21" bis_skin_checked="1">
                          <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <svg width="45" height="28" viewBox="0 0 85 68" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="_logo_lfwq3_10"><rect y="0.5" width="85" height="67" fill="url(#pattern0_1533_11662)"></rect><defs><pattern id="pattern0_1533_11662" patternContentUnits="objectBoundingBox" width="1" height="1"><use xlink:href="#image0_1533_11662" transform="scale(0.0117647 0.0149254)"></use></pattern><image id="image0_1533_11662" width="85" height="67" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFUAAABDCAYAAADtekncAAAACXBIWXMAAAWJAAAFiQFtaJ36AABHCWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzIgNzkuMTU5Mjg0LCAyMDE2LzA0LzE5LTEzOjEzOjQwICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDozNjQyZGRkYS03Mzc3LTExZjAtYWZiMC05ZmM4YTMwZWQ2ZGM8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6MjJiOTg5MWItMzlkOS04NTQxLWE2MTktN2JmMTUwYjBkNTk1PC94bXBNTTpJbnN0YW5jZUlEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPjI3QkZBRDUzQzg0ODZDQURENjVBQjYzNDA5RDg1RkJDPC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiNGQxY2Q3MC1lNDk1LTkyNDYtOGZmOC1mZGE5ZTljMDIzYWY8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTA6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpjYTcwNjdlNC05NzUyLTdkNGEtYjA2Yy04YzNjZTk1ZmE5MmU8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTE6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5kZXJpdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmNvbnZlcnRlZCBmcm9tIGltYWdlL2pwZWcgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpkMjlmZGNmOC0xZmZkLTk4NDktOWZhYS0wMjMxZDYyNTEzMDI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTE6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gaW1hZ2UvcG5nIHRvIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3A8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOmYyMWI0NGIxLTJjMmQtNzk0Ni04YzgwLWE2MDlkZDU1NDBmYjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAyNS0wOC0wN1QxMjoxMzo1OSswMjowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUuNSAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5kZXJpdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmNvbnZlcnRlZCBmcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDoyMmI5ODkxYi0zOWQ5LTg1NDEtYTYxOS03YmYxNTBiMGQ1OTU8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTM6NTkrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcE1NOkhpc3Rvcnk+CiAgICAgICAgIDx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdFJlZjppbnN0YW5jZUlEPnhtcC5paWQ6ZjIxYjQ0YjEtMmMyZC03OTQ2LThjODAtYTYwOWRkNTU0MGZiPC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOmYyMWI0NGIxLTJjMmQtNzk0Ni04YzgwLWE2MDlkZDU1NDBmYjwvc3RSZWY6ZG9jdW1lbnRJRD4KICAgICAgICAgICAgPHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4yN0JGQUQ1M0M4NDg2Q0FERDY1QUI2MzQwOUQ4NUZCQzwvc3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9tPgogICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3BuZzwvZGM6Zm9ybWF0PgogICAgICAgICA8cGhvdG9zaG9wOkxlZ2FjeUlQVENEaWdlc3Q+Q0RDRkZBN0RBOEM3QkUwOTA1NzA3NkFFQUYwNUMzNEU8L3Bob3Rvc2hvcDpMZWdhY3lJUFRDRGlnZXN0PgogICAgICAgICA8cGhvdG9zaG9wOkNvbG9yTW9kZT4zPC9waG90b3Nob3A6Q29sb3JNb2RlPgogICAgICAgICA8cGhvdG9zaG9wOklDQ1Byb2ZpbGUvPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAyNS0wOC0wN1QxMjowOTowMSswMjowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDI1LTA4LTA3VDEyOjEzOjU5KzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAyNS0wOC0wN1QxMjoxMzo1OSswMjowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNS41IChXaW5kb3dzKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8dGlmZjpJbWFnZVdpZHRoPjgxODwvdGlmZjpJbWFnZVdpZHRoPgogICAgICAgICA8dGlmZjpJbWFnZUxlbmd0aD45NDM8L3RpZmY6SW1hZ2VMZW5ndGg+CiAgICAgICAgIDx0aWZmOkJpdHNQZXJTYW1wbGU+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjg8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT44PC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+ODwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwvdGlmZjpCaXRzUGVyU2FtcGxlPgogICAgICAgICA8dGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPjI8L3RpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6U2FtcGxlc1BlclBpeGVsPjM8L3RpZmY6U2FtcGxlc1BlclBpeGVsPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj4zNjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjM2MDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPGV4aWY6RXhpZlZlcnNpb24+MDIzMTwvZXhpZjpFeGlmVmVyc2lvbj4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT42NTUzNTwvZXhpZjpDb2xvclNwYWNlPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+ODU8L2V4aWY6UGl4ZWxYRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+Njc8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0idyI/PkzGJCAAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAAKYFJREFUeNrsnHeYXVW99z9r7XLqzJleM6RCCgSSUASkI00EaRqvlytggyuggAgiCHjvC4ooqCCKaFCvICAKSFOqSajpISG9TDKZXs7MnLrLWuv945xUguCV9977vr7refaTPXuvvffa3/0r3185EcYYALj3XnjgAbj9LnjzUVg/CrMVqDg83ghVATQ9CY0XQVbDgsdg0jkwtgEcB159Fb71LZ546inOOvts/qeNAw88kOXLl7/3xBuuh6ZmWNoDJ34EOn8AhwaQ6xWIHoGJCBriYAxkRsEkoLpO06XgxQbDxfdj8//H7sOPQLoapi2F6lugfqOAnEUkZlFwbCxL0u+VJ8cMJtR0bAmpF4pztmheu8f8w4AqpXx/E7UDqbvh8Iwg02dRqLQJK22k7eJYLkrbhAgECpcAHLDsgLTvUZnwaXtY/cOA6nnee0967Q045OcwLivpqbCRVQ62jIIVo2hcpG6gXk6jUuyHT4o0fRTDZzHWVmwXRtEk8/ofBtSenh68FRuJRKLvPCmAjd0wfDGM6ZMMtjlEtAtWAs9EkaaF8dapnjKn9gyZA/LDJqazEnIWE5rFCTEZXkpo9RLRAXkn/IcBNZ1Os+ihB/jwflPB7HEyEJB5AA7dIBkcaxNVEUS0gkwmSqN9TFgT++KqzcHhI2mDP6xgxEdikK5DRVXtUa0x79OW8O4kkKCL/1iO6iePPcCHzzoHgj2dkw1H9QlUrYUbuohogtHBGBMnfqLfbb1u7ZJFFXbOUFvhkmyR9Ps+Q5mAg2bWMuxFWbRs8JgPHRm5k1EhUBbyHwnUB1avY0tNO7S1Q8vmndsBm6HWh5yUWHaEXMZlXNtpfbEZty5+flNFS1LTWCMQ6YBMV0A6A+Mn1tCwbwvLFvaTigVvghRYCoQQ/1CgAnzurqegPgptkZ1bdR6KAwLLlmhtEY9MKtYd9M1XH19IXaydgc6A9ctGCaUm6ysmTqlg0uFjWfWXQfZNFd6eMj3xMDkjQBgc+T+HUtkQGyPlhCrbbrCEsHPGZPqU6hpSausH+ZwXt+ZYNtTPDFGEMCwdbClCsw3dEjx8Dqw89a3X36hviI1Q25xi/aIMyboodtxi8pQUvomz6Ok+KsXIhmlHxK7HE51o7RGgSOn/fu9/RmXl2WfX1HziiIaGo1ri8baYlJgwJFcsMlgoZNYVCqvnZjIvPJJOP9ru+0u3005A/+eeaLjwshdYduvXIFcsHcq0Q+1WcFMQCMNIPmidEpKtiCCUxZQZDdQ1JMgUYOHyAutXtHP0geKl/Q6O30mBlQTGQ5gikXyIrP/vk9Qjksnjbxsz5jtHV1cfRl0duC5BGBIqhQpDLClpsqyKSfH4YR+tqTnsqoaGb/y0v//em3t6rgcGAQtQ74f37/kBluPxu6lj+cRZswEP0svgmblQ7UM06ppu9URdQ3x2wXUn5PsFGs3K17MMdOeJkV973smx3zeMiT5OzvShTB6t8lTKAKdf8ZeTPmDvLyVBELzntMsbG2/40fjx/45SdClFHNDZLDoMMVpjlEKHIVpr0krhBQEJ4KbGxouPjEZP+VRHxzlDSi3dE9hIJBL51o033njIEUfM0Fp3/+THP/7FY4899vre1nDJ+V/hEw/HIZMBJwctzYbRrMbWoYjIreHG/A1Bn39lIESzl/eKFX5hw8EHuwsbJ9TMIzSbGNE+wuQIlUfC+Ni9iqUnGawPvU9QhSht7zWCgOra2r865UsNDTf/aN99bxopFhkMAmwhyPf3Y7suxpjSplQJXK0RxmABw2FIZxBwZCw27vExY+Z9tKPjQ1mtV5Wpu3EcJ77yreVvTtpv8gGL2/tY/up8jjjixM8988zLl3re8D270HwDMJTL8/W7f8d3fvITGM7DyMuGxEJFID0Cx07U2nOnNuiVBLqRiOMTiQzhmRzDOgBdBOVRCH1a3RAdaJaeacieCdectyelMuDGQMcgHQEZBZ2EoQQMJyCwIayCoBJ8CbZTWuZOhs0hBx2ElUjsFdCp0ehhPx437qbRYpGhMMQuf6ggn8fLZAgKBZTnlSRWKTAGrTVKa0Kl0EqxJJ/nINdN3lJb+ygQ237vOff97IeT9pt8wCXf/C4XnnEEC+b/ltBfzXXXnf/jWTMnvwAcyx60/7YXn2ZJ+xrYrwWKh8BorwYVIFQWFWbxTQdCLKVoVpEOe/DUMFoNE3hZqsMCk7TPQKXinsmGwhmwf3K7092eSFAwEIHeIkSH4ICtEEbA3gwHxKFZQ42B3HxwElAF9Gioa4DaYkkLw5BUKsXHYjGeyOXeAeq/1NVdguMwkMmUAN0u/UKglQKldmqFEBitCZTCD0N8pVBag9YsLxQ4Lx6f+mg0etX8YvGWqlSq7fwLLvz8s/OW8Poz9/Lin2+joeUQoAiEfPyMWSfOe/nNE79y9X1Xgb5zh8QGAScffzybp06hItEK13xJM2aJId+r2dIdUtUoGdCCqgBaUgZja+KOQtZr+qqh92C4/iWYMhXGGkhG9wC1YhpM/BFEvy7YpxcOygsGAoFbEJwUg9CDoaghuRmq4oamCkP3zw0cDb2nQj4JjgVBwDeV4gn2GmLHEGKnDhqDgZ1qDyW1L+8rrQnLwIZao8qSGxhDFpgdj39hfrF4yxnnnnsOwIJX/8Qvv3cGDS3VkH4OYgkQLt+8/rtMnNjCgr98+44TTr/JyeaK391ujweBY1evYYmzDU5+Ae77qiH7vOL4Tyr63hacVAMdNabz1ZU8tLyDFe2Kot3A0IpBatwss3rfZlpzhHFvV1LTlkXsNxlh8lnYZmDJ12HiE4K0EKiohdISoS1wJEaXnKg0EBqNQRGLKKpHNX7e0H6QYePJ8MVroT6EmjrOGhl5B7BjXfe416dNe6k5kRCjxSLDYUigNVprtDHoMrgadqi9MgZVBnX7XKU1IgwRAwN8BGbdMmfOF79x0UWXzHvxFxxzSAEiTaAEhDmIRVixaDU//9UL/PDem1n66hJmHXXtacCfdmUGF9VWM2dgCC64GhYvh5XPw8O383DsL/z6jj6embsIRF0zxnMhs6X8UfSuJsWWEmFZWDePmQabH4dJ9wuKVZKca+MYF2NFkK6LkNHSJiIIy8WybSwsVCjJxQQjLtS1w8ythk0uhAfAT2+jGPjvAHVEqfaf9fW9XiVlY71tN9ZYlltUitAYtFKExSLe6CheOk1xeBhvdBQ/l8MPAkKtMZZF6HmM9veTzuepaGggedRRY8+97LIDWqqrGyLxKBWxDZAfBD0C8TpQURon1HHaGSdAro/mibMg33fU3NfWzaGUBTAAyzyfhgMO4NAYkO/hmaYqzr/6O/zw7tdZv6WL87985bVzX3jy0W/c8LXLO7q6ghXLlr1SBnYHqLqsScLMfxX6ZguqI5K8beNoB+VGkcRRuHg4mFwUPIMVCXBiCksW0aYAygftEcqQuoIiDDQLP2W45UXuzq/i8mK4G6hxKZvzWntAHJhwZm3tuXfU1Pxr2NHhZMIQUVeH09BAap99SNXVYVsWfjZLurOTztWr6R0cRAG1Z55J4+zZ1B1/PC3NzaUX8tJ4q69GdT9NkBnBIHAq26iYcS7UHQ3Z3pLfqEqB0TQ2ff7SvoHMPbst0LK4+8gjWbF+A/f2dO84fORRR5/w6vx5L44EirhjkfFDpo1tO6q3p+fVvUeH3uOQKgpG4xI7tNGRKJIURR0nag7lIE4gMr0eXZcn17WB9g2LyRbXk6gqgJ1FawsnLNIfDXDtkJm/1Nxwvul9cgq8/AgAVzc13fTp2trZjhC1Ba2DfqXS28Kwl40bm4pS2pWzZ9Ny+kepOPkUSKXYurWbp15fQHdXN1aoaDo2wfSWZg53Bd0WxE8+maqy7vmAGFmPWXMR0cYqCsXJqP5XMMKhsHYdha3fpv5jGlF/LGS7YWQYqidz7RWnfeqrNzyyO6hKcdn8+e8A6TOf+/ylANu2bcN1XSa2NtM2dtysdwd1y/NwWEpQwEY6LkZUYHSUSnEKU90bli/JpNK9aWIVFURjjUzad0Jnwu54jpVrHgN7C67lgCuI+IK8AzIacnGPDhZZBuAbLc233zJx0tV4HlmlkEJgS9laePvtAzj1VBLfvhV7xkwAXt/Sxbcv/Sq9nesY21ZL25gUqepKNgZx1nf2UN86jfM+fib1QKHoIR0XIQWFZZcTrRhFNH+CmH4Nma3BGBunPiC3fpDBF26n7rwpJdYS5IFBPvOpYw/+4W1/PntrZuSxvxb6CiEShx99zEeG/RDHcUgkk6zZvKUwkk4XEslkdS6bTe95rTCPThHUFSwK0SjaSuJQ5afVUeLQ+A/XbizGt81XjJ2q6OscwbIdhsN9aZhcz2Ez1q9gg38PQ7xJQmYxDINfQAmfSSL48s095q6HMuPSM2dsdqWkLwxLlNayyK5eTfLEExn75JMUgBHgs/98Mc8++DMuvuR0vn71BYybOG0XcqIIvU5+//hLbN7g8dEzL+XA6RNRgN81n+Irx5PY/wxsV6IHV2NHc1ClGVlRYNGiYcbHFWNnX4k15mTIdEHcASfC0O2r+P7v//LgrQvnXgLk9xb2Hvqhw0+b/8brz3R096KVIpVK8er8ee1SWsUZs2bWHz7joJN6u7uX7eawaHNhgy+p0RJpOUhdt6HLuqqyS8bVkOKE86L0dnpYsRYmTbcIvR7mP7aBJzckpp90duzWqBv+L3r0yySkxjga4WnyFaGnLAPEjBbEoy7xMhXKDw1hNzYy9pFH6APSBY+zPnI2a157luWL7+DAWccBm2DkdTAShAXCxnbjzJ49m8zoWp596lGqqj7PPm216PRCwqxCpTchnAAnmYOI5Ef3RbnrAcll/zqbo756CNL2gRw4MfB8cqMbcOodbvnUBZ/eorJbHliy+Bt7U+VTP/axMyJA6HlI2yadTnPQzIPH1jXUi5QlOPq448999LcPLt092VCVAtcSGCGxQBfN1P4RPdXrsahwHPp6AlbMT9O9aohFfx4ll3E48bImJtbbPPubXHWh1b6ZGnkonkkgRQwsF63sg5tjgFl//qZNN6zKZgccIaixLCLd3dRfdBHFWIwo8G9fvYk1rz3LcM8vOHDWdBhaAOnhEqAAOgSVh0Iaht+iorKKs05vIj2wFB9QQQYTgM71YokcJAU33RnnK7fGiKVa+cLF44nkIojbeuChleTNAEP968mNDDGcC8GVzBjTOvPdAvSTPnr6xzOhQkprR1VWayV6u7vRQOD7zjszOCkXooCWII0UQkaKhQBpJHE3Sn7AUNMco5DzKAwUWT+vwKpnc7TNtBnXaDH/qUIFk+xrwLSiSWLZDkrZp82MyrgtrWcyw7fvv2LFOfuvXHne5BUrLlri+x01M2YggXmvLubBn9zG4pe/RqpxIgy1g3RByHLQI0r7wi6HwyGku3GTcSaPHaEA+FQjDGhlkPWaec9HuePhJG0thiMPSmCsPsyP5sEfNzJsDTI80I6K5LH8Cip6JZ0d64IfvPTSw0BkT3A+fMyxZx4xa0ZLZ3cvRuyMyLUxJCsqGMgXWfTmG6vZPVhH0j0COQGWgRAponSkkqavc7NHPBVDYGgeG8eNRZAupCptClsVnasFBx6fxMoY3lriT2SqczZ57YKIMKLttumu/YNL2zywfGB+t+8/XiFlMWpZlhICF7j2yus588QWZh13NuTboaYFqupKYao2eyl5AkJDJk80kiaOIuceiDJgvDx4hsdfc7FtQV0KRkYsfFWL8CKExybIn2gRdQS2FKR/u4VwNMT55DkEY1ozvLNyxT9feNGXbCDwS+XtIFSoUGG0oSVVwUP/8R8rOrdte7nMV3cBtc+FaN5gMCBCHNkxYR9n4fzXuunuDmlsShF6mrFTk0RkBMuW1NTaWKMhG5bnmXFklG2bQnxjTiFCC4YIgbAAOa61pMKX1zV8bdn++7+9fNq03+6ndYvf18cQ0Ld1Od+/9cJSetRYzLn7t/zmZ38oJWoca++BLqLkaIM8treEYsNxeHWH4uSzMKDpHpBUJwUiGeHN/gIbNs0CP4YRiqhpIuyPkX2hjmj9ccSuuZiGgw92/u2qqy8qe+8dEtfc0jr57PPOO3l9Tz9SWoRhSNH3QEBDbTXLVq8fueLSL98I9O6ZqJHkWwyxgkaGCo2Ph1ffZj3eUG/Cx//QiR1LkoxFMUpT3eYynA7oGQwIiyE9b2fJZHyqHJvudr0P9db+hBpiUui8CW5+tJsbkk0P/2jyft/dLxKZvNnzGI7HYdEi5r+5msMOa2PSYYcAFg88+DKfu/w3XHn9r+jZ1gsVFe/RSQICn6geJF6psFNRQCMkDBioV4qPRyUVd/0Rs2ot0q+guuYKKltvoOmj1zLmy58hOqalVH0444zjgP13BedLV175raaKBNmRYbTRFAONEAJLSkayBYpBTvx8zt1XpKpr9tuTiklEDRhtUFohjY9nCsIVS887rfoXhUye3/y6nZqmeqpTCbQKidULAjQF3xAEmt6OApURi5EOCwJRj0WIa5lcVug1AyFjI5FpSIlVJkh2czP5uXNZfONNzDh+Rjm4UjiORAg487RDSDXWg1d8j6qIgEgTjYM/oEYuQdS1QJWiYUzItM4s3yvmuDOVYsrwNsRIP1ZrG0JEiDZEcOoMCghVCYuWpsZEQ2PjvttvPW7ChOkXX3rZ7LWd3UgpCZRGGB9LQBCG9KRzjGuQlZ/9zLHHVlRWTXynpFbOAGEblAzBC5DkGDX5VLP128/Nrn26fdMwP793E4GooHliEzX1SRqbHaykobo1QsyJMpxWpGosKOiuUjpPY+dCJ1Yw7mWD7Z+5at26X/0lk9kSEYLJ8ThuoUj/c79jXEtVuUY0wCc/eQzL5t/MPd/7ArG4C7nCXwc1FifM51AD6/EGoTiSIxgx/OtJI9yQCmjIVUBmhDCb5U0JPed+DoDsmxdjwuzOvLouZcTuv//+b5xyyilnCSFi1954808SsQijI6MoJNIExFQ32hiKocA2BZpqDE/94dnubVs2rSmT/51Gylz2VTjhJWjKW/QpG9eKgpVAUE2VaOrYEFw655GBs8e3VHHO7HFEahx04KGLRUIvpLfHJ3As9q1Xr9gF72tI2Y1jhrHIn3djB7+fnzNlzzplajQ685zKyjOvq6o+4951m6l54BIu/PQpkO4DW0BFTSmzNJoFab1LycYCy0GZbgZW1eBUTMRfcg6icwBZC/WHNxDOT7LkxhQ/J8aySsHiUcXRx32Ie89cwD51aWKnrkYZ8JXakYaM2aXnbWxv73GSlU3benqJODbYSSJBB/FgC8PRQwmI0uB0Mr414LAjv/i/Fi5acUup0LVTWq2bJ+4L9liYvA0yGixdUi0hFB5hqkkuP3RKYjTuyAmblo8kRnsCjGUz6ksynk3WM7RWhBsSwr8RrE4QWbTvURdTEyOx4GfPDW1Pjw0OhOHS+bnc75cWivLYUB5tjtuPAw/bBwJV8hGFPPhBmVLtUc4pg4nW5Ed6yWba8VaHyGmfId32SYqpg3HyK9EdHcSPiaMPCXlRGKjWnD0r5PxJf6Stt4PoIV/EaT4RYwzKmB2uL9AGKSW1VVXJVDyGETCSyWFElMpwHZbJM+pMIyKyTJ2gufn6Hy548HfPX1eKKNgtc2Td/IdHwJ0O85+GSe2gGg2WZ9BCg1AUUW6lWNVQL5anqmQBHcQXvtlXOf+1ftnVkQ1aYurNcY36ViHlGrTMgcqjpU+uU7V8/HyjxDnMe+U5XX6wBvSGIPhLA8Fphx8wqXWfkybg9fchbRchrRJ4QoIsb2WAtQ7wChlyIz0U9QB+Vx69dSKVHzqEoqhCTTgIk5iFs2oO/kCG+ikRzj4mxz/PHOUjM/uZoItkXEHqxIeQbgXGGEKzS9EKUNoQaIM2UJtMgh0lM9xJdbCCQKTwI+OZNTHg4Z/fu/nya+/9fCn04x3GX5iBAfAFrF4L5pug35QUx0usgoOQLlgRII5FAkdEMbrFy5lxgU8iHhEjslK8hScGCchj6wwm8BmVPg0Diol36zA8hYrp4yhms7s9+Ei44jtHHnbnpOc/T81wF0EQxXZcpOUghARR8p9ahyjlE/oFAr+AViGRtghDc7qIVp1Lw4Wn0z80TBgq/GQtrJ1D4tXP4UqwKkp59cIAeE6UhvOexW0+rvyRDEWld9bmSsmTckWitG/bkv6tCyi0P4qdbKOittn86td/fP2qG//jemAhUNhbEsa6+bbboCIG49tg+Tjom2vYbwjyQqMtjTAaIQKM8AiEh2LIjsoNbkK+LaRYhy9G0GQxOo8JfTwnpK2oGG4wPFKPXPIynQNDLOrt3e3BowiO6+j9fGpSGw3HNJPvG0FrRRgUCfwCvp8n8PMEXo4wKKJUgFEaqx7UJoH3vEvtBbORqQRBECCEwQp9gsYjKNSeiAryhL5mNKwh13wGref+nkjtQTurCwaUMbuWPHfQVAMYDEZCLJYCt4q6pgms39Ddf+Hld93me4XHd01wv1NSd7kxQxoeeQaGr4NzM4IBVzKExFgWUtsIY2GkREiBpLQqqTVaKAQhBaXYRyg6eg3Bjw1jD4KaKC+/8gonfPaz7/Dft8LScw49YnLrcxfjDy1E+0mk5WwvYGGMKdWxjMYYhagTWPlKcrd34X7oFGo/83ECpSkWi6XKgFIESuG5NQQGwuEBDILGfWqpBvxQ7ah/7Vav3h3ZHSGAMQZhOVhW6VTgE0rlFe/7+X2Pff3qr17p+/7w3jJbu9f9Y3lgCN76KOw/xTB0l2LaiEYXNHkRkpMSYwkSUhB1IWqZEuMGZKgxecOqiGHTNYYbzt9x22ceeGBvH7Twe3jg4+niv0XsM4k0xyjmluJnPFQ+3GFLhQSRlFgJF7Etif/ACHZqHKlPn1Z6G22wLAtjQFsCWwhEkMZWBpmqoLoiggsU/WBn9XYX8AQCY/Ruh3dF2qiAsFQtx7ItO+FGkldeftm/LFu6pOPX999//bv0he3aDKHBT8MEFwr7wsP7w36eYVqPoioLTY6gUUPOxttSMHPf6sEjSl1LlP1SKWqdj8Ev2wlOHsOybe08dOddPPHEE2zcuHGv7Ggx/PINM3zltGR1teKTZOZvITa2iNvkYAgwGkwg0Wkwi138l9IYJKkbLkDYLioIEUIgpURIjTTsoIwR1yYetRFG4/lqu+jtRii2S60lZan8bbZjat4hyVorVNHXJBJyzbq1wwveeGPZu7Ue2Xvlgb4HsUGotWA5sGUGXPgpaOkwrzx1P3N+/BYPbeimULo+BIiREuOr88a20/Qu+BO9X8u+n96Xjoey+fs+i7kGI1h7zzoqqzWNR+6LUxktuYBRge4O0Ju2YY+toeKSz2E1NBIGYdlEgJSl8FEphZQS13FwbKtkP7XaTb0NJeokBQxk83ihIhWLEnNttDG7wFi6tynvJ6MxXnnt1a4rL//Kz5YuXboQeGPP7NS7g7qjXmPAtWBcAqptHl02l7tueZJ5C0utSd//6a8fPPGYIw7/4Z13/Pr++37y7QIj3qr0yN/cfrXQ9/+Yz+auiSeTNJ9+BD333I9bsIkkK7AFyKCIHZdEj59J/LxzsSoqUUGwi9RtlzYBwkIKgRSiLHmlc6Ls2Us9GgJtNOmcRzEI0caQzhVwrDiWoOzIdtoAYzSWZSEEHDrr4GbP93LAc2UpDd/bUQUB/OpXMDgIp58O3/seD2/bxh0rV7JgF+993XXX3Xjrrbd+awcwS5as+973v/+DRx588MFydeR9Dzcen7S5vf2tlvr6mK81XQ/8Fm/JElzHxqmswG1rIzprFu706aVv7XslO7iXFskdx3dxRjtgN6VeAl9pCkGAF6gd5jPu2lRGnR3OaYepEKUZvu+H9VV1NsCylW9tmzn9oLZ3SaGZd4IKZH/6U96aN48F9fX8as4clmXfocbRrq6u9ubm5sY9T6zftKnrN7954JGnnnry6SULF75a5nHvNRrXbFi/fPLESY1+WXX80RHI5yGZRCQrsIBAa5TvI8Qu2WLzTse9/XWEKEuwkGQ9n9FCsSTB5QmOZeFaEltKoo4EDFpv/zhmB0wVsQQDQ/3BlVdc9djMGbPs5+cufjG/8fcdhx6UmK4UetGa4uI33y6+qhTB9pysOOGEE3YsbHBggK3btpEeGvorDYCiZnBwcHN1dXXlX0PqrVWrNr7w/Asvv/LqK28sWLBgUeeWLWv3Fn0APPnUkws+dvrHDs0U8igMoRMhEKC8AO0VsYyhIh7DsSy0LqU995TU3YXDlOeYEpBKM5grEGqDLSXKaBzLoi4RQYhStkqb7WAatn83gaEiXsVgulvV1bScBTx11iFc9djvZnwfGyj4mI0FPnxF55deX+v/ZI90+t80xGOPPTb3rLPOOvr9XhACq9es2bhu/YYtmzdv7uzv7x/IZLOZMAiKkUgk+al/+tQF06ZOay36HlJIpJRYUhJ1HSwpSxl3rUp2E7FD0fak7rtTop3HLCEIjSZTCAhUKRURc20SbsmZbZ8vKKm8FGYHqIlYNXPn/nnTccedekhtBQ0Dz01bQ0sjpnOY4Y40f3hxdPSqX6fPGC2a17bb2P9M06/52te+dtvfAqoNTJ8yZeL0KVMmvusv8sKQqnh8r22wEdsmnfXw/RzxeKIsrXvjlWavtjbUJYlNxVxCXaLVlqTs7QGxM7EihEGisaRAytJinnnmT28C6UMmRz9KjQXpDPm0x9BgwG1PZe4cLZo3d13Ff+rXKRs2bHj60ssuu09rbT6oJuyIbb8DUK01QwXoG0nT2fUWylil+NzsGm2VKkFmN0DNzk7CsiAqY9BmO1g7ARUYJCDLEmoLjbRspGXhOlUE2uOOu371G4C3t4SbGQ1Be8RtxdxVfuf6ruD35e+h3ptSvVtr+WWXXXLBRRd9MQzD5P+J3wIYYyj4HhkPsr6Fq7eg0q+RyzYxZp8kWgeYv3LtOwzCLtGTKYMtdhwyyO3tsGgkBpwYjhXbwZV+9MM75oWF9JYx9fa+zbXW2JH+oJCqJSbQPLU4/zSwds+i4d9sU9du2tS93/jxTR8kkIFSFH2fYhCQ9wXF0AI1So29GddbyfKtKVLNR7NvW3UpxtdmT+e/iwXYNbY3u73pDjB3U/eyDTUK4cSQVgxtoK9jNQMvXkP7mrld48bE7HFjYg2plMQIqW2E3NLuM+PL284fzuqH9oyq/mZJ/cpXrrjr2T8+cYsu8bESxXmP4QUBUCLfpVZzTaBCglARqBBfCXztgBE4ZEjoLmrtdvLZXpb2jCFeO5OGarfUom7e6ZTMni32xiCEREQrwCqDp8B4owhhymsxO0AFDVIgLAcHGBaw/smbKC56ihmH1bTU1MVwpcFSGmwjSdosWFtsH87q5WUTuhuo1t/q+TesW7sktJ3pJx5zzGSEYDSfx7WsvwquF4Z0DqYZKRTI5Atkij5ZX+Aph9DYWHhE6SeuNlKhV1Ntb6Fz0Gf1wL7Ut8yiqdpFoHEdd6dzeZe8m9AKGUlB1EX1LkR3PIFILyNaWUMk1lTqEdClAKIErEGgcWyJlHGEgSj9iLd/RmOih3H7NhNrjeE0xaDahdootER44cmRFX9akv9FmYubvwdUgHD+yy89v37Tpuj0gw85uLWuTgohGCkU8IMQ2yqFinuqd65YLDkDaWNLcMUoUdNFTG0mGqwjEm4k5Q6gjGJ1VwX9/lQmjJ9GXUpSKBSIRCJYtrWb3SypsCgDAxKDcGMYNHrlN7HWXovb9wim50k6F/ySonKo2OeYUj7AhAgBjiWJuA65vMIfWkbMXYoZWM/td/526eastaXTd4d6MnI4nZcFTztaRCJWtK3SWvLy8Jpn3sjM2VtC5T/FU8tmQ8YTyVPPv+Az50w5YPp+R5944symurpIGIY4loVtlVp3Qq0o+kHJ6wqJEkli/hKi/jIMNlIIYlGHQNtsG7LoydTgxscxtqUWS5R4pZQW8VgUIa0ddIodQanZBWEbokmChVeQ6r8bd/zR5Hv6WbwwTe+WLvQozPji95l4zFUE/jCOLQl8n5GRYUZGs1Q6aZrGjeXSf/33Z+/56S+vLjsgq1y4jINIJirtVGtzpLY4GmzZ2u3NK3cL8nfZ1PJbBICTz2Wf/Nk99zxx2NFn/dtZ533uICfi4IWjhKEqF/NK3tW2nRIEIoYQAkf3EXEkbiRGwYctgw6D+RQ+DTQ21lFTYZEvDqO0ASSOY6NcG1eWShwG0KqUDMGKlkyPKoIVQ2d6kP1PYyfGQT7PsiXDbNtaoLp+DCOZbRS6lmIBvgkZHBxhdGQE5ReJJ6to2udo7rrtprfu+ekvrwE6dwFMbucPudFArhsNgjLRt/+2LNX7cNoAZ5wyffYf//S9bxYyG+gd1rhOBUZGMNgIYbP91yhSaBwxRL27DhHNMdCfpD0dJeOlCKimqrKSupRNGBbJ5fQOxdZGEfohGRVgWTau62DbFrZlI90kRrolO2bbSGnh57MgbKSVp3Njms5tRWpqo+jCADUtDg2HfpbBkTRDve0EfhHXsUmm6mltjfHoj7+09stfv+/ickEv/34x+CBBLWfGPRueJlahGJfN4wdxjIiDjCJtB8eJQ0UKiiGj6TRLVnVmN3Vb2bHjpzdImZCpZITqpCRUBQq5UmuN2NHxJ5BGlAqBWuMHHsV8KSKqadqHqBvFaIUjJZ52CAS48VaKVpLAG6S7x+A6Fq4VYkyRplNuo5jcn4GtbxNxJDHXpSKVoK5xiAe/e+vyf772j18CVrxbjuL9DuvvBXXdpsE1c5/bPLrP2KlT6xrHpuLJFHY0gR2JIKVLOqNZtnRrYc3GTGbV5qDvuXnbVo+mvdxpx81oTUUCIfHwfR+jQzAKjEKgduxjNMIojFYINOgAablUN4zBRaBCn9ByGC7CwOJf4xVyxIKVeIPr2LrNJRZ1sIvdVB00m/iHrscf3UZSZInoAvs0SxJyJbdedeOzl35n7hXA2+9WIf1bnc7fO6JlDzijbcKUw5uaasdEIm4ChCgWw0J372B356bNb/3inhtv/5d/vXBq0NvtOK4UfQNDpYLeDt9d6kXdNd7R7Kz7GwQYgdE+2k4Rrd2PhpTLqGfoWPEywdrfINvvp7K1jdbxdQx19rJxvSTh5LFiKSo/+h9EohVE0kuotzqQbTF632oPLrnuobsff33wbqCv3Bjxd4feHwSo282IvYv0y12StiFQ+MhR+3/5+Zd/9EOKDj1bMxgJBlkOHbfHPwIhrRJzNNvjx9LfpVDfECpBws0RiTt4VhvR2n3Y9PAXGHjpN7Qe3EhVhU9do0tXV5z+Lk2Ufiqnf5q6aR8n0v8K0QYfgJ/fv/j1q26f96OMz/Nl6czzAYLxQYzw3UoL28cLr7x91z+d+OnG38757DeaaisY7aknH20F7bG95uZIgSVttBFoY1DKlBnA9mK8QRqHQhiSFNuoSm7GDtvYJkahEizpYLkRjBEQQiwise062irAtedDQ5aVC3r7r7v7zV899dq2OcC2ch+U/0GG3Rb/dcNeuSU399cPvrGoraV50vRjj2mV2WGM8hAqJGpDJBLDkhJLaFzLYBFgVLFEl7QPOkDoIiifQsFBhB7+SBYrDKlw+lBqhFjUJhKReHkHE0JlZZSapjoWLuzqvv6O1x68+Duv3bKuY/ThUi2eLO/vP2L4b5HU9yvNYvNA+OdktPXfrYQmInLoUJAyW7GrA7DroOCCioB2UbZFIAxKaZRSBEYSLWwmkV8Bwim31StSYxpg0ulQeBtMJ3QVyAlBsjpKZYXkE9c9f/+j8/rnAGvKql78PwHmfweoAOYLn/zwpSdf8JED2dqOozVO6zBE0qz60/oBoVWwT0uyIVERtXAjWMKiFJhpECHoAEiDPQrahkBQHMH0b+nObO0XA29vym90qoqxi66oOaqxUsAWBZUWr73Vv72krP9ez/4/EVROOHr8h0FDSwYyPXRv3pi7+77Vz97605VzgEJ1ym2oS1m1VXFRWREhEXdM1JbGkiC0xgTGUfnQ8rKFMD+SD7PpbDjSPxIOYugHegDz8pbipfddO/aayEdSgnU5pO1shSD8r3pH8V+MqTVpXO1xp580/p8IM7EVG/o2v/J6erEfsqDsff2yWm5Pd8q/sk69B/1Ru8zVbQ3uJ66+vO1fXnlu8K3fzR++vWxD1f+ToJb/dXbht7oc7n2QHtgta2G0fF9T5qD/f/xfKDQA/O8BAO2JXUDuwuOmAAAAAElFTkSuQmCC"></image></defs></svg>
                        <svg width="45" height="28" viewBox="0 0 85 38" fill="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="_name_lfwq3_15"><rect y="0.5" width="85" height="37" fill="url(#pattern0_1533_11661)"></rect><defs><pattern id="pattern0_1533_11661" patternContentUnits="objectBoundingBox" width="1" height="1"><use xlink:href="#image0_1533_11661" transform="scale(0.0117647 0.027027)"></use></pattern><image id="image0_1533_11661" width="85" height="37" preserveAspectRatio="none" xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFUAAAAlCAYAAAAz16WbAAAACXBIWXMAAAWJAAAFiQFtaJ36AABHCWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxMzIgNzkuMTU5Mjg0LCAyMDE2LzA0LzE5LTEzOjEzOjQwICAgICAgICAiPgogICA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogICAgICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgICAgICAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgICAgICAgICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgICAgICAgICAgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiCiAgICAgICAgICAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIKICAgICAgICAgICAgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIgogICAgICAgICAgICB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIgogICAgICAgICAgICB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyI+CiAgICAgICAgIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnBob3Rvc2hvcDozMDVhOWQ4Ny03Mzc3LTExZjAtYWZiMC05ZmM4YTMwZWQ2ZGM8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6ZmNiNDhlMGMtMzU1My1lZTQ4LTkzNTgtZDNhYzJiZmMyY2YxPC94bXBNTTpJbnN0YW5jZUlEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPjI3QkZBRDUzQzg0ODZDQURENjVBQjYzNDA5RDg1RkJDPC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiNGQxY2Q3MC1lNDk1LTkyNDYtOGZmOC1mZGE5ZTljMDIzYWY8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTA6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpjYTcwNjdlNC05NzUyLTdkNGEtYjA2Yy04YzNjZTk1ZmE5MmU8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTE6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5kZXJpdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmNvbnZlcnRlZCBmcm9tIGltYWdlL2pwZWcgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpkMjlmZGNmOC0xZmZkLTk4NDktOWZhYS0wMjMxZDYyNTEzMDI8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTE6MDcrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+Y29udmVydGVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmZyb20gaW1hZ2UvcG5nIHRvIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3A8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjcyZGMyMDkzLTdlNzEtMDc0NS1hNGI3LWNkZmFhZTk0NjI0ODwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAyNS0wOC0wN1QxMjoxMzo0NiswMjowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIDIwMTUuNSAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5kZXJpdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpwYXJhbWV0ZXJzPmNvbnZlcnRlZCBmcm9tIGFwcGxpY2F0aW9uL3ZuZC5hZG9iZS5waG90b3Nob3AgdG8gaW1hZ2UvcG5nPC9zdEV2dDpwYXJhbWV0ZXJzPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpmY2I0OGUwYy0zNTUzLWVlNDgtOTM1OC1kM2FjMmJmYzJjZjE8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMjUtMDgtMDdUMTI6MTM6NDYrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAyMDE1LjUgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcE1NOkhpc3Rvcnk+CiAgICAgICAgIDx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdFJlZjppbnN0YW5jZUlEPnhtcC5paWQ6NzJkYzIwOTMtN2U3MS0wNzQ1LWE0YjctY2RmYWFlOTQ2MjQ4PC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD54bXAuZGlkOjcyZGMyMDkzLTdlNzEtMDc0NS1hNGI3LWNkZmFhZTk0NjI0ODwvc3RSZWY6ZG9jdW1lbnRJRD4KICAgICAgICAgICAgPHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4yN0JGQUQ1M0M4NDg2Q0FERDY1QUI2MzQwOUQ4NUZCQzwvc3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9tPgogICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3BuZzwvZGM6Zm9ybWF0PgogICAgICAgICA8cGhvdG9zaG9wOkxlZ2FjeUlQVENEaWdlc3Q+Q0RDRkZBN0RBOEM3QkUwOTA1NzA3NkFFQUYwNUMzNEU8L3Bob3Rvc2hvcDpMZWdhY3lJUFRDRGlnZXN0PgogICAgICAgICA8cGhvdG9zaG9wOkNvbG9yTW9kZT4zPC9waG90b3Nob3A6Q29sb3JNb2RlPgogICAgICAgICA8cGhvdG9zaG9wOklDQ1Byb2ZpbGUvPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAyNS0wOC0wN1QxMjowOTowMSswMjowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDI1LTA4LTA3VDEyOjEzOjQ2KzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAyNS0wOC0wN1QxMjoxMzo0NiswMjowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBQaG90b3Nob3AgQ0MgMjAxNS41IChXaW5kb3dzKTwveG1wOkNyZWF0b3JUb29sPgogICAgICAgICA8dGlmZjpJbWFnZVdpZHRoPjgxODwvdGlmZjpJbWFnZVdpZHRoPgogICAgICAgICA8dGlmZjpJbWFnZUxlbmd0aD45NDM8L3RpZmY6SW1hZ2VMZW5ndGg+CiAgICAgICAgIDx0aWZmOkJpdHNQZXJTYW1wbGU+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjg8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT44PC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+ODwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwvdGlmZjpCaXRzUGVyU2FtcGxlPgogICAgICAgICA8dGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPjI8L3RpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6U2FtcGxlc1BlclBpeGVsPjM8L3RpZmY6U2FtcGxlc1BlclBpeGVsPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj4zNjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjM2MDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPGV4aWY6RXhpZlZlcnNpb24+MDIzMTwvZXhpZjpFeGlmVmVyc2lvbj4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT42NTUzNTwvZXhpZjpDb2xvclNwYWNlPgogICAgICAgICA8ZXhpZjpQaXhlbFhEaW1lbnNpb24+ODU8L2V4aWY6UGl4ZWxYRGltZW5zaW9uPgogICAgICAgICA8ZXhpZjpQaXhlbFlEaW1lbnNpb24+Mzc8L2V4aWY6UGl4ZWxZRGltZW5zaW9uPgogICAgICA8L3JkZjpEZXNjcmlwdGlvbj4KICAgPC9yZGY6UkRGPgo8L3g6eG1wbWV0YT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAKPD94cGFja2V0IGVuZD0idyI/Pss1jbQAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAAIQlJREFUeNqMmnecVOX1/9+3TZ/Zma2whYVdlqYgRRCwgAJqsJdgjSWa2JJojMTEWGOM/hSjBo3YokaNGltsICoKKIJ0l77LLtv77vS5/d7fH0vA1ZTveb3mj3me+9znmc/9nDPnfO4RACZOnIjH42HSpEns3LmT+vp6LrvsMgzDYP/+/YiiiG3b1NXVMXXqVObOnctLL71EX18fI0eOpKSkhIF4gnXr1lFUWMDo6mr2NTbjj+RzzOxjWfXZai655FKieRHe/3A54VCIZDJOT1cnruuSTKY4e/5skqkMm/a2UD1iGOOLxeJsd+P4SMAXExVvsjvHXrloXGd/yqS5cS/Dw46iSILX6/Ebw8vLjZ11zSihQmRbZ09zJ0eOqaStvYPunn5GjxmLNxCkq6uX5pZWFEXGI0uUDx+Gmklx0twTaGk+QHdnByfOncPevXvQdR3LsigvL+err75CkiTOPfdcUqkUX3zxBZqmEQ6HaW9vJxgMIkkSpaWl1NXVIfNfTBAEJElGlCQcBFwXRFEkEgriyj5a4gZWSCXupDBzGTRTxJZ8DGR1FElkbL6TH+rbUTCv3CkMHvg4L+UqHQvy+yb4SI2TIlrQ9et5hcPypa+afS9vqu/8XNd15gzLHX9CYPPNednUKSPGlvst28DUc6SVrNEVr/tsRW/+7+aV+SsvHus83tba6sey1WzPZuP8KaP6Xtqn/vqd7d2fLz6x+OYKz/ZTdcWyhLGBTM6sz7i+cNrNVwYSo6KdvTmnLWOJvZak9fXJTm+6vzXT39dNSjfZuq+Frn4N27KQBCgSfGi2QEgWEQ5i8r/se6A6jo0oingCYTp6kuzrShH2iogCdKRN2tM2tQe6QEtyxbToSWElVREQBoblhfXq8ouruO/L5I09bQesZ88avkbvb5xk6WYwOiKGYQ+gBCLklwQRfCV4/RHkYBSPk6PY3bnotdc3hG86sfy2cwp77hPKpiCNO4X8sUfjDeWhpRMM1G3xFO1acerIpm2nir58jOgMxs24HMG1Y65l4e2pHTmpYc0fIpO8b51Zkn3Yrjgej6xgqUks08J1Lax4G1K+i5rLoOk6OVXFCgrxVLah9/gST69TGWmPO7XNmRGFq7OmqaqmkDayLfUhD8mW/gzBvBiO65C1BBI5G29IwrQdHNdFdF1c1x0Kqm3bhIIBvME8BpIm32zbxlhf4tgLi9uPyyM7XpEEb/oY74EDZtPHr7+wdfUtM/zvLSh3z4jnTBR/GCFcQUlQZkFDj9DSl6mrjPlnWfPuxV9QijdajGhmefuW06ma/yPm3/xnFH8QWRJp+eJNGt9e5F123tgvY+mWY7UZv2DW1ffg9XqGPOzqmfMxjJv48snf8tH/e4SLl97M5PN+fmh+81vL8L3/xjGzvMLszuE/5vTfPHuYKDis+82JhE+9heIxR6Ol+tFS/ejxXsx0f8zJDsRQ42PsdC9OphvJ1Rbnclly8X7644nEGUcXtH7aGXjqzQ8/eSIa9BNTLEaX+4r6NbXXlRS6sw6KIlMVCCD8C1TXdQkHA2ze3Ug8a3D1MbFLjtI++WW5R5pWPmUqYrQUV5CQtDjNtV//dvo4oz4mR2vS035O9YwFBKJFRAqHseO5X1Lj+eLH1SMC8V0phUXn//xbsOgU+2zEQBR/KHxoNCxrRP2CorfWHVty1QPM+tHi/+hWHo+Xk278E/09bfTs/BK+BaooQsNOpCnnzOC0258dsk5c/Xv0rWuZ+cunUErG/VfX3fPFCl67YSFn/24JpSU1lHY1RvWW7dEf7fjg8bUrN2zYmHC2vH31pD3JzsbKrr7kAbMk0mAovrq9We8ne1KZlclU+iBTXQfDX8COLz/1PjAv9vqRwfRZ8oQziB13MZVT5xKOFQzComn4N36M9uLdNXs2bWPhI+cTK685dKBRYY2tkilqolCg2d+JPdlWFBE0wxoyLOHQut+h/JQF3wLUBQTAxW7YgDi8BiFQeHAqSVV6H22Fk4fcZ6CjBSECP1zyHkN2rn+FNUvuYY8uMD/XDow7eH8O7jHU/KVjKPXDlJlHQuUph8bfueUHXFT+0eveY6oaHFMbN/7SuxiXS01QW3ZOsAeamZcb+NU1b9aeNnbyjOXykoceYu3mWr76fJXngePkjeVKZpL3vPuZveiG723o9fkYe8KZjDjhTBrnl9HTvH8IqKKZJa05tKcHGFtufjdYk9XA950fYhgGOQ2OvWwooE7nTsRXf4g00AjBPDj3BSg7Gl6dT8P6HXD+vKEPxx/i8ieeJhQrPjwY30zbw5fyepufqogGem7omXq3guhApBKUKKCg5BXj8Qs4zTsQK085dJ55d/ydrn0TqhO9rdXR4VGmnHctMBiiMskBVt86n4CeO9+SpOVyV08ftTt2cl5R91t5kjRp9M3/ZOLxP/ivLuJveIuTK9LgzxtKxs79tDdDAnAEaegi10EzQHaGgppLxSmoVhg59YTD7HFdxA9voGHzXnZ5Sskz+znWuhq5rJhtX+1leRzOcTJD7jPn8t8gSuLhgdQecn8+nac3g+qJITkd2LqG9C2G7njiZ3R/tJ7iseAPhyipKCcpjyFug6Hp+A4fnkhejJPveYMHzz2ec45IQO82KDoGgFBePoHCUoqj2/KWb/wK+fFnX+SoUPqciRX26ZWXPfw9QI0vHkfq2ot05Ikw/jzMHe+i/PV8KqIhzGjBENAip93FFdNVvPnDyR9eMdTNHBvdgMB3mKplM4SK8vF7vYcHzX52bdnIX/bnkXMdEApo0zVGtx7gtYYiDL0DTHVo3Pw2oEDinQd44m/ddAwrJw+dTA6sQ6AOWuDS5UhHbSRt9pFMdNDRuYWv17xHvMcFW/9eiKieehynLr6X/S/ewbhc/1AvDheASszj8yNjalQJ2t15R8xkzsU3HsbINtGeOwv/zhW4wMC6p8ifexlK2xoSeHhub4iLv52RCSLBo87kiP+Y9MroOtjCwTWOCoKJbao4SgnYHTiZOkTZwknBu3uDZHSVsnwvriiwusHgw28yDBvux6eAavnB/ASj7WOQouDauGYfSGE8oRDpiWeyU15FNN2JEcwnZ4Kp9uFVV2DG2xHkCNWVEapHlwPjAR9wC3V/vJ3d996HkU3iOxTbD9vZN9xOU9cKrNqVyJULD40roShBhZidtZCH+e0jYqI9qfrUHw9ZbH2+BP+OFdT5K/nnbovepMbUfX9lzrRiXqz1sieuUZBfPNTDSSCgDGLmtuP07QF0kDVkNQ9DELGRwHiK3K7nCVTPAVTSWgF630rktlsxALnmMbSSk0lufpX8kEyyN4UnUsDIcZNo3fkVtg2G4wVjC1bHMlypFNwcUumJOL2fo7Y3UXHsXVx99308dsMVFI/SyJlg5LKgv4N+4C0EIYRZcDRitAxX78HV2/DXLKKwpJCMAZouE8m9jNb4DqKvBpccglyEJ28Uo666Eas3gqsdAAQEXwVysIBIgKiUcAJiVDbnFZf7GTnl+KFVQcvHNBjwx1UZtraqJDWXD1oC/PETi93tGUoLh4FxABJvYDTdjdGxFKdnI1bDW1hbluLuWY3V/zrqvp9g9TwOWhsGLo5rgdWAm20CW8UTjNG6cyPxgWKk8pPw+MsRw5u5ccmvqDruVFo7PDihSVx+9xLu/McL1Mw8lX2tYBga2DEksQrRDeEtvgqffA+e4utALMPc+jzzLirk+At/yv5daXQbdM0CqRJRKkIa/gP84uV4t8v4+qbg186ARAUhZ4C0AFrWAHcAK7Uas+8NRP9oHHMf6W2XoTkrEcb60ep/hlq7ENRHEAMhRJcorhOVk0ltTLhkLEXlVUO91UizvF4gnrWoLpIY6BvAI/rp7h4g6Af8IyD7BXTfh57N4Cn6LdK6d6B7OfS0wKzf4J6wAKdvM5I4ClwvuuniIIAdQrRjYAdQvEGaG7PEt+9k2KLz2fjqz4m/vIyqGa08/MJ19HcPIHvDRMPfQMtL3P7kTfRoHjra28GYhqCbSPk1yPZEeGQG8sV/huprMLY9hNLwONfcdSsb12+gcVcthumALSHoFkqkmJatSbbe9wiRAj8BRSIWEVi5Lk23DaaughNE9JZBcARibhKS70QYLWO3r8VJ7gAhiCt6wW5C8lbh9cp52GZMzplEkT3IigdccDJbEMMxnNgx1G7dQlJIsqUe5lxwGpff+QD/fOz3vLrsDYrGuHiUILhlyB4ZxROlo2kFn69vob0PjijKsfCELLIlIxoCKDaaBS4CmCaCmgPLIpYXwA3CA4tvY/y7M8k4AXyhcr78+0asP3/A+YuOZNo5xdhbN2PaHnxjo1xz4Wz+/vcPIJdGTHUhF1axv7mZFz7t43f+e/Df/Bpu2Ta02veJzHmLXz94G1eefCGJvl4q3RoE24/T8BWB0iKG3/UMZi6Hrhm0pbMcOVoj88kK2pv2U23ORLBksCVIfYazsRX54p9AuBU3oyJ4fLhEQDXx+AQKC2JCcU+2SBZEkv1xm0znZ4Sc51A7a/FE8pFOvJPpP/Wh7NzP1FnTuPDq6YQCT/OrJecyYMdo3l2L5NigWoiOiOiRaEhJLFkP2SRcMQdOI4GQ7kCMjgSvhKYDvjAECpGj42DYONa9uAPTgCPOm8SCc0YwZfZcUFKQSvDVJoFHbvmYeV+3c+UjNyFqR8OunfS++BuywVOh5ESk6i4YPhJdH8srDWFKV+7j+qOWoCy8FWwRZ9cmpi84jotvvIL9zf0cRQ1CthdHc8gfuJ/CwuEwrBi8hSCFYNTJDBsxjOa3l4EJou1FyKUxY6PxdDyLsDKCcMZvMbbdBIQQBAUyCUJBmarhJdjf1BfLqiBtrt1az75v9jPt5DxoSGOafmz7SX56+xmQGwuRHOz5LbmuHQROO5v7fjKdpx6vJ6HqFFgqYqQa8kbj2jpFAQhqMDzmgdgCxGoBIVIFKRPNABKt0D8ZufB8Xr19I++++yaPfHYFE487BsiClQQjBnons0+KM+3ri7jkhA8pe3gvJ8+vp/eNv/JYLVSN3Qlfv4vgRiGbI7z3faqiOZ5vEZn6j1eYWVCMkn8Sjuco2DvAzT89m+4tX+LkxiFX34qgeHEFC9vVwMmC2o+bbUb2foArT8RUImAJCGoKj9bBfivOjsxEzlm/FGHkMSjjbsba+keEUA2oWWzBRRX9hGS3SE7q7oedgjnw9hOv5U87+RmCY0S0Patw27Zjtq1FEl0sggjlp+Gf8yTmml0oy6/jzKMXIkeOwjWuRZJi0HwAK92EKYgQEWjf9A6sSCM5gL4T1AY8pQEOfPYKjFnHP57fz5stJk+svp5hFZPA6ATLBUEEUYHCY6HhPbxV27n3yTn86YxnSe6AfzYGabIsxut97HjsD+RU8Adg2wFI9EJeqZ8/7RZ59N1llBZHET1htK5OVMfAJ4iIXcshNhp8AQhGIVQw6D1yFMQayMXw2f2oiUaQS1FqbgKtgYriWSzetRx/AE5duxi36HWUUedg7Xkd5DI8PoVNnTqaaRbIIPe0Yby2Zc3n1z9y6XX8ctm9+I48BjPdhSiAKwSQ7DBu1o/w4cso+59jazzAo6u+4HGyCHku+o7NuFaK+j3Q0AElXvh4eQM1PQ1IMnQmIVQKthymJWVy8/176PHl88yGs8kvKgC9Fxz3YEp4MPGWfRAsgh3rGT91DBMvm8uvHljNkTUORw2XWbctS/jy8znhtEWopk6FbXHdzq28+cRS0qLCr1fJVOSncbQE02dPR/YGSCXiVLQkcHZ/haLHkTQbOwMeB0aMhNKRpTC8kO1vd6Dradj4BIIyAQJ5eFObCRuN3LtDZEp1DyUf/Qr7vCdQjoxCfAOFJUXkFZagqrVFgj8cRU0nJ502WvzG6LO5aFaYK6+9AopKBpmTy0L/fmj+HDU+wCe9Jfx1Y4b23gxnVcO40THcyGiEyDBUXylOqIRhw0oIxorJOQoDfb1sXvsZy//+KqUhCEYU6todPvriXCpnSJCtAskHrnW49peDYGvQ9imYaagpZc+aINdf8A5FRQqWaZCKO7y6p5ui4qG58tWzKmiua8NXGGAgpWP12zz/+Qpqjj0VHYgnHGw9i5FNoCb7SLQ3UL95NdtW/INEUy+TJ3nZ2GpTVhDk4gkGIVulolhha4PJQ1tFenWF6RUBHp+fhrHzYNRCkE2omMj9t9zN395Z/67w0IMPsvjXv6bEy7OnjFauaknazChwOH4kDA8PemPK9dOkBtncbrKzXUNTLfK8Am1tFnMXnc7dL75P2Me3auXv2+6NX3LHJQvZuT/NvUvmsuhXUdjTBqPPA9cdBBEGARUV6FwDqUbwREDRcMUxXHP5Otr2tRLvcTnrqtP4zbIPvrfP8peWcsdlv2DsRB/JnEuRrvPk2y/hn37pf9UzWtp7ePaPt/PiX55hQrmI7PeS1WyiIZmCkIRhuXT3a6iqg67InD8lxkUTcuSZGUKjRrG7ReT+jzv4vEF7Xp4ydSpHTZ7MN9u33769wzxhcolUs6FfYV2fQMQ/qPirhkPOSKIILo7loOUctAx4gUmjiyny/c83DEyYcRyX/Po+Hrn1F/zwghI4UA+2CfGdkD95EEgcMFLQVwuZA6CEBkNC1kAod6ioirF3QwsIcOolv/i3+yxY9BNefuh3DPSk8eT50EzIZDL4/8f5RpQV8/snnmbKpDE8cuNivAU6yayLYVh09wsEPAKaZpHTIOqx+XRvko0HRErywlTUx2nqyDKQNuk33A+lvLw8SkqK2Vdfn2lLGl8Ltjuv3O/EPIKDbjgYug22jWjZWIaDaLtYQLvBSsXBN3fejMjkuacfLlXTPQjb/wZNH0G4CPxF3yrThpMfWMu0421oi4MvCEYPZLtB64d0E/TXgtY7CCjiIKiqDsUF1H2js3JVK8fOGclVdy79lgCTGqzRRQlJlrFNjY/fXEN+sYydslhwxjzyamYdvr7uffjoBmhdj2tYCMWHhevxR88mk6indk0toh901cWPi2E4bEvwqKzgK5TcYbppkzUcOuMmu9p1fIpAf9buaEjLN4lLly7l5ZdfoaSoCGDj9jgXburj3b6cm7VMBywH23SxHTDBaNPZVJvmJ70m1wmQ9vmGcsBp/Zr449ez++472bvk/CFzo0cN57yF4zA6O8HjBdMFwQN6DyR2Q+oAuA4o4UGQJAFMGywHJIdIWCEB/ODyu4bS7O2f4B5Ye+jrwivvoWKUh2xcw7RBV4fqqK3Nnby3dBV7XlmG8Ow5sOb+IfNX3f0kVdV+VFvY2KDyUpvB5t0Zns453NaYYXHCJuMTXHyOTUS0qQza2IbJhk7uRpIyMoCqqvT09DBv3jy+2vD1ppZs5sqWLMd7YYoiUISAozv0mlAHbAQOIIgBxXVCsjcwVCyWszy2Ch7rhD9Mnsa3X14oxInJncRzIh6/BI4Dqgl+H8jfdVABDAu0g2K34KKlu6nyh5h/2kxIrgB/CdaAjyU3/oNFd+ZRNXoidryWUOxoTr/yeh6/81FKIwdBdbO4josghdhvFLP4ayjt9HDWaIsbvLehTLoAYoOleiAUYfr8BWz9y3tOwpauTWTtIJAtKipSBxKpTzf26z+KKSzM8zI66mVUFAI7+njbQH5u0oQxh7U7TdO44IILmDJlCitWrIg3t7a9l8lpH+qWruBKIElmwO+1o5EI02fOZvWqj/1mMunz+L4TUIedyMl/eoOZRROZP2/sQRkxgyCF4Jt3IF6HMawUyzCR/TIYDmR18MggCoPu7sCga9ggCIPjokxb/QHmnv0j/IWfoq6/DSl6BLZyA1U3345vwhjszj+jNi4lVHkUZ19wM2/99Q1Sbe0YpgvmMrQdr+GvmkG+dwKVw/14ZJOntwmUirCoby2Otw1X05Bi4xhxxDF47PeO8CiCPy+/tLcgFqG0tJT29jaaWtr/GVezH8dNYmQoB/wgb4/kBRzLsoe+TVVVlZKSEqZNm0Z3dzdja0bb48ePt+vq6vB4PNi2TXd3N5l0AkUWA7KAR/GFhrp/SGT2hXOAXki8hB7fhRiZgJLKp/fvd9F9dCFV4wIMNHVTXBYCRRx0b906LF3+S9sWhUFQBWAgiytVc8b506H/UxzvNOx4F3LwKRZdfwRO/37U5t2I3glonQMUTu/jlAsv5NEHHsayAMHGMTvBakASR5LNqIgOCCn4tEFkkUfC6rwNo6ue0IyLCAbLcEVCuG7xrNmz+iUcOjs7MU2LsuElVFZW5lRVzbW1t7drmk44HMLv87F79+7DoAqCgG3bZLNZ0uk06XQaSZIYMWIEHR0dWJZFMBikpqaGvfvqSKdS3lESnkEh5kv0lk9wMvsQglGEUAyr8XVcI4sQjuELLsB941ae+bgb241yx7lhene3E++ziBXI4BUHc2LnW2K7AMgCCBamEaVtXR1lo+Yx+zg/1t5vkIQixNJzEbwxzHgaxCI8o6bh9NVi99dCx3J+eOFFvPCwQLK/D9xyBGK4GYe84momHLeIZPdOxhQFmf+zG3BjKZyGHKJcCEoR8f4sho2JIlhFBQXkRUL4/X4aGxsRBIGysjIURSEYDNLa2komkyGaN/h6SfxPKYYkSdi2TTqdRlVVJElCEASCwSCVo6oRRdErKnglMQe5v2I2PYrr9CClZ+BtriI4/h5EsRBRiyD5t7Kl4AyWNcKB9U04XQbF1UUM9OXo7rbIphwcG5AOAikPnkxXHZr2qLjhEurrBaqqSxCTb2P0tiHFpuIRFqBsbUA5IKA0KSh7MnjDP0TyFmNuW01ZTTcnLTyexvp6sBUE1UZvSzJcWsdfnlrIsr/dxCv/vJ5Fi1Loda/iZCV8XgHUGWxd/SWWzAHDprW7u5uOjg68Xi+BQADLslBVlVQqRS6XO9QiZNv2v+9QGaLkuy6iKA72BYTDpNNptm3bRjydwyOLPo+MqCgK6BEkuwAlOhXq2vnitjsZ89BrlMy8BW31HVi1jzJm0Z85+8urWfHCs3zy6j5OWTyVUG+GdFxDzXmRZZAVYTCkugKaZhPv1Sg9shq1ZR8p/Wh+cM6l0P8CyviJKDWLqL3jZ2xfuYbCssH6wTsAc377Bzw/+CPmtiWg93DeDVfSurERxCiSHAQ5H7drBYryDvmKFxI6xv4MKMOQCsYhHrWYtctWsvWzVeg+NilOUMtmUqSScQBisRiiKGJZFrIsD2mR+o9tP9/tpXIch0QiQWtrKx6PB0EQ0AwHGdfvkcEbjoESR45OQi6aDRmdpbshd/F1vL/+NXxH3oHV00PEdXn4jh+x59PX+MszzcyeVUHJzBrEfa1kejLkMmA7ArYNtuWihD1UThtFgVdg6cO7GJbnI9j0MvSDEvbBR0/w8Ntr2NClUNQ1GHr7ExZPffA0x48twCPNhkaVY0td+qqz0JjBk3/5YMCOOLiiC5KAKwnICIiIkFdK8q13efK++5DzoLOdryrGVDJ58mQMwwDA5/OxZs0aVFUlFAr933qpvs3SgoICMpkMFRUV6Lp+aK4vkaEn0RGWJIGgnoDsRBR5OOzvg+a11Ezw8uLGOI9cdzU3X3s6cqof1qxEieb48Qkhbn8zw5/+tIe7fhekqKoMfzCFYRjYpo3H6wVRxufx4fX4WPvclyz/WuSIwm8I/H4LqjqYgXUlIC36Ge43yaQsJEmgIOZhxY5uIvddB4IPn6KgOjZpW+DI7RGkUAk+vx9PMIzgD0AgghA4mBP39bLx6y956sN2MopASiOtIb+b6uvi2Wef/R7ZFixYgCiK/zdQZVnGMAxUVWX16tUEg0GCweAQemd1k6wpdGQV0f3q+fuEmt0F9PYMkIz3kLQVUkqQCdWwqrYV8aEnkX0ScRVUV8LAx8RRflbvSqDfs4kLF1YyaWYJ5IfA69DV3I4HCcHJY+Xqffzj8zSuK1PbL7CuHQRRwHVdfIpIULToS1kYDglHdyMVPlPc3efl1g4viuzikQxEAWRJJOSJ4xUHCMgOIcUhILsEJQcvoNnQlIbtPRIuHvIFk8+63ftxzA7HMr7n3pIk0dXVRWlp6b/tAhQO9yKJPPjgg/T29qKqKqWlpdi2PYSh/7J0Os0jjz7OuIi1/tgR0kxFlsnaAgnNRTUh7BXIpnQkSSBhiRiWM5gmueCVAcvBKwvYwLACL0ePiVI1PEgoKFPf0EJGA9UNs3lfnKzmYJmDH1k63BAkHawNtqf4f5rLW8CIMQGeLQ8Q1QUR1wUXF0EQEEQB2znUp4HNwUztICCO6+IRocALomPzdQ/vd+mcf+ZZZxnnnnMOjuN8z4tN0+SNN94gnU7T3d2NpmmUlJRQW1t7mKmO4xCLxWhubmb79u2H4ue/M03TQBRoTLovTtKcmQndxLIP5ui45DSHlA5Zi/Qwvx02nEEATBt0DXIujm67ufKwEOqNG7y1thOvIhLwSciSTFa10IwePLKIodsM6NgGHJAgehBTURYQBiw2aC5LguFIXzad2dSmOyMVgXtwnaB7MLURBBcREF2QRJBF8EiDSYYoDT4ccbBti/4sZl2K1/oMfl1WVmZUV1WRy+X+LQ7JZPJQ6+T3mCpJEpWVlciyzDXXXEMul2PLli2k0+n/uMjj8YAg8NGKFZ7RIT6dWsTxGWOw4jQGSWk2pHmzW+e9ygDXihDVbOKGQ9Kw6E+7rAf6yjz8rljhaL8yKDFy8PCu62JbgzVB3Ka+SeU+B7YChQdDljQIg7Bj5KiRHddfdx2rVq1i5cqP/eAuAEoPXucTIShByCMQUURCskhQEQl7RKKySJDB/8WBnM2BXp2PgHdj+fnZYSUl7Nmz578qW8cddxxer5f9+/cPZWpxcTEjRoygqamJu+++G5/PRzQa5X+lWgfDgrE/wy/6NK5VJHw5k66sQw/QCawD2ppzrGaQYRlABVRfMJRzHYd2Nberz+CyQi8n+UXGSIIbFgZbxjKaQ1O/zuqsy8tI8taSwgKEg+mdbdsIgkgiPsBZZ57J4sWLGT16NGu/+EINBILvaYaBZVmC64Jl26Jp26JpmzI2MjYeBlXLIBBgMBqkEJQBQSYz/8S5ZDIZNmzY8D/lzH/lpd/7X4rH4/j9fm677bbvxY7/Zh6Ph+7ubu65997tiVzu51iCjCCb/nDAxnXc42bPYkRFBXv37u3SNK0rmUzi9XqZMWMGO3fupLm5mcCwkvqmAwfuatd5Bqg+yEQJGAAageaq6mozl80gywpjx4491Cmo6zqbenvIZrOHziNLElOnTKavrw9JktxAIEA4HLYFQbC9Xq/Z1dWFYRjE4/GD4S4ff8BPV2cnqqoSjUY5/fTT+fzzz/+jl343C/h31/3/AQDM5oZEpxjqtgAAAABJRU5ErkJggg=="></image></defs></svg>
                        <span class="aviator__text"></span>
                      </div>
                    </div>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true">
                    <a href="/games.php" type="link" class="_link_p19s5_1"><svg width="22" height="20"
                        viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M22 16.319L21.5141 6.46155C21.5141 3.44531 19.2828 1 16.5305 1C14.8939 1 13.4416 1.86487 12.533 3.20001C11.5338 3.85272 11 3.66431 11 3.66431C11 3.66431 10.4662 3.85272 9.46704 3.20001C8.55847 1.86487 7.1062 1 5.46948 1C2.71722 1 0.485962 3.44531 0.485962 6.46155L0 16.319L0.0113525 16.3245C0.0039673 16.4047 0 16.4863 0 16.5689C0 17.9115 0.993408 19 2.21838 19C2.85828 19 3.43372 18.701 3.83862 18.2253L3.88953 18.2505L7.04974 14.121C7.04974 14.121 8.26538 12.9888 9.66309 13.1221H10.4528H11H11.5472H12.3369C13.7347 12.9888 14.9503 14.121 14.9503 14.121L18.1105 18.2505L18.1614 18.2253C18.5663 18.701 19.1417 19 19.7817 19C21.0066 19 22 17.9115 22 16.5689C22 16.4863 21.9961 16.4047 21.9886 16.3245L22 16.319ZM9 9H7V11H5V9H3V7H5V5H7V7H9V9ZM16 5C16.5524 5 17 5.44763 17 6C17 6.55243 16.5524 7 16 7C15.4476 7 15 6.55243 15 6C15 5.44763 15.4476 5 16 5ZM14 9C13.4476 9 13 8.55237 13 8C13 7.44763 13.4476 7 14 7C14.5524 7 15 7.44763 15 8C15 8.55237 14.5524 9 14 9ZM16 11C15.4476 11 15 10.5524 15 10C15 9.44763 15.4476 9 16 9C16.5524 9 17 9.44763 17 10C17 10.5524 16.5524 11 16 11ZM18 9C17.4476 9 17 8.55237 17 8C17 7.44763 17.4476 7 18 7C18.5524 7 19 7.44763 19 8C19 8.55237 18.5524 9 18 9Z"
                          fill="#0F9658"></path>
                      </svg>
                      <p data-translate="header.games" class="_label_p19s5_35">Juegos</p>
                    </a>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true">
                    <a href="./all_games.php?categorías=all_games" type="link" class="_link_p19s5_1"><svg width="22"
                        height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M18.8799 9.7702C18.6738 8.59296 18.2095 7.50348 17.5476 6.56244C17.0135 5.80292 16.3507 5.1402 15.5912 4.60596C14.6501 3.94415 13.5607 3.47974 12.3834 3.27368C11.9339 3.19495 11.4717 3.15363 11 3.15363C10.5283 3.15363 10.0661 3.19495 9.61658 3.27368C8.43933 3.47974 7.34991 3.94415 6.40881 4.60596C5.64929 5.1402 4.98651 5.80292 4.45233 6.56244C3.79047 7.50348 3.32617 8.59296 3.12006 9.7702C3.04138 10.2197 3 10.6819 3 11.1536C3 11.6253 3.04138 12.0875 3.12006 12.537C3.32617 13.7143 3.79053 14.8036 4.45233 15.7448C4.98657 16.5043 5.64929 17.1671 6.40881 17.7012C7.34991 18.3631 8.43933 18.8275 9.61658 19.0336C10.0661 19.1122 10.5283 19.1536 11 19.1536C11.4717 19.1536 11.9339 19.1122 12.3834 19.0336C13.5607 18.8275 14.6501 18.3631 15.5912 17.7012C16.3507 17.1671 17.0134 16.5043 17.5476 15.7448C18.2095 14.8036 18.6738 13.7143 18.8799 12.537C18.9586 12.0875 19 11.6253 19 11.1536C19 10.6819 18.9586 10.2197 18.8799 9.7702ZM18.2644 9.7702H16.4922C16.3557 9.22827 16.1407 8.71722 15.8604 8.24969L17.1129 6.99719C17.6732 7.8186 18.0721 8.75818 18.2644 9.7702ZM16.0581 11.1536C16.0581 11.6331 15.9911 12.0972 15.8658 12.537C15.7579 12.9163 15.6064 13.2773 15.4176 13.6147C14.9594 14.4338 14.2802 15.113 13.4611 15.5712C13.1237 15.7599 12.7626 15.9115 12.3834 16.0195C11.9435 16.1447 11.4795 16.2117 11 16.2117C10.5205 16.2117 10.0564 16.1447 9.61658 16.0195C9.23737 15.9115 8.87634 15.7599 8.53888 15.5712C7.71979 15.113 7.04053 14.4338 6.5824 13.6147C6.39362 13.2773 6.24213 12.9163 6.13416 12.537C6.00891 12.0972 5.94183 11.6331 5.94183 11.1536C5.94183 10.6741 6.00891 10.21 6.13416 9.7702C6.24213 9.39093 6.39362 9.02997 6.5824 8.6925C7.04053 7.87341 7.71973 7.19415 8.53882 6.73602C8.87634 6.54724 9.23737 6.39575 9.61658 6.28778C10.0564 6.16254 10.5205 6.09546 11 6.09546C11.4795 6.09546 11.9435 6.16254 12.3834 6.28778C12.7626 6.39575 13.1237 6.54724 13.4611 6.73602C14.2802 7.19415 14.9594 7.87335 15.4176 8.6925C15.6064 9.02997 15.7579 9.39093 15.8658 9.7702C15.9911 10.21 16.0581 10.6741 16.0581 11.1536ZM15.1564 5.04071L13.9039 6.29321C13.4364 6.01282 12.9254 5.79797 12.3834 5.66138V3.88922C13.3954 4.08148 14.335 4.48047 15.1564 5.04071ZM9.61658 3.88922V5.66138C9.07465 5.79797 8.5636 6.01282 8.09607 6.29321L6.84357 5.04071C7.66498 4.48047 8.60455 4.08148 9.61658 3.88922ZM4.88708 6.99719L6.13959 8.24969C5.85919 8.71722 5.64435 9.22827 5.50781 9.7702H3.7356C3.92786 8.75818 4.32684 7.8186 4.88708 6.99719ZM3.7356 12.537H5.50781C5.64435 13.0789 5.85919 13.59 6.13959 14.0576L4.88708 15.31C4.32684 14.4886 3.92792 13.549 3.7356 12.537ZM6.84357 17.2665L8.09607 16.014C8.5636 16.2944 9.07465 16.5092 9.61658 16.6458V18.418C8.60461 18.2257 7.66498 17.8268 6.84357 17.2665ZM12.3834 18.418V16.6458C12.9254 16.5092 13.4364 16.2944 13.9039 16.014L15.1564 17.2665C14.335 17.8268 13.3954 18.2257 12.3834 18.418ZM17.1129 15.31L15.8604 14.0576C16.1407 13.59 16.3557 13.0789 16.4922 12.537H18.2644C18.0721 13.549 17.6732 14.4886 17.1129 15.31ZM12.8466 9.94336L11.6364 11.1536L12.8466 12.3638C13.1529 12.2818 13.4908 12.3464 13.7311 12.5868C14.0896 12.9453 14.0896 13.5264 13.7311 13.8848C13.3727 14.2432 12.7916 14.2432 12.4332 13.8848C12.1928 13.6445 12.1282 13.3065 12.2103 13.0002L11 11.79L9.78973 13.0002C9.87177 13.3065 9.80719 13.6445 9.56677 13.8848C9.20831 14.2432 8.62726 14.2432 8.2688 13.8848C7.91034 13.5264 7.91034 12.9453 8.2688 12.5868C8.50909 12.3465 8.84705 12.2819 9.15332 12.3638L10.3636 11.1536L9.15332 9.94336C8.84705 10.0253 8.50909 9.96075 8.2688 9.7204C7.91034 9.36194 7.91034 8.78082 8.2688 8.42236C8.62726 8.06396 9.20831 8.06396 9.56677 8.42236C9.80713 8.66272 9.87164 9.00067 9.78973 9.30695L11 10.5172L12.2103 9.30695C12.1284 9.00067 12.1929 8.66272 12.4332 8.42236C12.7916 8.06396 13.3727 8.06396 13.7311 8.42236C14.0896 8.78082 14.0896 9.36194 13.7311 9.7204C13.4908 9.96075 13.1529 10.0253 12.8466 9.94336ZM18.7781 3.22186C16.7006 1.14423 13.9382 0 11 0C8.06177 0 5.29944 1.14423 3.2218 3.22186C1.14417 5.2995 0 8.06183 0 11C0 13.9382 1.14417 16.7006 3.2218 18.7782C5.29944 20.8558 8.06177 22 11 22C13.9382 22 16.7006 20.8558 18.7781 18.7782C20.8558 16.7006 22 13.9382 22 11C22 8.06183 20.8558 5.2995 18.7781 3.22186ZM17.364 17.364C15.6641 19.0638 13.404 20 11 20C8.59607 20 6.33594 19.0638 4.63605 17.364C2.93616 15.6641 2 13.404 2 11C2 8.59601 2.93616 6.33594 4.63605 4.63605C6.33594 2.93616 8.59601 2 11 2C13.404 2 15.6641 2.93616 17.364 4.63605C19.0638 6.336 20 8.59607 20 11C20 13.404 19.0638 15.6641 17.364 17.364Z"
                          fill="#0F9658"></path>
                      </svg>
                      <p data-translate="header.casino2" class="_label_p19s5_35">Casino</p>
                    </a>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true">
                    <a href="./bonuses.php" type="link" class="_link_p19s5_1"><svg width="22" height="22"
                        viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0ZM11 20C6.03741 20 2 15.9626 2 11C2 6.03735 6.03741 2 11 2C15.9626 2 20 6.03735 20 11C20 15.9626 15.9626 20 11 20ZM11 3C6.58173 3 3 6.58173 3 11C3 15.4183 6.58173 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58173 15.4183 3 11 3ZM11.5651 14.7502V16H10.335V14.836C9.49341 14.7993 8.67792 14.5787 8.20081 14.3089L8.57739 12.8752C9.1051 13.157 9.8454 13.4145 10.6609 13.4145C11.377 13.4145 11.8661 13.1447 11.8661 12.6545C11.8661 12.1887 11.4649 11.8945 10.5359 11.5883C9.19281 11.1472 8.276 10.5344 8.276 9.3457C8.276 8.26733 9.0545 7.42157 10.3977 7.16431V6H11.6276V7.07849C12.4688 7.11523 13.0336 7.28668 13.4479 7.48285L13.0842 8.86774C12.7573 8.73279 12.1799 8.45117 11.276 8.45117C10.4601 8.45117 10.1969 8.79425 10.1969 9.13733C10.1969 9.54169 10.6363 9.79907 11.7028 10.1913C13.1972 10.706 13.7992 11.3801 13.7992 12.483C13.7992 13.5737 13.0084 14.5049 11.5651 14.7502Z"
                          fill="#0A893D"></path>
                      </svg>
                      <p data-translate="header.bonuses" class="_label_p19s5_35">Bonificaciones</p>
                    </a>
                  </li>
                  <li class="_item_1992l_14" aria-hidden="true"><a href="/all_games.php?categorías=live" type="link"
                      class="_link_p19s5_1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M23.1369 9.91624L14.0838 0.863096C12.9329 -0.287699 11.067 -0.287699 9.91625 0.863096L0.86312 9.91624C-0.287739 11.0671 -0.287674 12.933 0.86312 14.0838L9.91625 23.1369C11.067 24.2877 12.9329 24.2877 14.0838 23.1368L23.1369 14.0838C24.2877 12.9329 24.2877 11.067 23.1369 9.91624ZM5.03682 13.0669C4.37329 13.7304 3.29709 13.7304 2.6333 13.0667C1.96957 12.4029 1.96957 11.3267 2.6331 10.6632C3.29709 9.99921 4.37329 9.99921 5.03702 10.663C5.70081 11.3267 5.70081 12.4029 5.03682 13.0669ZM9.05184 9.05189C8.38792 9.71581 7.31166 9.71581 6.64793 9.05208C5.98421 8.38836 5.98421 7.3121 6.64813 6.64817C7.31173 5.98457 8.38792 5.98464 9.05165 6.64837C9.71538 7.3121 9.71544 8.38829 9.05184 9.05189ZM10.6632 2.63308C11.3265 1.96981 12.4028 1.96981 13.0665 2.63354C13.7302 3.29726 13.7302 4.37353 13.0669 5.0368C12.4028 5.70098 11.3265 5.70098 10.6628 5.03725C9.99903 4.37353 9.99903 3.29726 10.6632 2.63308ZM13.3368 21.3669C12.6733 22.0304 11.5971 22.0305 10.9333 21.3667C10.2696 20.7029 10.2696 19.6267 10.9331 18.9631C11.597 18.2992 12.6733 18.2992 13.337 18.9629C14.0007 19.6267 14.0007 20.7029 13.3368 21.3669ZM17.3518 17.3518C16.6879 18.0158 15.6117 18.0158 14.9479 17.352C14.2841 16.6883 14.2841 15.6121 14.9481 14.9481C15.6117 14.2846 16.6879 14.2846 17.3516 14.9484C18.0154 15.6121 18.0154 16.6883 17.3518 17.3518ZM21.3669 13.3368C20.7027 14.001 19.6265 14.001 18.9627 13.3373C18.299 12.6735 18.299 11.5973 18.9632 10.9331C19.6265 10.2698 20.7027 10.2698 21.3664 10.9335C22.0302 11.5973 22.0302 12.6735 21.3669 13.3368Z"
                          fill="#0F9658"></path>
                      </svg>
                      <p data-translate="header.live_games" class="_label_p19s5_35">Juegos en vivo</p>
                    </a></li>

                </ul>
              </nav>
            </div>
          </menu>
        </div>
        <?php if (!$is_logged_in): ?>
        <!-- Блок для неавторизованных -->
        <div class="_menu_8nsrw_1" bis_skin_checked="1">
          <button type="button"
            class="_button_1qy1r_1 _button_1qy1r_1_login _button_color_yellow_1qy1r_33 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_8nsrw_6"
            data-cy="button_open_login" data-translate="header.login">
            Iniciar sesión
          </button>
          <button type="button" data-cy="button_open_register" data-translate="header.register"
            class="_button_1qy1r_1 _button_color_green_1qy1r_39 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_8nsrw_6">
            Registrarse
          </button>
        </div>
        <?php else: ?>
        <!-- Блок для авторизованных -->
        <div class="_menu_8nsrw_122">
          <div class="_menu_8nsrw_1 _menu_logged_8nsrw_40">
            <div class="_info_z3cl7_14">
              <span style="display: flex;align-items: center;font-size: 12px;line-height: 18px;" data-translate="header.balance">Saldo</span>
              <span class="_count_z3cl7_20" style="display:flex;font-weight: 700;">
                <div class="summ-balance" id="balance">
                  <?php echo htmlspecialchars($deposit); ?>
                </div>
                <div class="summ-balance" id="currency">
                  <?php echo htmlspecialchars($currency); ?>
                </div>
              </span>
            </div>
          </div>
          <div class="_balance_z3cl7_1">
            <div class="_info_z3cl7_14">
              <span data-translate="header.bonuses">Bonificaciones</span>
              <span class="_count_z3cl7_20">
                <?php echo htmlspecialchars($bonificaciones); ?> COP
              </span>
            </div>
          </div>
        </div>

        <div style="display: flex;gap: 8px;">
          <button type="button" data-translate="header.deposit"
            class="href_accaunt_two _button_1r6hv_1 _button_color_green_1r6hv_39 _button_border-radius_medium_1r6hv_23 _button_border_1r6hv_20 _button_flex_1r6hv_14 _topUpButton_8nsrw_9">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"
              class="_menu_8nsrw_svg">
              <path
                d="M7 0C3.14014 0 0 3.14014 0 7C0 10.8599 3.14014 14 7 14C10.8599 14 14 10.8599 14 7C14 3.14014 10.8599 0 7 0ZM7 12C4.24316 12 2 9.75684 2 7C2 4.24316 4.24316 2 7 2C9.75684 2 12 4.24316 12 7C12 9.75684 9.75684 12 7 12ZM10 7C10 7.55225 9.55225 8 9 8H8V9C8 9.55225 7.55225 10 7 10C6.44775 10 6 9.55225 6 9V8H5C4.44775 8 4 7.55225 4 7C4 6.44775 4.44775 6 5 6H6V5C6 4.44775 6.44775 4 7 4C7.55225 4 8 4.44775 8 5V6H9C9.55225 6 10 6.44775 10 7Z"
                fill="white"></path>
            </svg>
            Recargar en 1 clic
          </button>
          <button type="button"
            class="href_accaunt  _button_1r6hv_1 _button_color_yellow_1r6hv_33 _button_border-radius_medium_1r6hv_23 _button_border_1r6hv_20 _button_flex_1r6hv_14 _accountButton_8nsrw_23"
            data-id="menu-button">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M6.9997 8C9.20527 8 10.9997 6.20557 10.9997 4C10.9997 1.79443 9.20527 0 6.9997 0C4.79414 0 2.9997 1.79443 2.9997 4C2.9997 6.20557 4.79414 8 6.9997 8ZM6.9997 2C8.10273 2 8.9997 2.89697 8.9997 4C8.9997 5.10303 8.10273 6 6.9997 6C5.89667 6 4.9997 5.10303 4.9997 4C4.9997 2.89697 5.89667 2 6.9997 2ZM13.5178 13.8555C13.3561 13.9536 13.1769 14 13.0007 14C12.6623 14 12.3327 13.8286 12.1442 13.5181C12.0837 13.4209 10.5319 11 6.9997 11C3.46747 11 1.91572 13.4209 1.85175 13.5239C1.66132 13.8281 1.33369 13.9966 0.998725 13.9966C0.820502 13.9966 0.640326 13.9487 0.477729 13.8491C0.0094671 13.561 -0.140435 12.9517 0.144233 12.4819C0.230658 12.3398 2.3083 9 6.9997 9C11.6911 9 13.7687 12.3398 13.8552 12.4819C14.1413 12.9541 13.9904 13.5688 13.5178 13.8555Z"
                fill="#fff"></path>
            </svg>
            <span style="" class="_menu_8nsrw_span" data-translate="header.account"> Cuenta
              <svg width="2" height="10" viewBox="0 0 2 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M0 1C0 0.447715 0.447715 0 1 0C1.55228 0 2 0.447715 2 1C2 1.55228 1.55228 2 1 2C0.447715 2 0 1.55228 0 1Z"
                  fill="#FFF"></path>
                <path
                  d="M0 5C0 4.44772 0.447715 4 1 4C1.55228 4 2 4.44772 2 5C2 5.55228 1.55228 6 1 6C0.447715 6 0 5.55228 0 5Z"
                  fill="#FFF"></path>
                <path
                  d="M0 9C0 8.44772 0.447715 8 1 8C1.55228 8 2 8.44772 2 9C2 9.55228 1.55228 10 1 10C0.447715 10 0 9.55228 0 9Z"
                  fill="#FFF"></path>
              </svg>
            </span>
          </button>
        </div>
        <?php endif; ?>
      </div>
      <menu class="_menu_ohy0w_1 _menu_ohy0w_2 _menu_mobile_ohy0w_49 open-modal-button">
        <nav class="_navigation_1992l_1 _navigation_mobile_1992l_17">
          <ul class="_list_1992l_6">
            <li class="_item_1992l_14" aria-hidden="true">
              <a href="./index.php" type="link" class="_link_p19s5_1 _link_active_p19s5_42  _link_mobile_p19s5_45"
                aria-current="page"><svg width="22" height="20" viewBox="0 0 22 20" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M4.78021 8L9.5929 20L0 8H4.78021ZM12.4072 20L22 8H17.2198L12.4072 20ZM11 8H6.21985L11 20L15.7802 8H11ZM7.53589 0H4.97864L0 7H4.78015L7.53589 0ZM11 7H15.7802L13.1313 0H8.86871L6.21985 7H11ZM22 7L17.0214 0H14.4641L17.2198 7H22Z"
                    fill="#0F9658"></path>
                </svg>
                <p data-translate="header.home" class="_label_p19s5_35">Inicio</p>
              </a>
            </li>
            <li class="_item_1992l_14" aria-hidden="true">
              <div class="_link_p19s5_1 _link_mobile_p19s5_45" bis_skin_checked="1">
                <div class="_aviator_kuupf_1 games__href _aviator_mobile_kuupf_24" aria-hidden="true"
                  bis_skin_checked="1">
                  <div class="_loader_r2b2v_1 _loader_kuupf_21" bis_skin_checked="1">
                    <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                  </div>
                  <svg width="28" height="16" viewBox="0 0 28 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="_logo_kuupf_8">
                    <path
                      d="M3.24974 14.8199H3.25674L3.24974 14.8244V14.8199ZM16.2499 8.22863L19.8042 8.92072L20.4999 7.60897L19.8032 6.79701L16.2499 8.22863ZM24.3195 5.61953C24.3077 5.62165 24.294 5.62378 24.2823 5.62826C24.2702 5.63275 24.2592 5.63949 24.25 5.64808L24.375 5.61717C24.354 5.61717 24.338 5.61953 24.3195 5.61953ZM27.8618 12.1573C27.7988 12.2483 27.6545 12.2455 27.59 12.1559C27.28 11.7268 26.4748 10.6081 26.3605 10.4493C26.1335 10.1284 25.8698 9.81221 25.7573 9.44009L25 7.1243C25.0446 7.1225 25.0885 7.11307 25.1295 7.09645C25.164 7.08111 25.1938 7.06247 25.222 7.041C25.3728 6.90979 25.5303 6.72929 25.6645 6.56104L27.0698 8.90939C27.378 9.56987 27.9115 11.3703 27.9843 11.6421V11.6419C28.0293 11.8026 27.9743 11.9968 27.8618 12.1573ZM27.7375 11.5109C27.5635 11.2117 26.223 9.5852 26.165 9.51748L26.1533 9.50333L26.1368 9.51276C26.1368 9.51276 25.991 9.60386 26.1153 9.78579C26.5263 10.3892 27.663 11.9475 27.6745 11.9631L27.6928 11.9876L27.7093 11.9631C27.716 11.9536 27.87 11.7346 27.7375 11.5109ZM24.6735 7.18589C25.2775 8.42849 25.0508 8.76145 23.9387 9.40682C22.8117 10.0633 21.0045 10.7632 19.6972 11.27C17.3817 12.1705 7.04329 15.0743 4.74451 15.8051C3.932 16.0649 3.932 16.0635 3.80799 15.8098C3.76149 15.7166 3.71674 15.6295 3.67874 15.5469C3.03999 15.8579 3.12948 15.4552 2.75373 15.222L3.39398 14.9636L3.39899 14.9606C6.40453 15.0665 18.6562 10.4769 18.6562 10.4769C18.6562 10.4769 18.6627 10.4068 18.4922 10.4068C18.1214 10.4099 17.3537 10.3773 17.2694 10.4163C15.7599 11.1332 10.8643 12.9015 10.7668 12.7585L10.0786 12.2453C10.0544 12.2278 10.0223 12.2237 9.99407 12.2344C9.58357 12.4008 6.65278 13.5424 4.27301 14.4598C4.27301 14.4598 4.08575 14.1441 4.31575 14.0245C4.78101 13.785 5.17325 13.6075 5.17325 13.6075C5.17325 13.6075 5.16826 13.519 4.86051 13.5438C3.937 13.6217 2.98199 12.8954 2.70898 12.7009C2.38472 12.4676 2.15448 12.5343 1.96272 12.6264C1.95556 12.6298 1.94896 12.6341 1.94313 12.6393C1.90953 12.6691 1.90788 12.719 1.93947 12.7507L3.81774 14.6278C3.68306 14.6832 3.54353 14.7274 3.40074 14.76L1.14922 12.7455C1.13446 12.7316 1.09471 12.7269 1.06322 12.7408L0.0126997 13.1986C0.0126997 13.1986 -0.0545466 12.6448 0.120443 12.5268C0.239952 12.4458 1.01772 12.1254 1.55547 11.805C1.70947 11.7148 1.57197 11.5657 1.57197 11.5657L0.461451 12.0943C0.461451 12.0943 0.334206 11.8127 0.542705 11.6962C1.30896 11.2731 1.87323 10.9357 2.39798 11.1426C3.02023 11.3882 4.74126 13.0011 5.65301 12.9794C5.99902 12.9711 10.0206 11.0444 10.4553 10.7879C10.7368 10.6247 10.5778 10.4772 10.5778 10.4772L8.0278 11.6122C8.0278 11.6122 7.88654 11.3526 8.02555 11.2747C8.20106 11.1766 8.42931 11.0631 8.65106 10.9529C8.65872 10.9491 8.66573 10.9442 8.67179 10.9384C8.70374 10.9077 8.7033 10.8584 8.67081 10.8283C8.5798 10.7429 8.53581 10.7117 8.40181 10.5765C8.377 10.552 8.33842 10.5451 8.3058 10.5593C7.36754 10.9807 5.83502 11.7026 5.83502 11.7026C5.83502 11.7026 5.85377 11.5636 5.89128 11.4116C5.98053 11.0487 5.97377 10.791 6.24878 10.6541C6.93229 10.3169 7.6388 9.98069 8.3688 9.60432C8.48106 9.54532 8.36055 9.19539 8.36055 9.19539L7.4818 9.5753C7.45029 9.58945 7.4123 9.58309 7.3873 9.55831L6.27028 8.46436L6.26853 8.45351C6.61279 8.03182 7.4088 7.62926 8.2463 7.70855C10.2188 7.89048 18.0849 8.98065 18.3777 9.01487C18.4539 9.02432 18.6227 9.06159 18.6309 9.22654C18.6327 9.25911 18.6209 9.31054 18.5997 9.35562C18.4969 9.54839 18.4557 9.5812 18.4557 9.5812L19.5562 9.96677C19.574 9.97428 19.5942 9.97489 19.6124 9.96842C19.8357 9.89386 21.3897 9.34948 23.5445 8.10049C23.5761 8.08202 23.6167 8.08448 23.6455 8.10663C23.773 8.21259 23.978 8.39145 24.079 8.38956C24.2082 8.38343 24.276 8.26213 24.276 8.26213C23.8922 7.75056 23.1655 6.2045 22.9172 5.52018C22.8842 5.42863 22.7915 5.36798 22.6857 5.3602C22.555 5.35241 22.3762 5.34462 22.2487 5.35548C21.7025 5.40361 21.269 5.47535 19.4717 6.1455C18.1577 6.6323 15.9832 7.71468 15.3839 8.01814C15.3541 8.03207 15.3409 8.0585 15.3409 8.08493C15.3409 8.11592 15.3595 8.14423 15.3889 8.15807C15.2929 8.14557 13.1944 7.97426 13.0026 7.95538C12.9971 7.95487 12.9916 7.95384 12.9863 7.95229C12.9425 7.93958 12.9179 7.89574 12.9314 7.85439C13.0041 7.63351 12.9561 7.29938 13.3701 7.01953C14.6921 6.11128 15.9104 5.57304 16.5029 5.50932C16.8852 5.46898 17.1267 5.67427 17.9244 5.93077C18.2254 6.02893 18.7717 5.87815 19.0612 5.80335C21.0305 5.31205 21.466 5.12683 22.583 4.94631C23.0017 4.87788 23.49 4.85004 23.8175 5.37105C23.93 5.55134 24.2445 6.3043 24.673 7.18589H24.6735ZM16.5722 6.38807C16.5722 6.38807 15.9921 6.17712 15.7054 6.20921C14.7414 6.31753 13.7279 7.16394 13.7279 7.16394C13.7279 7.16394 14.6439 7.32464 14.7829 7.23118C15.2794 6.89541 16.5722 6.38807 16.5722 6.38807ZM17.8382 6.20921C17.4512 6.05677 16.8619 5.82341 16.7212 5.78305C16.4779 5.71463 16.2231 5.78778 16.0591 5.91047L17.0289 6.27622C17.0553 6.28601 17.0771 6.30449 17.0903 6.32826C17.1204 6.38267 17.0981 6.44981 17.0404 6.47821C16.8784 6.55774 16.6782 6.6651 16.5904 6.70216C16.2494 6.84703 16.5524 7.01953 16.5524 7.01953L17.8564 6.39114C17.9292 6.35716 17.9574 6.25593 17.8382 6.20921ZM18.1249 9.24022L8.4563 7.98676C8.34555 7.97426 8.33731 8.0972 8.39856 8.11277C8.42006 8.11418 17.1069 9.77068 17.3202 9.80465C17.7574 9.87191 18.0549 9.54203 18.1824 9.36457H18.1827C18.2192 9.31479 18.1859 9.24966 18.1249 9.24022ZM22.085 0.622188C22.0377 0.448524 22.0915 0.232374 22.2147 0.0601216C22.2248 0.0457336 22.2378 0.0333332 22.2529 0.0235828C22.3178 -0.0184289 22.4065 -0.00280232 22.451 0.0584653C22.8749 0.648947 23.2977 1.24012 23.7195 1.832C23.9522 2.16212 24.2205 2.48446 24.3772 2.98187H24.3775L25.2075 5.45364C24.963 5.44892 24.6933 5.44726 24.521 5.45835C24.4873 5.46051 24.4543 5.46802 24.4232 5.48055C24.3775 5.49966 24.3372 5.52797 24.305 5.56101L22.9995 3.40827C22.6857 2.73245 22.161 0.898742 22.085 0.622188ZM22.3262 0.769207C22.5065 1.07714 23.8695 2.74024 23.927 2.8115L23.9387 2.82566L23.9557 2.81623V2.81598C23.9575 2.81457 24.1057 2.72279 23.9795 2.53636C23.5625 1.92049 22.4037 0.323699 22.392 0.306474L22.3732 0.282868L22.3565 0.306474C22.3497 0.315907 22.1945 0.538654 22.3262 0.769207ZM25.846 5.68064H25.8463C25.995 5.70991 26.0338 5.80735 25.9713 5.93101C25.7663 6.32412 25.4413 6.72032 25.0963 7.00018C25.0847 7.00829 25.0722 7.01518 25.059 7.02071C24.9945 7.0443 24.905 7.03534 24.8745 6.98083C24.6474 6.59127 24.4411 6.1912 24.2565 5.7821C24.2362 5.73798 24.265 5.68064 24.3107 5.65114C24.3181 5.64462 24.3268 5.63956 24.3363 5.63628C24.3462 5.63344 24.3565 5.63203 24.365 5.63038C24.3802 5.62896 24.392 5.62896 24.409 5.62896C24.8135 5.60678 25.489 5.61126 25.846 5.68064ZM24.536 5.7689C24.5275 5.77031 24.5207 5.77338 24.514 5.77621C24.429 5.80878 24.4665 5.87059 24.4665 5.87059L24.673 6.28424L25.8055 5.85714C25.8055 5.85714 25.8003 5.81303 25.6583 5.78801C25.3908 5.74247 24.536 5.7689 24.536 5.7689ZM21.5095 5.61717C21.5095 5.61717 21.3565 6.21488 21.1157 6.90508L20.6697 6.37202L20.3082 6.52753L20.9657 7.31449C20.7607 7.84541 20.5132 8.37351 20.2499 8.68475L20.9089 8.40159C21.059 8.26992 21.1802 8.00375 21.2745 7.68473L21.6365 8.11819L22 7.96174L21.3862 7.22813C21.519 6.56506 21.557 5.8437 21.5095 5.61717Z"
                      fill="#E50539"></path>
                  </svg><svg width="55" height="14" viewBox="0 0 55 14" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="_name_kuupf_33">
                    <path
                      d="M16.1372 4.27695L14.0604 12.7197C14.5819 12.7197 15.0783 12.5311 15.5486 12.1566C16.019 11.7831 16.3189 11.333 16.453 10.81L18.0567 4.27788H20.5555L20.5378 4.34885H24.8136L22.9509 11.6234C22.9481 11.6383 22.9397 11.6617 22.9267 11.7009C22.9155 11.7336 22.9081 11.7616 22.9025 11.785L22.8997 11.7962C22.8103 12.335 23.0189 12.6441 23.5237 12.7235L23.5423 12.7263L23.2117 14H22.8242C21.9283 14 21.2549 13.8058 20.8023 13.421C20.3487 13.0335 20.1764 12.5115 20.2873 11.8541C20.2966 11.7868 20.3124 11.7205 20.3338 11.6561L20.3441 11.6234L21.8957 5.6226H20.2267L19.314 9.35886C19.0179 10.5924 18.3324 11.6673 17.2558 12.5834L17.2176 12.6151C16.1186 13.5378 14.961 14 13.7437 14H11.2477L13.3051 5.55817L13.5705 4.27788H16.1372L16.1372 4.27695ZM34.1306 4.27695L32.2978 11.8578C32.294 11.883 32.2866 11.9082 32.2773 11.9325C32.267 11.9568 32.2587 11.9829 32.254 12.0091C32.1842 12.4554 32.3872 12.6908 32.864 12.7169L32.5511 14H31.6794C30.816 14 30.2293 13.7385 29.9154 13.2146C29.2886 13.7376 28.6125 13.9991 27.8842 13.9991H27.4967C26.6557 13.9991 25.9768 13.7656 25.459 13.3006C24.9393 12.8355 24.7391 12.2453 24.8536 11.5309C24.8629 11.4469 24.8816 11.3629 24.9095 11.2825L25.9423 6.9944C26.1267 6.21932 26.6017 5.57124 27.3691 5.05389C28.1356 4.53749 28.9636 4.27788 29.8539 4.27788H34.1297L34.1306 4.27695ZM44.2561 4.27695C45.1008 4.27695 45.7788 4.51134 46.2957 4.97732C46.8033 5.4349 47.0054 6.01574 46.903 6.71798L46.8983 6.7488L46.8583 6.99533L45.8021 11.2816C45.6187 12.0352 45.1446 12.6768 44.3772 13.2062C43.6219 13.7255 42.8014 13.9907 41.9101 14H41.4938C40.6388 14 39.9478 13.7703 39.4262 13.3118C38.9019 12.8505 38.6998 12.2584 38.8162 11.5309C38.8255 11.459 38.8413 11.3871 38.8618 11.3171L38.8721 11.2825L39.931 6.99533C40.1145 6.23146 40.5857 5.58618 41.3466 5.0623C42.0936 4.54869 42.9178 4.28722 43.8203 4.27788H44.2561L44.2561 4.27695ZM9.38041 0C10.3583 0 11.1332 0.240928 11.7097 0.720918C12.2815 1.20464 12.505 1.84338 12.3756 2.63901C12.3709 2.66702 12.3625 2.71651 12.343 2.79402C12.3225 2.86873 12.315 2.91729 12.3104 2.9453L10.0258 12.1398C9.96436 12.504 10.1152 12.7001 10.4766 12.7253L10.1553 14H9.15689C8.545 14 8.08585 13.8487 7.78596 13.5452C7.48142 13.2417 7.37617 12.8159 7.46558 12.2705C7.47583 12.1865 7.49539 12.1043 7.52239 12.0249L8.32986 8.79669H6.05368L4.7554 14H2.25569L3.55025 8.79669H0C0 7.8862 0.721785 7.14567 1.62239 7.14567C2.22962 7.14567 3.00169 7.14194 3.83803 7.14101H3.96376L5.01058 2.9453C5.23038 2.05723 5.74634 1.34378 6.56126 0.80683C7.37617 0.269877 8.31403 0 9.38041 0ZM39.4346 1.94424L38.8721 4.14154H39.9478L39.6311 5.39101H38.5759L37.0951 11.2984C37.0942 11.3134 37.0858 11.3358 37.0755 11.3731C37.0625 11.4105 37.0541 11.4422 37.0504 11.4674C36.9628 12.0025 37.1752 12.2948 37.6855 12.3434L37.368 13.6106H36.4962C35.7931 13.6106 35.2678 13.4266 34.9185 13.0615C34.5721 12.6936 34.4501 12.1949 34.5535 11.5618C34.5609 11.5169 34.5702 11.473 34.5823 11.431C34.5991 11.3675 34.6103 11.3236 34.614 11.2984L36.0948 5.39101H34.8748L35.1914 4.14154H36.4087L36.9526 1.94424C36.9526 1.94424 39.4346 1.94424 39.4346 1.94424ZM51.6425 4.02481L51.4488 4.83351C52.2032 4.29469 53.101 4.02481 54.1376 4.02481H55L54.3136 6.63861H52.9119L53.2165 5.39848C52.5906 5.61139 52.0318 5.99613 51.5345 6.55456C51.0381 7.11019 50.7056 7.70784 50.5352 8.34752L49.1987 13.5704H46.6264L49.0832 4.02481H51.6425V4.02481ZM31.3348 5.56003H29.5569C29.0381 5.56003 28.6982 5.88033 28.5361 6.51721L27.3468 11.4348C27.3431 11.46 27.3347 11.4992 27.3198 11.5506L27.2946 11.6439C27.1829 12.3611 27.4837 12.7178 28.1999 12.7178C28.5817 12.7178 28.945 12.5778 29.2849 12.2976C29.6248 12.0156 29.8456 11.6766 29.9452 11.2825L31.3348 5.56003ZM43.7318 5.57871C43.4338 5.57871 43.1637 5.71038 42.9243 5.97185C42.6915 6.22585 42.521 6.55456 42.4158 6.95704L42.4065 6.9944L41.369 11.2825C41.3625 11.318 41.3541 11.3526 41.3438 11.3871C41.3299 11.432 41.3205 11.4665 41.3159 11.4927C41.26 11.8503 41.2963 12.1426 41.4277 12.3714C41.559 12.603 41.749 12.7169 41.9976 12.7169C42.2705 12.7169 42.5322 12.5927 42.7818 12.3434C43.0258 12.1015 43.2037 11.7597 43.3155 11.319L43.3248 11.2816L44.3623 6.99533L44.3958 6.78522C44.4582 6.40328 44.4228 6.10726 44.2952 5.89528C44.1676 5.68423 43.9804 5.57964 43.7318 5.57964L43.7318 5.57871ZM9.06003 1.27468C8.72289 1.27468 8.41461 1.39795 8.14266 1.64354C7.87164 1.89007 7.69283 2.20478 7.60715 2.58671L6.45136 7.14007L6.4467 7.16809H8.71916L8.72754 7.14007L9.88333 2.58391L9.9122 2.41209C9.96435 2.08431 9.9122 1.80976 9.76225 1.59685C9.60765 1.37927 9.37668 1.27281 9.06003 1.27281V1.27468ZM24.4736 0C24.835 0 25.1256 0.127935 25.3435 0.381003C25.5596 0.634071 25.6378 0.9385 25.5782 1.29242C25.5195 1.64728 25.336 1.95451 25.0362 2.21411C24.7335 2.47372 24.4001 2.60352 24.0359 2.60352C23.6736 2.60352 23.384 2.47559 23.1688 2.22252C22.9509 1.96852 22.8717 1.66035 22.936 1.29242C22.9947 0.938501 23.1753 0.634071 23.4762 0.381003C23.7789 0.127935 24.1095 9.33831e-08 24.4727 9.33831e-08L24.4736 0Z"
                      fill="#E50539"></path>
                  </svg><span class="aviator__text"></span>
                </div>
              </div>
            </li>
            <li class="_item_1992l_14" aria-hidden="true">
              <a href="/games.php" type="link" class="_link_p19s5_1 _link_mobile_p19s5_45"><svg width="22" height="20"
                  viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M22 16.319L21.5141 6.46155C21.5141 3.44531 19.2828 1 16.5305 1C14.8939 1 13.4416 1.86487 12.533 3.20001C11.5338 3.85272 11 3.66431 11 3.66431C11 3.66431 10.4662 3.85272 9.46704 3.20001C8.55847 1.86487 7.1062 1 5.46948 1C2.71722 1 0.485962 3.44531 0.485962 6.46155L0 16.319L0.0113525 16.3245C0.0039673 16.4047 0 16.4863 0 16.5689C0 17.9115 0.993408 19 2.21838 19C2.85828 19 3.43372 18.701 3.83862 18.2253L3.88953 18.2505L7.04974 14.121C7.04974 14.121 8.26538 12.9888 9.66309 13.1221H10.4528H11H11.5472H12.3369C13.7347 12.9888 14.9503 14.121 14.9503 14.121L18.1105 18.2505L18.1614 18.2253C18.5663 18.701 19.1417 19 19.7817 19C21.0066 19 22 17.9115 22 16.5689C22 16.4863 21.9961 16.4047 21.9886 16.3245L22 16.319ZM9 9H7V11H5V9H3V7H5V5H7V7H9V9ZM16 5C16.5524 5 17 5.44763 17 6C17 6.55243 16.5524 7 16 7C15.4476 7 15 6.55243 15 6C15 5.44763 15.4476 5 16 5ZM14 9C13.4476 9 13 8.55237 13 8C13 7.44763 13.4476 7 14 7C14.5524 7 15 7.44763 15 8C15 8.55237 14.5524 9 14 9ZM16 11C15.4476 11 15 10.5524 15 10C15 9.44763 15.4476 9 16 9C16.5524 9 17 9.44763 17 10C17 10.5524 16.5524 11 16 11ZM18 9C17.4476 9 17 8.55237 17 8C17 7.44763 17.4476 7 18 7C18.5524 7 19 7.44763 19 8C19 8.55237 18.5524 9 18 9Z"
                    fill="#0F9658"></path>
                </svg>
                <p data-translate="header.games" class="_label_p19s5_35">Juegos</p>
              </a>
            </li>
            <li class="_item_1992l_14" aria-hidden="true">
              <a href="./all_games.php?categorías=all_games" type="link"
                class="_link_p19s5_1 _link_mobile_p19s5_45"><svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M18.8799 9.7702C18.6738 8.59296 18.2095 7.50348 17.5476 6.56244C17.0135 5.80292 16.3507 5.1402 15.5912 4.60596C14.6501 3.94415 13.5607 3.47974 12.3834 3.27368C11.9339 3.19495 11.4717 3.15363 11 3.15363C10.5283 3.15363 10.0661 3.19495 9.61658 3.27368C8.43933 3.47974 7.34991 3.94415 6.40881 4.60596C5.64929 5.1402 4.98651 5.80292 4.45233 6.56244C3.79047 7.50348 3.32617 8.59296 3.12006 9.7702C3.04138 10.2197 3 10.6819 3 11.1536C3 11.6253 3.04138 12.0875 3.12006 12.537C3.32617 13.7143 3.79053 14.8036 4.45233 15.7448C4.98657 16.5043 5.64929 17.1671 6.40881 17.7012C7.34991 18.3631 8.43933 18.8275 9.61658 19.0336C10.0661 19.1122 10.5283 19.1536 11 19.1536C11.4717 19.1536 11.9339 19.1122 12.3834 19.0336C13.5607 18.8275 14.6501 18.3631 15.5912 17.7012C16.3507 17.1671 17.0134 16.5043 17.5476 15.7448C18.2095 14.8036 18.6738 13.7143 18.8799 12.537C18.9586 12.0875 19 11.6253 19 11.1536C19 10.6819 18.9586 10.2197 18.8799 9.7702ZM18.2644 9.7702H16.4922C16.3557 9.22827 16.1407 8.71722 15.8604 8.24969L17.1129 6.99719C17.6732 7.8186 18.0721 8.75818 18.2644 9.7702ZM16.0581 11.1536C16.0581 11.6331 15.9911 12.0972 15.8658 12.537C15.7579 12.9163 15.6064 13.2773 15.4176 13.6147C14.9594 14.4338 14.2802 15.113 13.4611 15.5712C13.1237 15.7599 12.7626 15.9115 12.3834 16.0195C11.9435 16.1447 11.4795 16.2117 11 16.2117C10.5205 16.2117 10.0564 16.1447 9.61658 16.0195C9.23737 15.9115 8.87634 15.7599 8.53888 15.5712C7.71979 15.113 7.04053 14.4338 6.5824 13.6147C6.39362 13.2773 6.24213 12.9163 6.13416 12.537C6.00891 12.0972 5.94183 11.6331 5.94183 11.1536C5.94183 10.6741 6.00891 10.21 6.13416 9.7702C6.24213 9.39093 6.39362 9.02997 6.5824 8.6925C7.04053 7.87341 7.71973 7.19415 8.53882 6.73602C8.87634 6.54724 9.23737 6.39575 9.61658 6.28778C10.0564 6.16254 10.5205 6.09546 11 6.09546C11.4795 6.09546 11.9435 6.16254 12.3834 6.28778C12.7626 6.39575 13.1237 6.54724 13.4611 6.73602C14.2802 7.19415 14.9594 7.87335 15.4176 8.6925C15.6064 9.02997 15.7579 9.39093 15.8658 9.7702C15.9911 10.21 16.0581 10.6741 16.0581 11.1536ZM15.1564 5.04071L13.9039 6.29321C13.4364 6.01282 12.9254 5.79797 12.3834 5.66138V3.88922C13.3954 4.08148 14.335 4.48047 15.1564 5.04071ZM9.61658 3.88922V5.66138C9.07465 5.79797 8.5636 6.01282 8.09607 6.29321L6.84357 5.04071C7.66498 4.48047 8.60455 4.08148 9.61658 3.88922ZM4.88708 6.99719L6.13959 8.24969C5.85919 8.71722 5.64435 9.22827 5.50781 9.7702H3.7356C3.92786 8.75818 4.32684 7.8186 4.88708 6.99719ZM3.7356 12.537H5.50781C5.64435 13.0789 5.85919 13.59 6.13959 14.0576L4.88708 15.31C4.32684 14.4886 3.92792 13.549 3.7356 12.537ZM6.84357 17.2665L8.09607 16.014C8.5636 16.2944 9.07465 16.5092 9.61658 16.6458V18.418C8.60461 18.2257 7.66498 17.8268 6.84357 17.2665ZM12.3834 18.418V16.6458C12.9254 16.5092 13.4364 16.2944 13.9039 16.014L15.1564 17.2665C14.335 17.8268 13.3954 18.2257 12.3834 18.418ZM17.1129 15.31L15.8604 14.0576C16.1407 13.59 16.3557 13.0789 16.4922 12.537H18.2644C18.0721 13.549 17.6732 14.4886 17.1129 15.31ZM12.8466 9.94336L11.6364 11.1536L12.8466 12.3638C13.1529 12.2818 13.4908 12.3464 13.7311 12.5868C14.0896 12.9453 14.0896 13.5264 13.7311 13.8848C13.3727 14.2432 12.7916 14.2432 12.4332 13.8848C12.1928 13.6445 12.1282 13.3065 12.2103 13.0002L11 11.79L9.78973 13.0002C9.87177 13.3065 9.80719 13.6445 9.56677 13.8848C9.20831 14.2432 8.62726 14.2432 8.2688 13.8848C7.91034 13.5264 7.91034 12.9453 8.2688 12.5868C8.50909 12.3465 8.84705 12.2819 9.15332 12.3638L10.3636 11.1536L9.15332 9.94336C8.84705 10.0253 8.50909 9.96075 8.2688 9.7204C7.91034 9.36194 7.91034 8.78082 8.2688 8.42236C8.62726 8.06396 9.20831 8.06396 9.56677 8.42236C9.80713 8.66272 9.87164 9.00067 9.78973 9.30695L11 10.5172L12.2103 9.30695C12.1284 9.00067 12.1929 8.66272 12.4332 8.42236C12.7916 8.06396 13.3727 8.06396 13.7311 8.42236C14.0896 8.78082 14.0896 9.36194 13.7311 9.7204C13.4908 9.96075 13.1529 10.0253 12.8466 9.94336ZM18.7781 3.22186C16.7006 1.14423 13.9382 0 11 0C8.06177 0 5.29944 1.14423 3.2218 3.22186C1.14417 5.2995 0 8.06183 0 11C0 13.9382 1.14417 16.7006 3.2218 18.7782C5.29944 20.8558 8.06177 22 11 22C13.9382 22 16.7006 20.8558 18.7781 18.7782C20.8558 16.7006 22 13.9382 22 11C22 8.06183 20.8558 5.2995 18.7781 3.22186ZM17.364 17.364C15.6641 19.0638 13.404 20 11 20C8.59607 20 6.33594 19.0638 4.63605 17.364C2.93616 15.6641 2 13.404 2 11C2 8.59601 2.93616 6.33594 4.63605 4.63605C6.33594 2.93616 8.59601 2 11 2C13.404 2 15.6641 2.93616 17.364 4.63605C19.0638 6.336 20 8.59607 20 11C20 13.404 19.0638 15.6641 17.364 17.364Z"
                    fill="#0F9658"></path>
                </svg>
                <p data-translate="header.casino2" class="_label_p19s5_35">Casino</p>
              </a>
            </li>
            <li class="_item_1992l_14" aria-hidden="true">
              <a href="./bonuses.php" type="link" class="_link_p19s5_1  _link_mobile_p19s5_45"><svg width="22"
                  height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M11 0C4.92487 0 0 4.92487 0 11C0 17.0751 4.92487 22 11 22C17.0751 22 22 17.0751 22 11C22 4.92487 17.0751 0 11 0ZM11 20C6.03741 20 2 15.9626 2 11C2 6.03735 6.03741 2 11 2C15.9626 2 20 6.03735 20 11C20 15.9626 15.9626 20 11 20ZM11 3C6.58173 3 3 6.58173 3 11C3 15.4183 6.58173 19 11 19C15.4183 19 19 15.4183 19 11C19 6.58173 15.4183 3 11 3ZM11.5651 14.7502V16H10.335V14.836C9.49341 14.7993 8.67792 14.5787 8.20081 14.3089L8.57739 12.8752C9.1051 13.157 9.8454 13.4145 10.6609 13.4145C11.377 13.4145 11.8661 13.1447 11.8661 12.6545C11.8661 12.1887 11.4649 11.8945 10.5359 11.5883C9.19281 11.1472 8.276 10.5344 8.276 9.3457C8.276 8.26733 9.0545 7.42157 10.3977 7.16431V6H11.6276V7.07849C12.4688 7.11523 13.0336 7.28668 13.4479 7.48285L13.0842 8.86774C12.7573 8.73279 12.1799 8.45117 11.276 8.45117C10.4601 8.45117 10.1969 8.79425 10.1969 9.13733C10.1969 9.54169 10.6363 9.79907 11.7028 10.1913C13.1972 10.706 13.7992 11.3801 13.7992 12.483C13.7992 13.5737 13.0084 14.5049 11.5651 14.7502Z"
                    fill="#0A893D"></path>
                </svg>
                <p data-translate="header.bonuses" class="_label_p19s5_35">Bonificaciones</p>
              </a>
            </li>
            <li class="_item_1992l_14" aria-hidden="true">
              <a href="/all_games.php?categorías=live" type="link" class="_link_p19s5_1 _link_mobile_p19s5_45"><svg
                  width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M23.1369 9.91624L14.0838 0.863096C12.9329 -0.287699 11.067 -0.287699 9.91625 0.863096L0.86312 9.91624C-0.287739 11.0671 -0.287674 12.933 0.86312 14.0838L9.91625 23.1369C11.067 24.2877 12.9329 24.2877 14.0838 23.1368L23.1369 14.0838C24.2877 12.9329 24.2877 11.067 23.1369 9.91624ZM5.03682 13.0669C4.37329 13.7304 3.29709 13.7304 2.6333 13.0667C1.96957 12.4029 1.96957 11.3267 2.6331 10.6632C3.29709 9.99921 4.37329 9.99921 5.03702 10.663C5.70081 11.3267 5.70081 12.4029 5.03682 13.0669ZM9.05184 9.05189C8.38792 9.71581 7.31166 9.71581 6.64793 9.05208C5.98421 8.38836 5.98421 7.3121 6.64813 6.64817C7.31173 5.98457 8.38792 5.98464 9.05165 6.64837C9.71538 7.3121 9.71544 8.38829 9.05184 9.05189ZM10.6632 2.63308C11.3265 1.96981 12.4028 1.96981 13.0665 2.63354C13.7302 3.29726 13.7302 4.37353 13.0669 5.0368C12.4028 5.70098 11.3265 5.70098 10.6628 5.03725C9.99903 4.37353 9.99903 3.29726 10.6632 2.63308ZM13.3368 21.3669C12.6733 22.0304 11.5971 22.0305 10.9333 21.3667C10.2696 20.7029 10.2696 19.6267 10.9331 18.9631C11.597 18.2992 12.6733 18.2992 13.337 18.9629C14.0007 19.6267 14.0007 20.7029 13.3368 21.3669ZM17.3518 17.3518C16.6879 18.0158 15.6117 18.0158 14.9479 17.352C14.2841 16.6883 14.2841 15.6121 14.9481 14.9481C15.6117 14.2846 16.6879 14.2846 17.3516 14.9484C18.0154 15.6121 18.0154 16.6883 17.3518 17.3518ZM21.3669 13.3368C20.7027 14.001 19.6265 14.001 18.9627 13.3373C18.299 12.6735 18.299 11.5973 18.9632 10.9331C19.6265 10.2698 20.7027 10.2698 21.3664 10.9335C22.0302 11.5973 22.0302 12.6735 21.3669 13.3368Z"
                    fill="#0F9658"></path>
                </svg>
                <p data-translate="header.live_games" class="_label_p19s5_35">Juegos en vivo</p>
              </a>
            </li>
          </ul>
        </nav>
      </menu>
    </header>

    <main class="main-content">
      <div class="_container_4gtrl_1" style="position: relative !important;" bis_skin_checked="1">
        <article class="_banner_o6otq_1">
          <div style="position:relative;">
            <div class="owl-carousel owl-theme" id="bannerCarousel">
              <a href="deposit.php" class="item">
                  <img fetchpriority="high" alt="banner" src="./images/partners/4-banner.png">
              </a>
              <a href="deposit.php" class="item">
                <img fetchpriority="high" alt="banner" src="./images/partners/3-banner.png">
              </a>
            </div>
          </div>
        </article>

        <div class="custom-slider-nav">
          <button class="slider-btn slider-prev"><span>&#8592;</span></button>
          <button class="slider-btn slider-next"><span>&#8594;</span></button>
        </div>
          </div>
        </article>
        <style>
        /* Стили слайдера */
        /* .promo-slider-content {
          position: absolute;
          top: 10%;
          left: 5%;
          z-index: 2;
          color: #fff;
          text-align: left;
          width: 60%;
          max-width: 650px;
        } */
        
        .promo-title {
          font-size: 2.9em;
          font-weight: 950;
          line-height: 1.08;
          margin-bottom: 0.6em;
        }
        
        .highlight-amount {
          color: #FFD600;
          font-weight: 950;
          letter-spacing: 1px;
          text-shadow: 0 1px 2px #222;
        }
        
        .partners-row {
          display: grid;
          grid-template-columns: repeat(5, 1fr);
          gap: 12px;
          margin: 12px 0 12px 0;
          height: 40px;
        }
        
        .partner-logo {
          height: 40px;
          overflow: hidden;
          padding: 0 8px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        
        .partner-logo img {
          height: 100%;
          max-height: 100%;
          max-width: 100%;
          object-fit: contain;
        }

        .promo-slider-btn {
          display: inline-block;
          padding: 0.7em 1.6em;
          background: #FFD600;
          color: #1a237e;
          font-weight: 900;
          border-radius: 14px;
          text-decoration: none;
          font-size: 1.5em;
          box-shadow: 0 2px 12px rgba(0,0,0,0.22);
          margin-top: 2em;
        }
        
        .custom-slider-nav {
          position: absolute;
          right: 35%;
          bottom: 32px;
          display: flex;
          justify-content: flex-end;
          gap: 32px;
          z-index: 10;
        }
        
        .slider-btn {
          width: 90px;
          height: 44px;
          background: #fff;
          border: none;
          border-radius: 12px;
          box-shadow: 0 2px 8px rgba(255,214,0,0.18);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.4em;
          color: #1a237e;
          cursor: pointer;
          outline: none;
          transition: box-shadow 0.2s;
        }
        
        .slider-btn span {
          font-family: 'Montserrat', sans-serif;
          font-weight: 900;
        }
        
        .slider-btn:active {
          box-shadow: 0 1px 2px rgba(255,214,0,0.28), 0 0 0 2px #FFD600 inset;
          transform: translateY(2px);
        }

        /* Fixed deposit button style for mobile */
        @media (max-width: 600px) {
          #fixed-deposit-btn {
            left: 10px !important;
            bottom: 10px !important;
            padding: 10px 18px !important;
            font-size: 15px !important;
          }
        }
        </style>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
          var owl = $('#bannerCarousel');
          $('.slider-prev').click(function(){ owl.trigger('prev.owl.carousel'); });
          $('.slider-next').click(function(){ owl.trigger('next.owl.carousel'); });
        });
        </script>

        <div class="_row_14oe3_1" bis_skin_checked="1">
          <div class="_column_14oe3_6" bis_skin_checked="1">
            <div class="_showCase_780w6_1" bis_skin_checked="1">
              <div class="_header_780w6_10" bis_skin_checked="1">
                <div class="_row_780w6_15" bis_skin_checked="1">
                  <h2 class="_title_780w6_19" data-translate="main.casino">Casino</h2>
                </div>
                <div class="_slider_14oe3_17" bis_skin_checked="1">
                  <div class="swiper swiper-initialized swiper-horizontal navigation-tabs" draggable="true" bis_skin_checked="1">
                    <div class="swiper-wrapper" bis_skin_checked="1" style="transform: translate3d(0px, 0px, 0px)">
                      <div class="swiper-slide swiper-slide-active" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_984)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_984" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.pragmaticplay">PragmaticPlay</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/jEBtY5wzVUqECjrAFoFGTstIkmJRjufxoKjhrAKL.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.wazdan">Wazdan</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_5197)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_5197" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.threeoaks">ThreeOaks</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/B7iNFZVxKk5gZNJ4qoEWzMzNr9UmHzuwlBY77R42.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.bgaming">BGaming</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/fiLxi7V73uTysqDPoLjj3X6adt2La9W1lCKqO2py.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.gamebeat">Gamebeat</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/cPBBc4afdFjTiPf0ms7TSekQsbIjEXNYinzNOxTD.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.evoplay">Evoplay</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/ph5WVnYX9JZ9STNwzZ975aTu2PfedWgHLX3cY4Ra.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.blueprint">Blueprint</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/2xP9oZOJyBQBxz4LYnqeLZPzeWCHXo2KrvF6GPFL.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.smartsoft">SmartSoft</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/2dWkVeJVMBVMfkb0db3t0OjudoRlNb2dHCFsCQp7.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.spadegaming">Spadegaming</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/fgy8lJtLqfF2lkfdDgrMqrmSx1IHeCIwFKuKhlmH.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.belatra_games">Belatra
                            Games</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/DzZw4aweYOucdFNhUppG69bJdox9tjSbaosTvefs.png" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.redrake">RedRake</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/zspodIHpiFOGYoFZEIf0HYOUXwkPmSKIfUzvktNR.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.100hp">100HP</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_7266)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_7266" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF7A00"></stop>
                                <stop offset="0.890625" stop-color="#8200D1"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.endorphina">Endorphina</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/Ke0EyAXnqQ7rV47p9sjGQdtQ4OOP3iYohUpdAZrD.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.fantasma">Fantasma</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/VV0NB9wyKfG0INvnPZBdo5Qzbbs72YOha8P6b8m2.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.hogaming">HoGaming</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_6870)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_6870" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="white"></stop>
                                <stop offset="0.890625" stop-color="#00C5D1"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.netent">NetEnt</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_8075)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_8075" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.netgame">NetGame</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/ku2IJ4wQKjVPqpQo1dgrWPKOlbXsKkvavAJKhRQq.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.playngoasia">PlayngoAsia</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_8339)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_8339" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#00F0FF"></stop>
                                <stop offset="0.890625" stop-color="#000AFF"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.pragmaticplaylive">PragmaticPlayLive</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/81t1JsFocODCL3T2ps7NmHS6ivdZdSEKqErt8hQb.png" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.print_studios">Print
                            Studios</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_7662)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_7662" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.push_gaming">Push
                            Gaming</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/ZJgLWVfXCVm267QGGmGhzHg4g7bxBSaOYV9D8ilw.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.quickspin">Quickspin</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_241)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_241" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.red_tiger">Red
                            Tiger</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><svg width="12" height="12" viewbox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="navigation-tabs__item-icon">
                            <path d="M4.23862 1.11628C5.02146 -0.372094 6.97854 -0.372092 7.76138 1.11628L11.7245 8.65117C12.5073 10.1395 11.5288 12 9.9631 12H2.0369C0.471227 12 -0.507314 10.1395 0.275521 8.65116L4.23862 1.11628Z" fill="url(#paint0_linear_260)"></path>
                            <defs>
                              <lineargradient id="paint0_linear_260" x1="0" y1="0" x2="7.5185" y2="11.5079" gradientunits="userSpaceOnUse">
                                <stop stop-color="#FF00B8"></stop>
                                <stop offset="0.890625" stop-color="#FAFF00"></stop>
                              </lineargradient>
                            </defs>
                          </svg><span class="navigation-tabs__item-name" data-translate="main.valor_games">Valor
                            Games</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/kBS37YtBoQIN6qizFjPXJ4QftVKaS71m3I6z1euy.png" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.xprogaming">XProgaming</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/nAcGsbHgtp9iQaVsdOqhMcCiYNa0Rgb2otlqnFzy.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.yggdrasil">Yggdrasil</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/7kL37guAHRK2obTPmqzFtwCr1xhE1C43N5Vtc49Y.png" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.ezugi">Ezugi</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/DwJzRGNT8Fy1mWIeJr8ROvkEPVpINKfESxLtalNA.png" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.relax_gaming_slots">Relax Gaming
                            Slots</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/SfOCG280h7nD1rQG9enAwvvIiRQ8X4JrYjALgLC7.jpg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.big_time_gaming_branded">Big Time Gaming
                            Branded</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/tIZOTI0tKJA0EJut602DRLAiVNnqGK0CrruwS9FT.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.silverback">Silverback</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><img class="navigation-tabs__item-icon" src="./images/eMSrzSNzTeWkE8Axo4xcGBSl5PXCzopu1NrpAAV0.svg" alt width="14" height="14" loading="lazy"><span class="navigation-tabs__item-name" data-translate="main.betsoft">Betsoft</span></a>
                      </div>
                    </div>
                    <div class="_buttons_18u6m_1" bis_skin_checked="1">
                      <button title="Previous" class="_button_18u6m_1 _button_hidden_18u6m_51" type="button" disabled>
                        <svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.0361552 5.18562C0.048553 5.21651 0.0680351 5.24306 0.0867844 5.27132C0.100404 5.29189 0.106938 5.31539 0.123978 5.33431C0.129902 5.34084 0.13833 5.34297 0.144559 5.34914C0.151278 5.35591 0.153903 5.36495 0.16111 5.37142L5.1642 9.87158C5.25996 9.95752 5.38015 10 5.49936 10C5.63617 10 5.77199 9.94433 5.87166 9.83447C6.05635 9.62938 6.03974 9.31296 5.83453 9.12839L1.24454 4.99982L5.83453 0.871258C6.03974 0.686681 6.05635 0.370263 5.87166 0.165177C5.68503 -0.039908 5.3694 -0.0560216 5.1642 0.128067L0.16111 4.62823C0.154209 4.63445 0.151644 4.64318 0.14517 4.64965C0.138696 4.65606 0.130146 4.65856 0.123978 4.66534C0.106938 4.68426 0.100404 4.70776 0.0867844 4.72833C0.0680351 4.75659 0.048553 4.78314 0.0361552 4.81402C0.0240016 4.84406 0.019299 4.87445 0.0132527 4.90583C0.0071454 4.93744 0 4.96766 0 4.99982C0 5.03199 0.0071454 5.0622 0.0132527 5.09382C0.019299 5.12519 0.0240016 5.15559 0.0361552 5.18562Z" fill="#202040"></path>
                        </svg></button><button title="Next" class="_button_18u6m_1 _button_next_18u6m_48" type="button">
                        <svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.0361552 5.18562C0.048553 5.21651 0.0680351 5.24306 0.0867844 5.27132C0.100404 5.29189 0.106938 5.31539 0.123978 5.33431C0.129902 5.34084 0.13833 5.34297 0.144559 5.34914C0.151278 5.35591 0.153903 5.36495 0.16111 5.37142L5.1642 9.87158C5.25996 9.95752 5.38015 10 5.49936 10C5.63617 10 5.77199 9.94433 5.87166 9.83447C6.05635 9.62938 6.03974 9.31296 5.83453 9.12839L1.24454 4.99982L5.83453 0.871258C6.03974 0.686681 6.05635 0.370263 5.87166 0.165177C5.68503 -0.039908 5.3694 -0.0560216 5.1642 0.128067L0.16111 4.62823C0.154209 4.63445 0.151644 4.64318 0.14517 4.64965C0.138696 4.65606 0.130146 4.65856 0.123978 4.66534C0.106938 4.68426 0.100404 4.70776 0.0867844 4.72833C0.0680351 4.75659 0.048553 4.78314 0.0361552 4.81402C0.0240016 4.84406 0.019299 4.87445 0.0132527 4.90583C0.0071454 4.93744 0 4.96766 0 4.99982C0 5.03199 0.0071454 5.0622 0.0132527 5.09382C0.019299 5.12519 0.0240016 5.15559 0.0361552 5.18562Z" fill="#202040"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                <a href="/all_games.php?categor%C3%ADas=all_games" class="_link_1n9vq_1 _link_color_blue_1n9vq_28 _link_780w6_30" data-translate="main.todos_los_juegos">Todos
                  los juegos</a>
              </div>
              <div class="_body_780w6_33" bis_skin_checked="1">
                <div class="_gamesList_15kvw_1 _list_14oe3_30" bis_skin_checked="1">
                  <div class="_plate_121bb_12" style="grid-template-columns: repeat(5, 1fr); gap: 8px" bis_skin_checked="1">
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultMobile222221.avif" alt="aviator" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.diver">Diver</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-1.jpeg" alt="aviator" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.diver">Diver</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-2.jpeg" alt="Wheel" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.wheel">Wheel</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-4.jpeg" alt="Mines" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.mines">Mines</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-4.png" alt="Plinko AZTEC" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.plinko_aztec">Plinko AZTEC</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-11.jpeg" alt="Crash" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.crash">Crash</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop.png" alt="Chicken Road" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.chicken_road">Chicken Road</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-2.png" alt="Plinko 1000" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.plinko_1000">Plinko 1000</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-3.png" alt="Crime Empire" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.crime_empire">Crime Empire</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-1.png" alt="Air Jet" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.air_jet">Air Jet</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultMobile1212.avif" alt="Air Jet" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.air_jet">Air Jet</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop.jpeg" alt="Roulette" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.roulette">Roulette</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="_column_14oe3_6" bis_skin_checked="1">
            <div class="_showCase_780w6_1" bis_skin_checked="1">
              <div class="_header_780w6_10" bis_skin_checked="1">
                <div class="_row_780w6_15" bis_skin_checked="1">
                  <h2 class="_title_780w6_19" data-translate="main.juegos_en_vivo">Juegos en vivo</h2>
                </div>
                <div class="_slider_14oe3_17" bis_skin_checked="1">
                  <div class="swiper swiper-initialized swiper-horizontal navigation-tabs swiper-backface-hidden" draggable="true" bis_skin_checked="1">
                    <div class="swiper-wrapper" bis_skin_checked="1" style="transform: translate3d(0px, 0px, 0px)">
                      <div class="swiper-slide swiper-slide-active" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.roulette">Roulette</span></a>
                      </div>
                      <div class="swiper-slide swiper-slide-next" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.baccarat">Baccarat</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.crash">Crash</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.blackjack">Blackjack</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.instant_win">instant
                            win</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.slots">Slots</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.live">Live</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.valor_games">Valor
                            Games</span></a>
                      </div>
                      <div class="swiper-slide" style="margin-right: 8px" bis_skin_checked="1">
                        <a class="navigation-tabs__item"><span class="navigation-tabs__item-name" data-translate="main.all_games">All
                            Games</span></a>
                      </div>
                    </div>
                    <div class="_buttons_18u6m_1" bis_skin_checked="1">
                      <button title="Previous" class="_button_18u6m_1 _button_hidden_18u6m_51" type="button" disabled>
                        <svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.0361552 5.18562C0.048553 5.21651 0.0680351 5.24306 0.0867844 5.27132C0.100404 5.29189 0.106938 5.31539 0.123978 5.33431C0.129902 5.34084 0.13833 5.34297 0.144559 5.34914C0.151278 5.35591 0.153903 5.36495 0.16111 5.37142L5.1642 9.87158C5.25996 9.95752 5.38015 10 5.49936 10C5.63617 10 5.77199 9.94433 5.87166 9.83447C6.05635 9.62938 6.03974 9.31296 5.83453 9.12839L1.24454 4.99982L5.83453 0.871258C6.03974 0.686681 6.05635 0.370263 5.87166 0.165177C5.68503 -0.039908 5.3694 -0.0560216 5.1642 0.128067L0.16111 4.62823C0.154209 4.63445 0.151644 4.64318 0.14517 4.64965C0.138696 4.65606 0.130146 4.65856 0.123978 4.66534C0.106938 4.68426 0.100404 4.70776 0.0867844 4.72833C0.0680351 4.75659 0.048553 4.78314 0.0361552 4.81402C0.0240016 4.84406 0.019299 4.87445 0.0132527 4.90583C0.0071454 4.93744 0 4.96766 0 4.99982C0 5.03199 0.0071454 5.0622 0.0132527 5.09382C0.019299 5.12519 0.0240016 5.15559 0.0361552 5.18562Z" fill="#202040"></path>
                        </svg></button><button title="Next" class="_button_18u6m_1 _button_next_18u6m_48" type="button">
                        <svg width="6" height="10" viewbox="0 0 6 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M0.0361552 5.18562C0.048553 5.21651 0.0680351 5.24306 0.0867844 5.27132C0.100404 5.29189 0.106938 5.31539 0.123978 5.33431C0.129902 5.34084 0.13833 5.34297 0.144559 5.34914C0.151278 5.35591 0.153903 5.36495 0.16111 5.37142L5.1642 9.87158C5.25996 9.95752 5.38015 10 5.49936 10C5.63617 10 5.77199 9.94433 5.87166 9.83447C6.05635 9.62938 6.03974 9.31296 5.83453 9.12839L1.24454 4.99982L5.83453 0.871258C6.03974 0.686681 6.05635 0.370263 5.87166 0.165177C5.68503 -0.039908 5.3694 -0.0560216 5.1642 0.128067L0.16111 4.62823C0.154209 4.63445 0.151644 4.64318 0.14517 4.64965C0.138696 4.65606 0.130146 4.65856 0.123978 4.66534C0.106938 4.68426 0.100404 4.70776 0.0867844 4.72833C0.0680351 4.75659 0.048553 4.78314 0.0361552 4.81402C0.0240016 4.84406 0.019299 4.87445 0.0132527 4.90583C0.0071454 4.93744 0 4.96766 0 4.99982C0 5.03199 0.0071454 5.0622 0.0132527 5.09382C0.019299 5.12519 0.0240016 5.15559 0.0361552 5.18562Z" fill="#202040"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                <a href="/all_games.php?categor%C3%ADas=live" class="_link_1n9vq_1 _link_color_blue_1n9vq_28 _link_780w6_30" data-translate="main.todos_los_juegos">Todos
                  los
                  juegos</a>
              </div>
              <div class="_body_780w6_33" bis_skin_checked="1">
                <div class="_gamesList_15kvw_1 _list_14oe3_30" bis_skin_checked="1">
                  <div class="_plate_121bb_12 _plate_121bb_2" style="grid-template-columns: repeat(5, 1fr); gap: 8px" bis_skin_checked="1">
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-12.jpeg" alt="Blackjack 8" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_8">Blackjack 8</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-13.jpeg" alt="PowerUP Roulette" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.powerup_roulette">PowerUP Roulette</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-9.jpeg" alt="Blackjack 2" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_2">Blackjack 2</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-5.jpeg" alt="Blackjack 5" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_5">Blackjack 5</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-10.jpeg" alt="32 Cards" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.32_cards">32 Cards</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-7.jpeg" alt="One Day Teen Patti" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.one_day_teen_patti">One Day Teen Patti</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-3.jpeg" alt="Blackjack Salon Priv&eacute;" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_salon_priv&eacute;">Blackjack Salon Priv&eacute;</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-8.jpeg" alt="Sweet Bonanza CandyLand" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.sweet_bonanza_candyland">Sweet Bonanza CandyLand</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenm" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-6.jpeg" alt="Blackjack 1" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_1">Blackjack 1</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                    <div data-testid="game-item" class="_gameItem_1jj5g_1 hiddenn" bis_skin_checked="1">
                      <div class="_inner_1jj5g_13" aria-hidden="true" bis_skin_checked="1">
                        <div class="lazyload-wrapper image _image_1jj5g_26" bis_skin_checked="1">
                          <div class="image__placeholder" bis_skin_checked="1">
                            <div class="_imagePlaceholder_1jj5g_124" bis_skin_checked="1">
                              <svg width="86" height="38" viewbox="0 0 86 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M26.8604 14L15.9672 38H10.8946L0 14H4.75714C5.15118 15.1223 5.56293 16.1889 6.04816 17.1273L4.39301 17.7863L5.87067 19.3898L7.112 20.7368L5.68103 21.7971L7.30731 23.0023L9.15314 24.3701L8.98206 24.6713L8.56128 25.4121L8.98206 26.153L12.1559 31.7408L13.4602 34.0372L14.7645 31.7408L17.9384 26.153L18.3591 25.4121L17.9384 24.6713L17.7673 24.3702L19.6132 23.0023L21.2393 21.7971L19.8084 20.7367L21.0497 19.3897L22.5274 17.7862L20.8221 17.1072C21.303 16.1738 21.7117 15.1143 22.103 14H26.8604ZM31.8937 18L39.4288 34H35.2305L33.8812 31H26.554L25.1982 34H21L28.4892 18H31.8937ZM32.082 27L30.2828 23H30.1694L28.3617 27H32.082ZM67.0224 21.7737C67.6744 22.9709 68 24.3746 68 25.9894C68 27.5876 67.6744 28.9897 67.0224 30.1915C66.3712 31.3947 65.4459 32.3309 64.2474 32.9973C63.0488 33.6653 61.6254 34 59.9773 34C58.3445 34 56.9328 33.6653 55.7411 32.9973C54.5503 32.3309 53.6288 31.3947 52.9776 30.1915C52.3256 28.9897 52 27.5876 52 25.9894C52 24.3746 52.3218 22.9709 52.9661 21.7737C53.6104 20.5782 54.5318 19.6509 55.7303 18.9905C56.929 18.3302 58.3445 18 59.9773 18C61.6254 18 63.0488 18.3302 64.2474 18.9905C65.4459 19.6509 66.3712 20.5782 67.0224 21.7737ZM64 25.9908C64 25.1498 63.853 24.4317 63.5578 23.8326C63.2636 23.2348 62.8181 22.7796 62.2205 22.467C61.6237 22.1556 60.8746 22 59.9752 22C58.6337 22 57.6358 22.3466 56.9818 23.0399C56.327 23.7344 56 24.7181 56 25.9908C56 26.8306 56.147 27.55 56.4413 28.1478C56.7364 28.7456 57.1735 29.2047 57.7543 29.5226C58.3353 29.8404 59.0757 30 59.9752 30C61.3168 30 62.3229 29.6469 62.9938 28.9404C63.6646 28.2341 64 27.2505 64 25.9908ZM45 30V18H41V34H53L50 30H45ZM79.608 28.8883L84 34H79.0641L74.738 29.0164L74 29V34H70V18H77.5576C77.7058 18 77.8341 18.0208 77.9769 18.0264C78.1492 18.0101 78.3234 18 78.5 18C81.5375 18 84 20.4624 84 23.5C84 26.158 82.1144 28.3756 79.608 28.8883ZM80 23.5C80 22.6716 79.3284 22 78.5 22H74V25H78.5C79.3284 25 80 24.3284 80 23.5Z" fill="#302FA0"></path>
                                <path d="M25.4302 3.34436C20.4796 6.60742 19.5705 11.8288 17.8181 13.8084C18.5032 10.5179 17.2651 8.75672 19.9466 5.84912C16.7799 7.95764 17.9136 10.8181 16.6577 13.6569L16.3764 13.4923C17.9479 9.06519 13.6782 3.40699 20.2267 0C12.605 2.32953 14.8079 7.44171 13.9972 12.0998L13.4602 11.7855L12.923 12.0998C12.1123 7.44172 14.3153 2.32953 6.69353 7.6e-06C13.242 3.40699 8.97243 9.06519 10.5439 13.4923L10.2138 13.6854C8.93756 10.8372 10.091 7.96473 6.91371 5.84913C9.59516 8.75672 8.35709 10.5179 9.04224 13.8084C7.28984 11.8287 6.38067 6.60743 1.43018 3.34437C4.94665 6.53883 5.61913 14.5004 8.27343 17.8557L6.97366 18.3732L9.34631 20.9479L8.20036 21.7971L11.1134 23.9559L10.2863 25.4121L13.4602 31L16.634 25.4121L15.807 23.9559L18.72 21.7971L17.574 20.9479L19.9466 18.3732L18.6007 17.8374C21.2433 14.4717 21.9199 6.53314 25.4302 3.34436ZM11.0135 21.3366L9.01954 17.5533L12.0462 19.7514L11.0135 21.3366ZM15.9068 21.3365L14.874 19.7514L17.9007 17.5533L15.9068 21.3365Z" fill="#FDA700"></path>
                              </svg>
                            </div>
                          </div>
                          <img src="./images/defaultDesktop-14.jpeg" alt="Blackjack 4" width="139" height="185" style="display: unset">
                        </div>
                      </div>
                      <div aria-hidden="true" class="_hoverBox_1jj5g_41" bis_skin_checked="1">
                        <p class="_name_1jj5g_59" data-translate="main.blackjack_4">Blackjack 4</p>
                        <svg width="24" height="24" viewbox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="m13.789 1.578 9.764 19.528A2 2 0 0 1 21.763 24H2.237a2 2 0 0 1-1.789-2.894l9.764-19.528a2 2 0 0 1 3.578 0z" fill="#ffffff"></path>
                        </svg>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <footer class="_footer_19h5r_1">
      <div class="_supports_19h5r_9" bis_skin_checked="1">
        
        <div class="_container_ux79l_1 _container_desktop_ux79l_9 hiddenn" bis_skin_checked="1">
          <div class="_desktop_content_ux79l_23" bis_skin_checked="1">
            <div class="_logo_ux79l_39" bis_skin_checked="1">
              <svg width="35" height="38" viewBox="0 0 35 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M27 14L16.0502 38H10.9512L0 14H4.78186C5.17795 15.1223 5.59184 16.1889 6.07959 17.1273L4.41583 17.7863L5.90118 19.3898L7.14896 20.7368L5.71055 21.7971L7.34529 23.0023L9.2007 24.3701L9.02873 24.6713L8.60577 25.4121L9.02873 26.153L12.2191 31.7408L13.5302 34.0372L14.8413 31.7408L18.0316 26.153L18.4545 25.4121L18.0316 24.6713L17.8597 24.3702L19.7151 23.0023L21.3497 21.7971L19.9114 20.7367L21.1591 19.3897L22.6445 17.7862L20.9303 17.1072C21.4137 16.1738 21.8246 15.1143 22.2179 14H27Z"
                  fill="#302FA0"></path>
                <path
                  d="M26 3.34436C20.8432 6.60742 19.8961 11.8288 18.0707 13.8084C18.7844 10.5179 17.4947 8.75671 20.2879 5.84912C16.9893 7.95764 18.1702 10.8181 16.862 13.6569L16.569 13.4922C18.206 9.06519 13.7584 3.40699 20.5797 0C12.6404 2.32953 14.9351 7.44171 14.0907 12.0998L13.5312 11.7855L12.9717 12.0998C12.1272 7.44172 14.422 2.32953 6.48266 7.6e-06C13.304 3.40699 8.85652 9.06519 10.4934 13.4923L10.1496 13.6854C8.82019 10.8372 10.0217 7.96473 6.71201 5.84913C9.50519 8.75672 8.21553 10.5179 8.92924 13.8084C7.10381 11.8287 6.15677 6.60743 1 3.34437C4.66299 6.53883 5.36349 14.5004 8.12839 17.8557L6.77446 18.3732L9.24598 20.9479L8.05227 21.7971L11.0867 23.9559L10.2251 25.4121L13.5312 31L16.8373 25.4121L15.9758 23.9559L19.0102 21.7971L17.8165 20.9479L20.2879 18.3732L18.886 17.8374C21.6387 14.4717 22.3435 6.53314 26 3.34436ZM10.9826 21.3366L8.90559 17.5533L12.0584 19.7514L10.9826 21.3366ZM16.0798 21.3365L15.004 19.7514L18.1568 17.5533L16.0798 21.3365Z"
                  fill="#FDA700"></path>
              </svg>
            </div>
            <div class="_column_ux79l_44" bis_skin_checked="1">
              <p data-translate="footer.operator_info" style="margin-bottom:0;">
                La información en el sitio es proporcionada por el operador
                del sitio - la empresa ValorBet N.V., registrada en la
                dirección: Palm Avenue 10, Rosebank, Sint Maarten.
              </p>
              <p data-translate="footer.license_info" style="margin:0;">La
                actividad de la empresa ValorBet N.V. está licenciada y
                regulada por IslandGames N.V. (número de licencia: No.
                1234/JAZ2021-567; válida hasta el 31 de diciembre de 2025) y
                por la legislación de Sint Maarten. </p>
                <p data-translate="footer.payment_info" style="margin:0;">Los pagos son
                procesados por Global Invest Solutions Ltd (número de
                registro: HE 654321, dirección: Ocean Drive 22, Mesa Verde,
                5678, Limassol, Chipre), una subsidiaria de ValorBet N.V.</p>
              <p data-translate="footer.copyright" class="footer_copyright">© 2021 - 2025. ValorCasino. Derechos Reservados</p>
            </div>
          </div>
        </div>
        <div class="_row_19h5r_14" bis_skin_checked="1">
          <div class="_container_1698s_1 _container_desktop_1698s_11" bis_skin_checked="1">
            <div bis_skin_checked="1">
              <h2 data-translate="footer.questions" class="_title_1698s_27">¿Tiene preguntas?</h2>
              <p class="_subtitle_1698s_18" data-translate="footer.contact_us">
                ¡Escríbanos o llámenos y responderemos de inmediato!
              </p>
            </div>
            <div class="_buttons_1698s_42" bis_skin_checked="1">
              <button type="button"
                class="_button_1qy1r_1 _button_color_blue_1qy1r_36 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_fixHeight_1qy1r_76 _button_1698s_21">
                Chat de ayuda
              </button>
            </div>
          </div>
        </div>

        <div class="politics-mobile__sidebar" style="margin-top: -5px;">
          <div class="politics-menu" style="margin: 0;">
            <div class="politics-menu__value" onclick="toggleDropdown(this)">
              <span class="politics-mobile-menu__selected" data-translate="footer.legal_info">Información Legal</span>
              <svg xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.8134 5.96384C4.78252 5.95145 4.75597 5.93196 4.72771 5.91322C4.70714 5.8996 4.68364 5.89306 4.66472 5.87602C4.65819 5.8701 4.65605 5.86167 4.64988 5.85544C4.64311 5.84872 4.63408 5.8461 4.62761 5.83889L0.127446 0.835804C0.0415059 0.740042 -0.000976568 0.61985 -0.000976562 0.500636C-0.000976557 0.363833 0.0546894 0.228007 0.164557 0.128336C0.369642 -0.056348 0.68606 -0.0397362 0.870637 0.165469L4.9992 4.75546L9.12777 0.165469C9.31234 -0.0397358 9.62876 -0.0563476 9.83385 0.128337C10.0389 0.314975 10.055 0.6306 9.87096 0.835804L5.3708 5.83889C5.36457 5.84579 5.35584 5.84836 5.34937 5.85483C5.34296 5.8613 5.34046 5.86985 5.33369 5.87602C5.31476 5.89306 5.29127 5.8996 5.2707 5.91322C5.24243 5.93196 5.21588 5.95145 5.185 5.96384C5.15497 5.976 5.12457 5.9807 5.0932 5.98675C5.06158 5.99285 5.03137 6 4.9992 6C4.96703 6 4.93682 5.99285 4.9052 5.98675C4.87383 5.9807 4.84343 5.976 4.8134 5.96384Z">
                </path>
              </svg>
            </div>
            <div class="politics-menu__dropdown-wrapper">
              <nav class="politics-menu__dropdown">
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/refund.php" data-translate="footer.refund_policy">Política de reembolso</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/cancellation.php" data-translate="footer.cancellation_policy">Política de cancelación</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/fairness.php" data-translate="footer.fairness_rng">Métodos de prueba de equidad y RNG </a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/user-auxiliary.php" data-translate="footer.account_payments_bonuses">Cuenta, Pagos y Bonos</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/kyc.php" data-translate="footer.kyc_policy">Política de Conozca a su Cliente (KYC)</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/dispute-resolution.php" data-translate="footer.dispute_resolution">Resolución de Disputas</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/responsible-gaming.php" data-translate="footer.responsible_gaming">Juego Responsable</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/responsible-gambling.php" data-translate="footer.responsible_gaming">Juego responsable</a>
              </nav>
            </div>
          </div>
        </div>

        <div class="politics-mobile__sidebar" style="margin-top: -5px;">
          <div class="politics-menu" style="margin: 0;">
            <div class="politics-menu__value" onclick="toggleDropdown(this)">
              <span class="politics-mobile-menu__selected" data-translate="footer.site_info">Información del Sitio</span>
              <svg xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M4.8134 5.96384C4.78252 5.95145 4.75597 5.93196 4.72771 5.91322C4.70714 5.8996 4.68364 5.89306 4.66472 5.87602C4.65819 5.8701 4.65605 5.86167 4.64988 5.85544C4.64311 5.84872 4.63408 5.8461 4.62761 5.83889L0.127446 0.835804C0.0415059 0.740042 -0.000976568 0.61985 -0.000976562 0.500636C-0.000976557 0.363833 0.0546894 0.228007 0.164557 0.128336C0.369642 -0.056348 0.68606 -0.0397362 0.870637 0.165469L4.9992 4.75546L9.12777 0.165469C9.31234 -0.0397358 9.62876 -0.0563476 9.83385 0.128337C10.0389 0.314975 10.055 0.6306 9.87096 0.835804L5.3708 5.83889C5.36457 5.84579 5.35584 5.84836 5.34937 5.85483C5.34296 5.8613 5.34046 5.86985 5.33369 5.87602C5.31476 5.89306 5.29127 5.8996 5.2707 5.91322C5.24243 5.93196 5.21588 5.95145 5.185 5.96384C5.15497 5.976 5.12457 5.9807 5.0932 5.98675C5.06158 5.99285 5.03137 6 4.9992 6C4.96703 6 4.93682 5.99285 4.9052 5.98675C4.87383 5.9807 4.84343 5.976 4.8134 5.96384Z">
                </path>
              </svg>
            </div>
            <div class="politics-menu__dropdown-wrapper">
              <nav class="politics-menu__dropdown">
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/about-us.php" data-translate="footer.about_us">Sobre nosotros</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/contact-us.php" data-translate="footer.contact">Contacto</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/privacy.php" data-translate="mainprivacy_policy.">Política de privacidad</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/terms-and-conditions.php" data-translate="footer.general_terms">Condiciones generales</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/aml.php" data-translate="footer.aml_policies">Políticas de AML</a>
                <a class="politics-menu__dropdown-item politics-menu__dropdown-item--mobile"
                  href="/politics/self-exclusion.php" data-translate="footer.self_exclusion">Autoexclusión</a>
              </nav>
            </div>
          </div>
        </div>

        <section class="language-selector language-selector--mob">
          <div class="language-dropdown">
            <div class="language-dropdown__control">
              <div class="language-dropdown__value-container">
                <div class="language-dropdown__single-value">
                  <div class="language-dropdown-flag">
                    <img id="current-flag" src="./images/ES.svg" alt="ES" />
                  </div>
                  <span data-translate="footer.spanish" id="current-language">Spanish</span>
                </div>
              </div>
              <div
                class="language-dropdown_arrow language-dropdown__indicator language-dropdown__dropdown-indicator css-15lsz6c-indicatorContainer"
                aria-hidden="true">
                <svg height="20" width="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false" class="css-8mmkcg">
                  <path
                    d="M4.516 7.548c0.436-0.446 1.043-0.481 1.576 0l3.908 3.747 3.908-3.747c0.533-0.481 1.141-0.446 1.574 0 0.436 0.445 0.408 1.197 0 1.615-0.406 0.418-4.695 4.502-4.695 4.502-0.217 0.223-0.502 0.335-0.787 0.335s-0.57-0.112-0.789-0.335c0 0-4.287-4.084-4.695-4.502s-0.436-1.17 0-1.615z">
                  </path>
                </svg>
              </div>
            </div>

            <div class="language-dropdown__menu" style="display: none;">
              <div class="language-dropdown__menu-list">
                <div data-translate="footer.spanish" class="language-dropdown__option" data-lang="es" data-flag="./images/ES.svg">
                  <div class="language-dropdown-flag">
                    <img src="./images/ES.svg" alt="ES" />
                  </div>
                  Spanish
                </div>
                <div data-translate="footer.portuguese" class="language-dropdown__option" data-lang="pt" data-flag="./images/port.svg">
                  <div class="language-dropdown-flag">
                    <img src="./images/port.svg" alt="PT" />
                  </div>
                  Portuguese
                </div>
                <div data-translate="footer.english" class="language-dropdown__option" data-lang="en" data-flag="./images/eng.svg">
                  <div class="language-dropdown-flag">
                    <img src="./images/eng.svg" alt="EN" />
                  </div>
                  English
                </div>
                <div data-translate="footer.french" class="language-dropdown__option" data-lang="fr" data-flag="./images/fr.svg">
                  <div class="language-dropdown-flag">
                    <img src="./images/fr.svg" alt="FR" />
                  </div>
                  Français
                </div>
                <div data-translate="footer.arabic" class="language-dropdown__option" data-lang="ar" data-flag="./images/ar.svg">
                  <div class="language-dropdown-flag">
                    <img src="./images/ar.svg" alt="AR" />
                  </div>
                  العربية
                </div>
              </div>
            </div>
          </div>
        </section>

        <div>
          <p  class="_register_xqcyt_200" data-translate="footer.operator_info" style="margin-bottom:0;">
                  La información en el sitio es proporcionada por el operador
                  del sitio - la empresa ValorBet N.V., registrada en la
                  dirección: Palm Avenue 10, Rosebank, Sint Maarten.
                </p>
                <p  class="_register_xqcyt_200" data-translate="footer.license_info" style="margin:0;">La
                  actividad de la empresa ValorBet N.V. está licenciada y
                  regulada por IslandGames N.V. (número de licencia: No.
                  1234/JAZ2021-567; válida hasta el 31 de diciembre de 2025) y
                  por la legislación de Sint Maarten. </p>
                  <p  class="_register_xqcyt_200" data-translate="footer.payment_info" style="margin:0;">Los pagos son
                  procesados por Global Invest Solutions Ltd (número de
                  registro: HE 654321, dirección: Ocean Drive 22, Mesa Verde,
                  5678, Limassol, Chipre), una subsidiaria de ValorBet N.V.</p>
        </div>
              <p  class="_copyright_xqcyt_201" data-translate="footer.copyright" class="footer_copyright">© 2021 - 2025. ValorCasino. Derechos Reservados</p>

      </div>
      <div class="_container_fsgsa_1" bis_skin_checked="1">
        <button title="Messages" type="button" class="_burger_fsgsa_5">
          <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M8.99988 10C11.7567 10 13.9999 7.75684 13.9999 5C13.9999 2.24316 11.7567 0 8.99988 0C6.24304 0 3.99988 2.24316 3.99988 5C3.99988 7.75684 6.24304 10 8.99988 10ZM8.99988 2C10.6542 2 11.9999 3.3457 11.9999 5C11.9999 6.6543 10.6542 8 8.99988 8C7.34558 8 5.99988 6.6543 5.99988 5C5.99988 3.3457 7.34558 2 8.99988 2ZM17.5179 17.8555C17.3563 17.9536 17.1771 18 17.0009 18C16.6625 18 16.3329 17.8286 16.1444 17.5181C16.058 17.3784 13.9091 14 8.99988 14C4.09021 14 1.94177 17.3784 1.85242 17.5225C1.66296 17.8281 1.33484 17.9976 0.998902 17.9976C0.821168 17.9976 0.64148 17.9502 0.478883 17.8506C0.00964445 17.563 -0.140746 16.9526 0.14441 16.4819C0.25525 16.2988 2.92957 12 8.99988 12C15.0697 12 17.744 16.2988 17.8553 16.4819C18.1415 16.9541 17.9906 17.5688 17.5179 17.8555Z"
              fill="white"></path>
          </svg>
        </button>
        <div class="_content_fsgsa_33" bis_skin_checked="1">
          <div class="_links_fsgsa_58" bis_skin_checked="1">
            <div class="_list_fsgsa_66" bis_skin_checked="1">
              <a aria-label="footer.menu.terms_and_conditions" href="/politics/terms-and-conditions.php"
                class="_link_fsgsa_58" data-translate="footer.general_terms">Condiciones
                generales</a><a aria-label="footer.menu.privacy_policy" href="/politics/privacy.php"
                class="_link_fsgsa_58" data-translate="footer.privacy_policy">Política de
                privacidad</a>
            </div>
            <div class="_list_fsgsa_66" bis_skin_checked="1">
              <a aria-label="footer.menu.responsible_gambling" href="/politics/responsible-gambling.php"
                class="_link_fsgsa_58" data-translate="footer.responsible_gaming">Juego
                responsable</a><a aria-label="footer.menu.about_us" href="/politics/about-us.php"
                class="_link_fsgsa_58" data-translate="footer.about_us">Sobre
                nosotros</a>
            </div>
            <div class="_list_fsgsa_66" bis_skin_checked="1">
              <a aria-label="footer.menu.contact_us" href="/politics/contact-us.php"
                class="_link_fsgsa_58" data-translate="footer.contact">Contacto</a><a aria-label="footer.menu.user_auxiliary"
                href="/politics/user-auxiliary.php" class="_link_fsgsa_58" data-translate="footer.account_payments_bonuses">Cuenta, Pagos y Bonos</a>
            </div>
                        <div class="_list_fsgsa_66" bis_skin_checked="1">
              <a aria-label="footer.menu.contact_us"  href="#"
                class="_link_fsgsa_58" data-translate="footer.affiliate_program">Programa de afiliados</a>
            </div>

            <section class="language-selector">
              <div class="language-dropdown">
                <div class="language-dropdown__control">
                  <div class="language-dropdown__value-container">
                    <div class="language-dropdown__single-value">
                      <div class="language-dropdown-flag">
                        <img id="current-flag" src="./images/ES.svg" alt="ES" />
                      </div>
                      <span data-translate="footer.spanish" id="current-language">Spanish</span>
                    </div>
                  </div>
                  <div
                    class="language-dropdown_arrow language-dropdown__indicator language-dropdown__dropdown-indicator css-15lsz6c-indicatorContainer"
                    aria-hidden="true">
                    <svg height="20" width="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false"
                      class="css-8mmkcg">
                      <path
                        d="M4.516 7.548c0.436-0.446 1.043-0.481 1.576 0l3.908 3.747 3.908-3.747c0.533-0.481 1.141-0.446 1.574 0 0.436 0.445 0.408 1.197 0 1.615-0.406 0.418-4.695 4.502-4.695 4.502-0.217 0.223-0.502 0.335-0.787 0.335s-0.57-0.112-0.789-0.335c0 0-4.287-4.084-4.695-4.502s-0.436-1.17 0-1.615z">
                      </path>
                    </svg>
                  </div>
                </div>

                <div class="language-dropdown__menu" style="display: none;">
                  <div class="language-dropdown__menu-list">
                    <div data-translate="footer.spanish" class="language-dropdown__option" data-lang="es" data-flag="./images/ES.svg">
                      <div class="language-dropdown-flag">
                        <img src="./images/ES.svg" alt="ES" />
                      </div>
                      Spanish
                    </div>
                    <div data-translate="footer.portuguese" class="language-dropdown__option" data-lang="pt" data-flag="./images/port.svg">
                      <div class="language-dropdown-flag">
                        <img src="./images/port.svg" alt="PT" />
                      </div>
                      Portuguese
                    </div>
                    <div data-translate="footer.english" class="language-dropdown__option" data-lang="en" data-flag="./images/eng.svg">
                      <div class="language-dropdown-flag">
                        <img src="./images/eng.svg" alt="EN" />
                      </div>
                      English
                    </div>
                    <div data-translate="footer.french" class="language-dropdown__option" data-lang="fr" data-flag="./images/fr.svg">
                      <div class="language-dropdown-flag">
                        <img src="./images/fr.svg" alt="FR" />
                      </div>
                      Français
                    </div>
                    <div data-translate="footer.arabic" class="language-dropdown__option" data-lang="ar" data-flag="./images/ar.svg">
                      <div class="language-dropdown-flag">
                        <img src="./images/ar.svg" alt="AR" />
                      </div>
                      العربية
                    </div>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>
      </div>
      <div class="_icons_19h5r_25" bis_skin_checked="1">
        <a class="_icons__item_19h5r_48 _rgc_19h5r_54" aria-label="Responsible gambling"></a><a
          class="_icons__item_19h5r_48 _bga_19h5r_60" aria-label="BeGambleAware"></a>
        <div class="_icons__item_19h5r_48 _eightplus_19h5r_72" bis_skin_checked="1"></div>
      </div>
      <div class="_payments_1lthh_1" bis_skin_checked="1">
        <div class="_list_sh5ev_1" bis_skin_checked="1">
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/5a63e45e802d93da96a501216c32e1409ff85913.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/49df77480268e86ebe8d4926ef927363744dd656.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/fab25d5e96189ea2e02263a42ff77ad5d9c35cfa.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/307b7b315b7597e6513c760304c7c94fe003e175.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/397f7bdb61cef2095cc631cf2f1badff6036e860.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/b3abc3a9d903672137e3c09cf194a9b0c7341144.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/7ae673bfb8eb4cb84305ad9d4901e2b5ba4485c6.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/acd43e216e7e4818318c2c839e31eacfa2418c51.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/72698307473a26ea6b15973628f47d7739283ba6.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/225105fd02855983336015e9e13f13f025f6f793.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/31daea7f9f718c7481cbdb8e1b6ea08c8cd7dba5.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/8244c9f24968b8fe98f63426d8f082fed00959f2.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/c597fb320ff5ae4add7a6549d0bce15970773f8f.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/ca9660e876672c8241e1368a3643b110e2233ae7.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/33efc8ead2414bf349ca93bcb62d3f7ad925ad88.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/d304cb2aa723a3394608a08c061c9623887ada13.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/9f64cca67d82187701c87f3b5c3e2850a7d8c74d.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/d7b49e386c53b17ff428693692b51fe1ecbd0a31.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/e4d15b3adf888755bb668d654f9b4e1a5163998c.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/f4eec821f87985da4db2328a6ec436bc0e293bb5.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/327e939dab2ca92f266aebd9b7243ce38582e17d.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/7b09034fc26fe518df1fd9278ed1886db78e441e.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
          <div class="_item_sh5ev_6 _item_desktop_sh5ev_20" bis_skin_checked="1">
            <picture>
              <source srcset="
                    ./images/32f92d3f521bb8967cb3dd3deea063897f43cb0c.webp
                  " type="image/webp" />
              <img loading="lazy" src="./images/c9ddf68a7b7be62b1b5142978d6bbf8626dade69.png" alt="Payment" width="78"
                height="56" />
            </picture>
          </div>
        </div>
      </div>
    </footer>

    <div class="_container_t4ztx_1 hiddenm" id="tabbar">
      <a href="./index.php" aria-current="page" class="_link_t4ztx_20"><svg
          xmlns="http://www.w3.org/2000/svg" width="22" height="20" fill="none">
          <path fill="#0F9658"
            d="m4.78 8 4.813 12L0 8zm7.627 12L22 8h-4.78zM11 8H6.22L11 20l4.78-12zM7.536 0H4.979L0 7h4.78zM11 7h4.78l-2.649-7H8.87l-2.65 7zm11 0-4.979-7h-2.557l2.756 7z" />
        </svg>
        <p data-translate="footer.home" class="_label_t4ztx_33">Inicio</p>
      </a>
      <a href="./games.php" class="_link_t4ztx_20">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20" fill="none">
          <path fill="grey"
            d="m22 16.319-.486-9.857C21.514 3.445 19.283 1 16.531 1c-1.637 0-3.09.865-3.998 2.2-1 .653-1.533.464-1.533.464s-.534.189-1.533-.464C8.558 1.865 7.107 1 5.469 1 2.717 1 .486 3.445.486 6.462L0 16.319l.011.006q-.01.12-.011.244C0 17.912.993 19 2.218 19c.64 0 1.216-.299 1.62-.775l.052.025 3.16-4.129s1.215-1.132 2.613-.999h2.674c1.398-.133 2.613.999 2.613.999l3.16 4.13.051-.026c.405.476.98.775 1.62.775C21.008 19 22 17.912 22 16.569q0-.124-.011-.244zM9 9H7v2H5V9H3V7h2V5h2v2h2zm7-4a1 1 0 1 1 0 2 1 1 0 0 1 0-2m-2 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2m2 2a1 1 0 1 1 0-2 1 1 0 0 1 0 2m2-2a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
        </svg>
        <p data-translate="footer.games" class="_label_t4ztx_33">Juegos</p>
      </a>
      <div class="_box_t4ztx_46">
        <button title="Aviator" type="button" class="_burger_t4ztx_79 games__href">
          <svg width="34" height="21" viewBox="0 0 34 21" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
              d="M3.94611 19.4512H3.95462L3.94611 19.457V19.4512ZM19.732 10.8001L24.0479 11.7084L24.8928 9.98677L24.0467 8.92107L19.732 10.8001ZM29.5308 7.37563C29.5166 7.37841 29.4999 7.38121 29.4856 7.38709C29.471 7.39299 29.4576 7.40183 29.4464 7.4131L29.5982 7.37253C29.5727 7.37253 29.5533 7.37563 29.5308 7.37563ZM29.9607 9.43148C30.6941 11.0624 30.4188 11.4994 29.0685 12.3464C27.7 13.208 25.5054 14.1266 23.918 14.7919C21.1063 15.9737 8.55256 19.785 5.76119 20.7442C4.77457 21.0852 4.77457 21.0833 4.62398 20.7504C4.56753 20.628 4.51318 20.5138 4.46705 20.4054C3.69141 20.8136 3.80009 20.2849 3.34381 19.9789L4.12126 19.6398L4.12734 19.6357C7.77693 19.7748 22.6539 13.751 22.6539 13.751C22.6539 13.751 22.6618 13.659 22.4548 13.659C22.0046 13.663 21.0723 13.6203 20.97 13.6714C19.137 14.6123 13.1924 16.9332 13.074 16.7455L12.2383 16.0719C12.2089 16.0491 12.1699 16.0436 12.1357 16.0577C11.6372 16.276 8.07837 17.7744 5.18865 18.9785C5.18865 18.9785 4.96126 18.5641 5.24055 18.4071C5.80551 18.0928 6.28181 17.8599 6.28181 17.8599C6.28181 17.8599 6.27575 17.7437 5.90204 17.7762C4.78065 17.8785 3.62099 16.9252 3.28947 16.67C2.89573 16.3637 2.61615 16.4513 2.3833 16.5721C2.37461 16.5766 2.36659 16.5823 2.35951 16.5891C2.31871 16.6282 2.31671 16.6937 2.35507 16.7353L4.63583 19.199C4.47229 19.2717 4.30286 19.3297 4.12947 19.3725L1.39548 16.7285C1.37756 16.7102 1.32929 16.704 1.29105 16.7223L0.015421 17.3231C0.015421 17.3231 -0.0662352 16.5963 0.146253 16.4414C0.29137 16.3352 1.2358 15.9146 1.88879 15.494C2.07578 15.3757 1.90882 15.18 1.90882 15.18L0.560333 15.8737C0.560333 15.8737 0.405822 15.5042 0.658999 15.3512C1.58946 14.7959 2.27463 14.353 2.91184 14.6247C3.66742 14.9471 5.75724 17.0639 6.86437 17.0354C7.28453 17.0246 12.1678 14.4958 12.6958 14.1592C13.0376 13.9449 12.8445 13.7513 12.8445 13.7513L9.74804 15.241C9.74804 15.241 9.57652 14.9003 9.74531 14.7981C9.95843 14.6693 10.2356 14.5203 10.5049 14.3757C10.5142 14.3707 10.5227 14.3643 10.53 14.3567C10.5688 14.3164 10.5683 14.2517 10.5288 14.2121C10.4183 14.1 10.3649 14.0591 10.2022 13.8817C10.1721 13.8495 10.1252 13.8404 10.0856 13.8591C8.9463 14.4122 7.08538 15.3596 7.08538 15.3596C7.08538 15.3596 7.10815 15.1772 7.1537 14.9777C7.26207 14.5014 7.25386 14.1632 7.58781 13.9836C8.41778 13.541 9.27568 13.0997 10.1621 12.6057C10.2984 12.5283 10.1521 12.0689 10.1521 12.0689L9.08504 12.5676C9.04678 12.5862 9.00064 12.5778 8.97029 12.5453L7.61391 11.1095L7.61178 11.0952C8.02981 10.5418 8.9964 10.0134 10.0134 10.1175C12.4086 10.3563 21.9603 11.7871 22.3158 11.832C22.4083 11.8444 22.6133 11.8933 22.6233 12.1098C22.6254 12.1526 22.6111 12.2201 22.5853 12.2792C22.4606 12.5323 22.4105 12.5753 22.4105 12.5753L23.7468 13.0814C23.7684 13.0912 23.7929 13.092 23.8151 13.0836C24.0862 12.9857 25.9732 12.2712 28.5897 10.6319C28.6281 10.6076 28.6774 10.6109 28.7124 10.64C28.8672 10.779 29.1161 11.0138 29.2388 11.0113C29.3957 11.0032 29.478 10.844 29.478 10.844C29.012 10.1726 28.1295 8.1434 27.8281 7.24524C27.788 7.12507 27.6754 7.04549 27.547 7.03526C27.3882 7.02504 27.1711 7.01483 27.0163 7.02906C26.353 7.09224 25.8266 7.18639 23.6442 8.06597C22.0486 8.7049 19.4081 10.1255 18.6804 10.5238C18.6443 10.5421 18.6282 10.5768 18.6282 10.6115C18.6283 10.6521 18.6508 10.6893 18.6865 10.7075C18.5699 10.6911 16.0217 10.4662 15.7889 10.4414C15.7822 10.4408 15.7755 10.4394 15.7691 10.4374C15.7159 10.4207 15.686 10.3632 15.7024 10.3089C15.7907 10.019 15.7324 9.58045 16.2351 9.21313C17.8405 8.02106 19.3198 7.31461 20.0392 7.23101C20.5034 7.17804 20.7967 7.44748 21.7654 7.78413C22.1309 7.91297 22.7942 7.71508 23.1457 7.61689C25.537 6.97207 26.0658 6.72896 27.4222 6.49203C27.9307 6.40222 28.5236 6.36568 28.9212 7.0495C29.0579 7.28613 29.4397 8.2744 29.9601 9.43148H29.9607ZM20.1233 8.38436C20.1233 8.38436 19.419 8.10747 19.0708 8.14958C17.9002 8.29175 16.6695 9.40267 16.6695 9.40267C16.6695 9.40267 17.7818 9.61359 17.9506 9.49095C18.5535 9.05022 20.1233 8.38436 20.1233 8.38436ZM21.6606 8.14958C21.1907 7.94951 20.4752 7.64323 20.3043 7.59026C20.0089 7.50045 19.6995 7.59646 19.5004 7.75749L20.678 8.23756C20.7101 8.25039 20.7365 8.27465 20.7525 8.30584C20.789 8.37725 20.7619 8.46538 20.6919 8.50266C20.4952 8.60704 20.252 8.74794 20.1455 8.79658C19.7314 8.98672 20.0993 9.21313 20.0993 9.21313L21.6828 8.38839C21.7711 8.34377 21.8054 8.2109 21.6606 8.14958ZM22.0088 12.1278L10.2684 10.4826C10.1339 10.4662 10.1239 10.6276 10.1982 10.648C10.2244 10.6499 20.7727 12.824 21.0316 12.8686C21.5626 12.9569 21.9238 12.5239 22.0786 12.291H22.079C22.1233 12.2257 22.0829 12.1402 22.0088 12.1278ZM33.8322 15.9564C33.7557 16.0759 33.5805 16.0722 33.5022 15.9546C33.1257 15.3915 32.1479 13.9232 32.0092 13.7147C31.7336 13.2935 31.4133 12.8785 31.2767 12.3901L30.3571 9.35065C30.4113 9.34828 30.4646 9.3359 30.5144 9.31409C30.5563 9.29396 30.5924 9.2695 30.6267 9.24131C30.8098 9.06912 31.001 8.83219 31.1641 8.61137L32.8704 11.6936C33.2448 12.5604 33.8926 14.9235 33.9809 15.2803V15.28C34.0356 15.4909 33.9688 15.7458 33.8322 15.9564ZM33.6813 15.1081C33.47 14.7154 31.8422 12.5806 31.7718 12.4917L31.7575 12.4731L31.7375 12.4855C31.7375 12.4855 31.5605 12.6051 31.7114 12.8438C32.2105 13.6358 33.5908 15.6811 33.6048 15.7015L33.6269 15.7337L33.647 15.7015C33.6552 15.6891 33.8422 15.4017 33.6813 15.1081ZM26.8175 0.816621C26.7601 0.588688 26.8254 0.30499 26.975 0.0789095C26.9873 0.0600443 27.003 0.0437498 27.0213 0.0309524C27.1002 -0.0241879 27.2078 -0.00367805 27.2619 0.0767357C27.7767 0.851743 28.2901 1.62766 28.8022 2.4045C29.0849 2.83778 29.4106 3.26085 29.6009 3.9137H29.6012L30.6091 7.15791C30.3122 7.15171 29.9847 7.14955 29.7755 7.16411C29.7346 7.16692 29.6945 7.17677 29.6568 7.19322C29.6012 7.2183 29.5524 7.25547 29.5132 7.29883L27.9279 4.47335C27.5469 3.58636 26.9097 1.17962 26.8175 0.816621ZM27.1104 1.00958C27.3293 1.41375 28.9844 3.59657 29.0542 3.6901L29.0685 3.70868L29.0891 3.6963V3.696C29.0912 3.69412 29.2712 3.57366 29.118 3.32897C28.6116 2.52064 27.2045 0.424855 27.1902 0.402247L27.1675 0.371265L27.1471 0.402247C27.1389 0.414628 26.9504 0.707002 27.1104 1.00958ZM31.3845 7.45584H31.3847C31.5654 7.49425 31.6124 7.62215 31.5365 7.78445C31.2876 8.30041 30.893 8.82042 30.474 9.18774C30.46 9.19838 30.4448 9.20742 30.4288 9.21468C30.3505 9.24564 30.2418 9.23388 30.2047 9.16233C29.9289 8.65104 29.6785 8.12595 29.4543 7.58901C29.4297 7.53111 29.4646 7.45584 29.5202 7.41713C29.5291 7.40856 29.5397 7.40193 29.5512 7.39762C29.5633 7.39389 29.5757 7.39204 29.5861 7.38987C29.6046 7.38802 29.6188 7.38802 29.6395 7.38802C30.1307 7.3589 30.9509 7.36478 31.3845 7.45584ZM29.7937 7.57168C29.7834 7.57353 29.7752 7.57756 29.767 7.58128C29.6638 7.62402 29.7093 7.70515 29.7093 7.70515L29.9601 8.24809L31.3353 7.6875C31.3353 7.6875 31.3289 7.6296 31.1565 7.59676C30.8316 7.53699 29.7937 7.57168 29.7937 7.57168ZM26.1186 7.37253C26.1186 7.37253 25.9328 8.15703 25.6405 9.06292L25.0989 8.36328L24.66 8.56738L25.4584 9.60026C25.2094 10.2971 24.9089 10.9902 24.5892 11.3987L25.3894 11.0271C25.5716 10.8543 25.7188 10.5049 25.8333 10.0862L26.2729 10.6551L26.7142 10.4498L25.969 9.48692C26.1302 8.61664 26.1763 7.66986 26.1186 7.37253Z">
            </path>
          </svg>
        </button>
        <button title="Hot" class="_hot_t4ztx_53">HOT!</button>
      </div>
      <a  href="./all_games.php?categorías=all_games" class="_link_t4ztx_20">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none">
          <path fill="grey"
            d="M18.88 9.77a7.96 7.96 0 0 0-1.332-3.208 8.1 8.1 0 0 0-1.957-1.956A7.96 7.96 0 0 0 11 3.154q-.709 0-1.383.12a7.96 7.96 0 0 0-3.208 1.332 8.1 8.1 0 0 0-1.957 1.956A7.96 7.96 0 0 0 3 11.154q0 .708.12 1.383a8 8 0 0 0 1.332 3.208A8.1 8.1 0 0 0 6.41 17.7 7.96 7.96 0 0 0 11 19.154q.709-.001 1.383-.12a7.96 7.96 0 0 0 3.208-1.333 8.1 8.1 0 0 0 1.957-1.956A7.96 7.96 0 0 0 19 11.154q0-.708-.12-1.384m-.616 0h-1.772a5.6 5.6 0 0 0-.632-1.52l1.253-1.253c.56.822.96 1.761 1.151 2.773m-2.206 1.384a5.04 5.04 0 0 1-.64 2.46 5.1 5.1 0 0 1-3.035 2.406 5.05 5.05 0 0 1-3.844-.449 5.1 5.1 0 0 1-2.405-3.034 5.04 5.04 0 0 1 .448-3.844 5.1 5.1 0 0 1 3.035-2.405 5.05 5.05 0 0 1 3.844.448 5.1 5.1 0 0 1 2.405 3.034c.125.44.192.904.192 1.384m-.902-6.113-1.252 1.252a5.6 5.6 0 0 0-1.52-.632V3.89a7.4 7.4 0 0 1 2.772 1.152m-5.54-1.152v1.772a5.6 5.6 0 0 0-1.52.632L6.844 5.041a7.4 7.4 0 0 1 2.773-1.152M4.888 6.997 6.14 8.25a5.6 5.6 0 0 0-.632 1.52H3.736a7.4 7.4 0 0 1 1.151-2.773m-1.151 5.54h1.772c.136.542.351 1.053.632 1.52L4.887 15.31a7.4 7.4 0 0 1-1.151-2.773m3.108 4.73 1.252-1.253c.468.28.979.495 1.52.632v1.772a7.4 7.4 0 0 1-2.772-1.151m5.54 1.151v-1.772a5.6 5.6 0 0 0 1.52-.632l1.252 1.253c-.821.56-1.76.959-2.773 1.151m4.729-3.108-1.253-1.252c.28-.468.496-.98.632-1.521h1.772a7.4 7.4 0 0 1-1.151 2.773m-4.266-5.367-1.21 1.21 1.21 1.21a.9.9 0 0 1 .884.224.918.918 0 1 1-1.298 1.298.9.9 0 0 1-.223-.885L11 11.79 9.79 13a.9.9 0 0 1-.223.885.918.918 0 0 1-1.298-1.298.9.9 0 0 1 .884-.223l1.21-1.21-1.21-1.21a.9.9 0 0 1-.884-.224.918.918 0 1 1 1.298-1.298c.24.24.305.579.223.885l1.21 1.21 1.21-1.21a.9.9 0 0 1 .223-.885.918.918 0 1 1 1.298 1.298.9.9 0 0 1-.884.223m5.931-6.721A10.93 10.93 0 0 0 11 0C8.062 0 5.3 1.144 3.222 3.222A10.93 10.93 0 0 0 0 11c0 2.938 1.144 5.7 3.222 7.778A10.93 10.93 0 0 0 11 22c2.938 0 5.7-1.144 7.778-3.222A10.93 10.93 0 0 0 22 11c0-2.938-1.144-5.7-3.222-7.778m-1.414 14.142A8.94 8.94 0 0 1 11 20a8.94 8.94 0 0 1-6.364-2.636A8.94 8.94 0 0 1 2 11c0-2.404.936-4.664 2.636-6.364A8.94 8.94 0 0 1 11 2c2.404 0 4.664.936 6.364 2.636A8.94 8.94 0 0 1 20 11a8.94 8.94 0 0 1-2.636 6.364" />
        </svg>
        <p data-translate="footer.casino" class="_label_t4ztx_33">Casino</p>
      </a><a  href="/all_games.php?categorías=live" class="_link_t4ztx_20"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none">
          <path fill="grey"
            d="M23.137 9.916 14.084.863a2.947 2.947 0 0 0-4.168 0L.863 9.916a2.947 2.947 0 0 0 0 4.168l9.053 9.053a2.947 2.947 0 0 0 4.168 0l9.053-9.053a2.947 2.947 0 0 0 0-4.168m-18.1 3.15a1.7 1.7 0 1 1-2.403-2.403 1.7 1.7 0 0 1 2.403 2.404m4.015-4.014a1.7 1.7 0 1 1-2.404-2.404 1.7 1.7 0 0 1 2.404 2.404m1.611-6.419a1.7 1.7 0 1 1 2.403 2.405 1.7 1.7 0 0 1-2.403-2.405m2.674 18.734a1.7 1.7 0 1 1-2.403-2.404 1.7 1.7 0 0 1 2.403 2.404m4.015-4.015a1.7 1.7 0 1 1-2.404-2.403 1.7 1.7 0 0 1 2.404 2.403m4.015-4.015a1.7 1.7 0 1 1-2.405-2.404 1.7 1.7 0 0 1 2.405 2.404" />
        </svg>
        <p data-translate="footer.live_games" class="_label_t4ztx_33">Juegos en vivo</p>
      </a><button title="Messages" type="button" class="_chat_t4ztx_124">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path
            d="M8.99988 10C11.7567 10 13.9999 7.75684 13.9999 5C13.9999 2.24316 11.7567 0 8.99988 0C6.24304 0 3.99988 2.24316 3.99988 5C3.99988 7.75684 6.24304 10 8.99988 10ZM8.99988 2C10.6542 2 11.9999 3.3457 11.9999 5C11.9999 6.6543 10.6542 8 8.99988 8C7.34558 8 5.99988 6.6543 5.99988 5C5.99988 3.3457 7.34558 2 8.99988 2ZM17.5179 17.8555C17.3563 17.9536 17.1771 18 17.0009 18C16.6625 18 16.3329 17.8286 16.1444 17.5181C16.058 17.3784 13.9091 14 8.99988 14C4.09021 14 1.94177 17.3784 1.85242 17.5225C1.66296 17.8281 1.33484 17.9976 0.998902 17.9976C0.821168 17.9976 0.64148 17.9502 0.478883 17.8506C0.00964445 17.563 -0.140746 16.9526 0.14441 16.4819C0.25525 16.2988 2.92957 12 8.99988 12C15.0697 12 17.744 16.2988 17.8553 16.4819C18.1415 16.9541 17.9906 17.5688 17.5179 17.8555Z"
            fill="white"></path>
        </svg>
      </button>
    </div>
  </div>

  <div class="_modal_15zx8_1">
    <div class="_overlay_hi522_1 _overlay--open_hi522_12" aria-hidden="true"></div>
    <div class="_container_15zx8_19 _container_heightAuto_15zx8_27">
      <div
        class="_container_10y1t_1 _login_uzguq_1 _login_desktop_uzguq_77 _container--lg_10y1t_15 _container_backgroundColor_white_10y1t_69">
        <div class="_head_10y1t_76 _head_color_yellow_10y1t_101 _head_desktop_10y1t_88">
          <svg width="144" height="130" viewBox="0 0 144 130" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.1">
              <path
                d="M111.628 17.8389L120.77 21.4838L106.194 37.3233L113.865 43.0161L95.2512 56.8294L98.4242 62.4229L72.1608 108.726L45.8971 62.4229L49.0701 56.8291L30.4563 43.0161L38.1275 37.3236L23.5508 21.4841L32.4245 17.946C29.8231 12.9085 27.6154 7.18264 25.5029 1.15791H0L58.4064 130H85.6008L144 1.15791H118.495C116.398 7.14004 114.206 12.8279 111.628 17.8389Z"
                fill="black"></path>
              <path
                d="M136.333 -56.0461C109.792 -38.5286 104.918 -10.498 95.5236 0.129364C99.1967 -17.5356 92.559 -26.9903 106.935 -42.5995C89.9579 -31.28 96.0359 -15.9238 89.303 -0.683891L87.7949 -1.56792C96.2198 -25.3343 73.3298 -55.7099 108.436 -74C67.5759 -61.4941 79.3861 -34.0497 75.04 -9.04321L72.1606 -10.7307L69.281 -9.04321C64.9349 -34.0497 76.7453 -61.4941 35.8844 -74C70.9911 -55.7099 48.1017 -25.3342 56.5263 -1.5679L54.7567 -0.530853C47.9148 -15.8212 54.0985 -31.242 37.0648 -42.5994C51.4402 -26.9902 44.8028 -17.5355 48.476 0.129387C39.0813 -10.4983 34.2072 -38.5285 7.66733 -56.046C26.5193 -38.8968 30.1245 3.84444 44.3543 21.857L37.3862 24.6353L50.1061 38.457L43.9626 43.0161L59.5797 54.6052L55.1454 62.4229L72.1607 92.4211L89.1759 62.4229L84.7419 54.6055L100.359 43.0161L94.2152 38.457L106.935 24.6352L99.7196 21.7587C113.887 3.69006 117.514 -38.9273 136.333 -56.0461ZM59.0439 40.5436L48.3543 20.2334L64.5806 32.0339L59.0439 40.5436ZM85.2772 40.5436L79.7404 32.0339L95.9667 20.2334L85.2772 40.5436Z"
                fill="black"></path>
            </g>
          </svg>
          <h3 data-translate="modal.access" class="_title_10y1t_114 _title_fontSize_medium_10y1t_131">
            Acceso
          </h3>
          <div data-cy="close-modal-button-login" class="_close_10y1t_164" aria-hidden="true"></div>
        </div>
        <div data-cy="login-modal" class="_body_uzguq_1">
          <form id="login-form" class="_form_uzguq_10" method="POST">
            <div class="_controls_uzguq_10">
              <div class="_row_uzguq_10">
                <div class="_root_1rq38_1 _root_ltr_1rq38_115">
                  <label data-translate="modal.email_phone" class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="username">Correo electrónico /
                    Teléfono</label>
                  <div class="_inputContent_1rq38_22">
                    <input  class="_input_1rq38_22" id="email" name="email"
                      placeholder="Correo electrónico o teléfono" data-translate="modal.email_placeholder" required>
                  </div>
                </div>
              </div>
              <div class="_row_uzguq_10">
                <div class="_root_1rq38_1">
                  <label data-translate="modal.password" class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="password">Contraseña</label>
                  <div class="_inputContent_1rq38_22">
                    <input  class="_input_1rq38_22 toggle-password-input" id="password" name="password"
                      placeholder="Contraseña" data-translate="modal.password_placeholder"  type="password" required>
                    <div class="_endIcon_1rq38_75">
                      <img class="toggle-password-input-svg" src="../images/uneach.svg" alt="">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="_buttons_uzguq_98">
              <button data-translate="modal.access" type="submit"
                class="_button_1qy1r_1 _button_color_yellow_1qy1r_33 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_fixHeight_1qy1r_76 _submit_uzguq_26"
                data-cy="login-submit">
                Acceso
              </button>
            </div>
          </form>

          <script>
            document.getElementById('login-form').addEventListener('submit', function(e) {
              e.preventDefault();

              // Получаем текущий язык из localStorage
              const currentLang = localStorage.getItem('selectedLanguage') || 'es';
              const formData = new FormData(this);

              // Добавляем язык в FormData
              formData.append('lang', currentLang);

              fetch('login.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  // Успешная авторизация (перевод берем из JSON)
                  showTranslatedNotification('success', data.message, currentLang);
                  setTimeout(() => {
                    window.location.href = 'account.php';
                  }, 2000);
                } else {
                  // Ошибка (перевод берем из JSON)
                  showTranslatedNotification('failure', data.message, currentLang);
                }
              })
              .catch(error => {
                console.error('Error:', error);
                // Получаем перевод для стандартной ошибки
                const errorMsg = getTranslation('main.login_error', currentLang);
                Notiflix.Notify.failure(errorMsg || 'Se ha producido un error al enviar los datos.');
              });
            });

            // Функция для показа переведенных уведомлений
            function showTranslatedNotification(type, messageKey, lang) {
              // Получаем полный перевод из нашего JSON
              const fullTranslation = getTranslation(messageKey, lang) || messageKey;
              Notiflix.Notify[type](fullTranslation);
            }

            // Функция для получения переводов (использует загруженные ранее translations)
            function getTranslation(key, lang) {
              try {
                const [page, section, subKey] = key.split('.');
                if (window.translations && window.translations[page] && window.translations[page][section]) {
                  return window.translations[page][section][subKey][lang];
                }
              } catch (e) {
                console.warn('Translation not found for key:', key);
              }
              return null;
            }
          </script>

        </div>
        <div class="_footer_10y1t_188 _footer_uzguq_53">
          <p data-translate="modal.no_account" class="_text_uzguq_59">No tengo una cuenta</p>
          <button data-translate="modal.signup" type="button" class="_link_uzguq_65 open_login-modal">
            Registrarse <img src="../images/arrow-right.svg" alt>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="_modal_15zx8_12 _modal--open_15zx8_31">
    <div class="_overlay_hi522_1 _overlay--open_hi522_12" aria-hidden="true"></div>
    <div class="_container_15zx8_19 _container_heightAuto_15zx8_27">
      <div
        class="_container_10y1t_1 _register_fyyq9_1 _register_desktop_fyyq9_1 _container--lg_10y1t_15 _container_backgroundColor_white_10y1t_69 _container_10y1t_1222">
        <div class="_head_10y1t_76 _head_color_green_10y1t_104 _head_desktop_10y1t_88">
          <svg width="144" height="130" viewBox="0 0 144 130" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.1">
              <path
                d="M111.628 17.8389L120.77 21.4838L106.194 37.3233L113.865 43.0161L95.2512 56.8294L98.4242 62.4229L72.1608 108.726L45.8971 62.4229L49.0701 56.8291L30.4563 43.0161L38.1275 37.3236L23.5508 21.4841L32.4245 17.946C29.8231 12.9085 27.6154 7.18264 25.5029 1.15791H0L58.4064 130H85.6008L144 1.15791H118.495C116.398 7.14004 114.206 12.8279 111.628 17.8389Z"
                fill="black"></path>
              <path
                d="M136.333 -56.0461C109.792 -38.5286 104.918 -10.498 95.5236 0.129364C99.1967 -17.5356 92.559 -26.9903 106.935 -42.5995C89.9579 -31.28 96.0359 -15.9238 89.303 -0.683891L87.7949 -1.56792C96.2198 -25.3343 73.3298 -55.7099 108.436 -74C67.5759 -61.4941 79.3861 -34.0497 75.04 -9.04321L72.1606 -10.7307L69.281 -9.04321C64.9349 -34.0497 76.7453 -61.4941 35.8844 -74C70.9911 -55.7099 48.1017 -25.3342 56.5263 -1.5679L54.7567 -0.530853C47.9148 -15.8212 54.0985 -31.242 37.0648 -42.5994C51.4402 -26.9902 44.8028 -17.5355 48.476 0.129387C39.0813 -10.4983 34.2072 -38.5285 7.66733 -56.046C26.5193 -38.8968 30.1245 3.84444 44.3543 21.857L37.3862 24.6353L50.1061 38.457L43.9626 43.0161L59.5797 54.6052L55.1454 62.4229L72.1607 92.4211L89.1759 62.4229L84.7419 54.6055L100.359 43.0161L94.2152 38.457L106.935 24.6352L99.7196 21.7587C113.887 3.69006 117.514 -38.9273 136.333 -56.0461ZM59.0439 40.5436L48.3543 20.2334L64.5806 32.0339L59.0439 40.5436ZM85.2772 40.5436L79.7404 32.0339L95.9667 20.2334L85.2772 40.5436Z"
                fill="black"></path>
            </g>
          </svg>
          <h3 data-translate="modal.account_creation" class="_title_10y1t_114 _title_fontSize_medium_10y1t_131">
            Creación de cuenta
          </h3>
          <div data-cy="close-modal-button" class="_close_10y1t_164 close-modal-button-register" aria-hidden="true">
          </div>
        </div>
        <div data-cy="register-modal" class="_body_fyyq9_1">
          <div class="_banner_1191g_1" style="
                background-image: url('https://static.valor.bet/banners/thumbs/desktop/qJRuZ6Q1qSBK1KH9Tj42XZQTeTxQBI5XrlhdkKUk.jpg');
              "></div>
          <div class="_box_fyyq9_16">
            <div>
              <div class="tabs__content">
                <div class="tabs-item">
                  <div class="_quick_118hh_1">
                    <form id="registration-form" class="form _form_118hh_20 _form_desktop_118hh_58" method="POST">
                      <div class="_root_1rq38_1 _root_ltr_1rq38_115 _control_118hh_1">
                        <label data-translate="modal.email" class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="email">Correo
                          electrónico</label>
                        <div class="_inputContent_1rq38_22">
                          <input  class="_input_1rq38_22" id="email" name="email" placeholder="Contraseña" data-translate="modal.registration_email_placeholder"
                            type="email" data-testid="email-input" value="" required="">
                        </div>
                      </div>
                      <div class="_root_1rq38_1 _control_118hh_1">
                        <label data-translate="modal.password" class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="password">Contraseña</label>
                        <div class="_inputContent_1rq38_22">
                          <input class="_input_1rq38_22 toggle-password-input" id="password" name="password"
                             placeholder="Contraseña" data-translate="modal.password_placeholder"  type="password" element="password" data-testid="password-input"
                            value="" required="">
                          <div class="_endIcon_1rq38_75">
                            <img class="toggle-password-input-svg" src="../images/uneach.svg" alt="">
                          </div>
                        </div>
                      </div>
                      <!-- Добавленный блок выбора страны -->
                      <div class="_root_1rq38_1 _control_118hh_1">
                        <label data-translate="modal.country" class="_label_1rq38_10 _label_color_mirage_1rq38_16" for="country">País</label>
                        <div class="_inputContent_1rq38_22">
                          <select class="_input_1rq38_22" id="country" name="country" required>
                            <option data-translate="modal.select_country" value="" disabled selected>Selecciona tu país</option>
                            <option data-translate="modal.argentina" value="Argentina">Argentina</option> <!-- Испанский -->
                            <option data-translate="modal.bolivia" value="Bolivia">Bolivia</option> <!-- Испанский -->
                            <option data-translate="modal.brazil" value="Brazil">Brasil</option> <!-- Португальский -->
                            <option data-translate="modal.chile" value="Chile">Chile</option> <!-- Испанский -->
                            <option data-translate="modal.colombia" value="Colombia">Colombia</option> <!-- Испанский -->
                            <option data-translate="modal.costa_rica" value="Costa Rica">Costa Rica</option> <!-- Испанский -->
                            <option data-translate="modal.cuba" value="Cuba">Cuba</option> <!-- Испанский -->
                            <option data-translate="modal.dominican_republic" value="Dominican Republic">República Dominicana</option> <!-- Испанский -->
                            <option data-translate="modal.ecuador" value="Ecuador">Ecuador</option> <!-- Испанский -->
                            <option data-translate="modal.el_salvador" value="El Salvador">El Salvador</option> <!-- Испанский -->
                            <option data-translate="modal.guatemala" value="Guatemala">Guatemala</option> <!-- Испанский -->
                            <option data-translate="modal.haiti" value="Haiti">Haïti</option> <!-- Французский и гаитянский креольский -->
                            <option data-translate="modal.honduras" value="Honduras">Honduras</option> <!-- Испанский -->
                            <option data-translate="modal.mexico" value="Mexico">México</option> <!-- Испанский -->
                            <option data-translate="modal.nicaragua" value="Nicaragua">Nicaragua</option> <!-- Испанский -->
                            <option data-translate="modal.panama" value="Panama">Panamá</option> <!-- Испанский -->
                            <option data-translate="modal.paraguay" value="Paraguay">Paraguay</option> <!-- Испанский -->
                            <option data-translate="modal.peru" value="Peru">Perú</option> <!-- Испанский -->
                            <option data-translate="modal.puerto_rico" value="Puerto Rico">Puerto Rico</option> <!-- Испанский -->
                            <option data-translate="modal.uruguay" value="Uruguay">Uruguay</option> <!-- Испанский -->
                            <option data-translate="modal.venezuela" value="Venezuela">Venezuela</option> <!-- Испанский -->
                          </select>
                        </div>
                      </div>
                      <!-- Конец добавленного блока -->
                      <button data-translate="modal.open_account" type="submit"
                        class="_button_1qy1r_1 _button_color_green_1qy1r_39 _button_border-radius_medium_1qy1r_23 _button_border_1qy1r_20 _button_flex_1qy1r_14 _button_fixHeight_1qy1r_76 form__submit_june"
                        data-testid="submit-button">
                        Abrir cuenta
                      </button>
                    </form>

                    <script>
                      document.getElementById('registration-form').addEventListener('submit', function (e) {
                        e.preventDefault(); // Отменяем стандартную отправку формы

                        // Собираем данные формы
                        const formData = new FormData(this);

                        // Отправляем данные на сервер через AJAX
                        fetch('register.php', {
                          method: 'POST',
                          body: formData
                        })
                          .then(response => response.json())
                          .then(data => {
                            if (data.success) {
                              // Успешная регистрация
                              Notiflix.Notify.success(data.message);
                              setTimeout(() => {
                                window.location.href = 'account.php'; // Перенаправление на account.php
                              }, 2000); // Задержка 2 секунды
                            } else {
                              // Ошибка
                              Notiflix.Notify.failure(data.message);
                            }
                          })
                          .catch(error => {
                            console.error('Ошибка:', error);
                            Notiflix.Notify.failure('Se ha producido un error al enviar los datos.');
                          });
                      });
                    </script>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div  class="_policy_fyyq9_20">
           <p data-translate="modal.terms_agreement"> Estoy familiarizado y de acuerdo con</p>
            <a data-translate="modal.terms_usage" href="/politics.php" aria-hidden="true">las condiciones del acuerdo de uso del
              sitio</a>
          </div>
        </div>
        <div class="_details_fyyq9_32">
          <div class="_item_fyyq9_42">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M0.17827 5.25726C0.455552 3.73352 3.05504 1.76172 3.71349 1.34357C4.37193 0.925232 5.06523 0.178406 5.06523 0.178406C5.06523 0.178406 5.75847 0.925232 6.41698 1.34357C7.07549 1.76172 9.6751 3.73352 9.95232 5.25726C10.2295 6.78082 9.25902 8.2746 7.56072 8.3642C7.56072 8.3642 6.41667 8.4848 5.5656 7.73505C5.83476 8.33624 6.25475 9.01617 6.91069 9.68933V9.98035H5.06535H3.2199V9.68933C3.87578 9.01617 4.29582 8.33612 4.56487 7.73492C3.71398 8.4848 2.56975 8.3642 2.56975 8.3642C0.871446 8.2746 -0.0990131 6.78082 0.17827 5.25726ZM16.6981 10.1588C16.6981 10.1588 18.8409 6.73206 21.9706 5.07941C18.8409 3.4267 16.6981 0 16.6981 0C16.6981 0 14.5553 3.4267 11.4258 5.07941C14.5553 6.73206 16.6981 10.1588 16.6981 10.1588ZM7.62724 12.8953C7.62724 12.8953 5.88365 12.7112 5.06535 14.1836C4.24687 12.7112 2.50334 12.8953 2.50334 12.8953C0.759874 12.9874 -0.236464 14.5209 0.0482037 16.085C0.332811 17.6493 3.00151 19.6736 3.67754 20.103C4.35356 20.5323 5.06535 21.2991 5.06535 21.2991C5.06535 21.2991 5.77702 20.5323 6.45293 20.103C7.12901 19.6736 9.79765 17.6493 10.0824 16.085C10.367 14.5209 9.37077 12.9874 7.62724 12.8953ZM19.4645 16.0617C19.1429 16.0617 18.8126 16.108 18.4946 16.1941C18.959 15.6904 19.2339 15.0082 19.2339 14.3726C19.2339 13.1655 18.0987 12.187 16.6982 12.187C15.2977 12.187 14.1625 13.1655 14.1625 14.3726C14.1625 15.0223 14.5331 15.7207 15.0937 16.2274C14.7164 16.0897 14.3168 16.012 13.932 16.012C12.5315 16.012 11.3963 16.9905 11.3963 18.1977C11.3963 19.4047 12.5315 20.3834 13.932 20.3834C14.771 20.3834 15.6802 20.0498 16.2745 19.5231C16 20.1816 15.5508 20.9444 14.8157 21.6987V22H16.727H18.6384V21.6987C17.8974 20.9384 17.4473 20.1698 17.1734 19.5077C17.7598 20.0602 18.641 20.433 19.4645 20.433C20.8648 20.433 22 19.4545 22 18.2473C22 17.0401 20.8648 16.0617 19.4645 16.0617Z"
                fill="#ffffff"></path>
            </svg>
            <p data-translate="modal.bets_bonus" class="_text_fyyq9_60">¡Obtén un 650% en apuestas!</p>
          </div>
          <div class="_item_fyyq9_42">
            <svg width="22" height="24" viewBox="0 0 22 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M7.12878 14H11.8713H12.5643H18L16.4096 4.00003H12.0643H11.5101H7.71899H7.16486H2.81958L1 14H6.43561H7.12878ZM15.9587 4.7117L17.2036 13.2882H12.5288L12.0999 4.7117H15.9587ZM7.67694 4.7117H11.5359L11.8456 13.2882H7.17072L7.67694 4.7117ZM3.25403 4.7117H7.11298L6.48755 13.2882H1.81274L3.25403 4.7117ZM3.77863 6.27872H6.13171L6.06866 6.93777L4.13306 11.2596H3.17792L5.14911 7.17526L5.15088 7.16H3.64331L3.77863 6.27872ZM10.7089 6.27872L10.72 6.93777L9.27087 11.2596H8.31586L9.82721 7.17526L9.82727 7.16H8.31976L8.35583 6.27872H10.7089ZM12.9962 7.16L12.933 6.27872H15.2861L15.3715 6.93777L14.4088 11.2596H13.4538L14.5054 7.17526L14.5038 7.16H12.9962ZM18 15H1V19L0 20V24H0.846069H18.1539H19V20L18 19V15ZM13 19H6V17H13V19ZM21 14L22 15V17H19V15L20 14L19.8539 6.70389C19.288 6.47653 18.8882 5.93546 18.8882 5.30057C18.8882 4.46127 19.5848 3.78098 20.4441 3.78098C21.3034 3.78098 22 4.46127 22 5.30057C22 5.93546 21.6003 6.47653 21.0343 6.70389L21 14ZM8.18549 3.00003H3.17792L3.4856 2.20975C4.00098 0.885956 5.38708 3.05176e-05 6.94275 3.05176e-05H9.44995H9.51794H12.0252C13.5809 3.05176e-05 14.9669 0.885956 15.4823 2.20975L15.79 3.00003H11.0002H9.51562H9.45239H8.18549Z"
                fill="#ffffff"></path>
            </svg>
            <p data-translate="modal.casino_bonus" class="_text_fyyq9_60">¡Obtén un 650% en el casino!</p>
          </div>
          <div class="_item_fyyq9_42">
            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                d="M22 3.07056V4.92938C22 6.62238 20.7173 7.99994 19.1407 7.99994H18.8402C18.8402 7.99994 16.8432 6.00653 16.829 5.95288H19.1407C19.6663 5.95288 20.0938 5.49377 20.0938 4.92938V3.07056C20.0938 2.50616 19.6663 2.04706 19.1407 2.04706H2.85925C2.33368 2.04706 1.90619 2.50616 1.90619 3.07056V4.92938C1.90619 5.49377 2.33368 5.95288 2.85925 5.95288L5.76776 5.85663C5.74927 5.9162 3.28735 7.99994 3.28735 7.99994H2.85925C1.28271 7.99994 0 6.62238 0 4.92938V3.07056C0 1.37756 1.28271 0 2.85925 0H19.1407C20.7173 0 22 1.37756 22 3.07056ZM10.7281 15.0524C10.0745 15.0524 9.4812 14.8761 9.05817 14.683L8.75635 15.6653C9.13873 15.8502 9.79242 16.0012 10.467 16.0264V16.8239H11.453V15.9677C12.6097 15.7996 13.2437 15.1616 13.2437 14.4142C13.2437 13.6586 12.7611 13.1968 11.5633 12.8442C10.7085 12.5755 10.3563 12.3991 10.3563 12.1221C10.3563 11.887 10.5673 11.652 11.2212 11.652C11.9457 11.652 12.4085 11.845 12.6705 11.9374L12.962 10.9885C12.63 10.8542 12.1772 10.7367 11.5031 10.7115V9.97266H10.5172V10.7703C9.44061 10.9466 8.81659 11.526 8.81659 12.2648C8.81659 13.0792 9.55145 13.4991 10.6279 13.8013C11.3726 14.0111 11.6942 14.2126 11.6942 14.5318C11.6942 14.8677 11.3021 15.0524 10.7281 15.0524ZM20.0147 12.5864C20.0539 12.8716 20.0748 13.1614 20.0748 13.4553C20.0748 13.7491 20.0539 14.0389 20.0147 14.3242C20.0052 14.3927 19.9811 14.4572 19.9695 14.5251C19.902 18.6624 15.8976 22 10.9611 22C5.98242 22 1.94635 18.6063 1.94635 14.4201C1.94635 14.2967 1.95898 14.176 1.96655 14.0543C1.94757 13.8557 1.92517 13.6578 1.92517 13.4553C1.92517 13.1614 1.94604 12.8716 1.98529 12.5864C2.18304 11.1483 2.86517 9.8313 3.89221 8.75256C4.32532 8.29767 4.81958 7.88519 5.36469 7.5238C6.65735 6.66675 8.23541 6.09747 9.9588 5.93243C10.3006 5.89966 10.6479 5.88226 11 5.88226C11.3521 5.88226 11.6993 5.89966 12.0411 5.93243C13.7645 6.09747 15.3427 6.66675 16.6353 7.5238C17.1804 7.88519 17.6747 8.29767 18.1077 8.75256C19.1348 9.8313 19.8169 11.1483 20.0147 12.5864ZM3.03375 14.3242C3.22058 15.5237 3.78912 16.6246 4.63367 17.5393L6.05933 16.3495C5.55823 15.7556 5.20679 15.0676 5.05133 14.3242H3.03375ZM6.11182 14.3242C6.24097 14.8307 6.48059 15.3036 6.80872 15.7242C7.19116 16.2145 7.6936 16.6339 8.28113 16.953C8.78522 17.2267 9.35175 17.4268 9.9588 17.5345C10.2948 17.5942 10.643 17.6259 11 17.6259C11.3569 17.6259 11.7051 17.5942 12.0411 17.5345C12.6482 17.4268 13.2148 17.2267 13.7188 16.953C14.3064 16.6339 14.8088 16.2145 15.1913 15.7242C15.5193 15.3036 15.759 14.8307 15.8881 14.3242C15.9597 14.0438 15.9976 13.7532 15.9976 13.4553C15.9976 13.1574 15.9596 12.8668 15.8881 12.5864C15.759 12.0798 15.5193 11.6071 15.1912 11.1864C14.8088 10.6961 14.3064 10.2768 13.7188 9.95764C13.2148 9.68384 12.6482 9.48383 12.0411 9.37604C11.7051 9.31641 11.3569 9.28479 11 9.28479C10.643 9.28479 10.2948 9.31641 9.9588 9.37604C9.35175 9.48383 8.78522 9.68384 8.28113 9.95764C7.6936 10.2768 7.19116 10.6961 6.80872 11.1864C6.48059 11.6071 6.24097 12.0798 6.11182 12.5864C6.04034 12.8668 6.00244 13.1574 6.00244 13.4553C6.00244 13.7532 6.04034 14.0438 6.11182 14.3242ZM16.9486 14.3242C16.7932 15.0676 16.4418 15.7556 15.9406 16.3495L17.3663 17.5393C18.2109 16.6246 18.7794 15.5237 18.9662 14.3242H16.9486ZM6.10608 18.7681C7.20221 19.4728 8.52136 19.9473 9.9588 20.1032V18.4195C9.06793 18.2897 8.24353 17.9965 7.53174 17.5783L6.10608 18.7681ZM12.0411 20.1032C13.4786 19.9473 14.7977 19.4728 15.8939 18.7681L14.4682 17.5783C13.7564 17.9965 12.9321 18.2897 12.0411 18.4195V20.1032ZM17.3663 9.37134L15.9406 10.561C16.4418 11.155 16.7932 11.843 16.9486 12.5864H18.9662C18.7794 11.3868 18.2109 10.286 17.3663 9.37134ZM12.0411 6.80737V8.49109C12.932 8.62085 13.7564 8.91406 14.4682 9.33228L15.8939 8.14258C14.7977 7.43774 13.4786 6.96332 12.0411 6.80737ZM6.10614 8.14258L7.5318 9.33228C8.24353 8.91406 9.06793 8.62085 9.9588 8.49109V6.80737C8.52136 6.96332 7.20221 7.43774 6.10614 8.14258ZM3.03375 12.5864H5.05133C5.20685 11.843 5.55823 11.155 6.05933 10.561L4.63367 9.37134C3.78912 10.286 3.22058 11.3868 3.03375 12.5864Z"
                fill="#ffffff"></path>
            </svg>
            <p data-translate="modal.cashback" class="_text_fyyq9_60">¡Obtén hasta un 30% de reembolso!</p>
          </div>
        </div>
        <div class="_footer_10y1t_188 _footer_fyyq9_73">
          <p data-translate="modal.already_account" class="_footer__text_fyyq9_79">¿Ya tiene cuenta?</p>
          <button data-translate="modal.login_action" type="button" class="_link_fyyq9_85 open_register-modal">
            Ingresar <img src="../images/arrow-right.svg" alt>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- jQuery и Owl Carousel -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  
  <script src="./js/script.js"></script>
  <script src="./js/toggle-password.js"></script>
  <script src="./js/slider.js"></script>
  <script src="./js/mobile-sidebar.js"></script>
  <script src="./js/swiper.js"></script>
  <script src="./js/href.js"></script>
  <script src="./js/language_toggle.js"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Обработка навигационных табов провайдеров
      document.querySelectorAll('.swiper-wrapper .navigation-tabs__item').forEach(item => {
        item.addEventListener('click', function (e) {
          e.preventDefault();

          // Получаем название из элемента
          const itemName = this.querySelector('.navigation-tabs__item-name').textContent.trim();
          const paramValue = itemName.toLowerCase().replace(/\s+/g, '_');

          // Определяем тип таба (proveedores или categorías)
          const isProviderTab = this.querySelector('.navigation-tabs__item-icon') !== null;
          const paramName = isProviderTab ? 'proveedores' : 'categorías';

          // Переходим на соответствующую страницу
          window.location.href = `/all_games.php?${paramName}=${encodeURIComponent(paramValue)}`;
        });
      });

      // Обработка игровых карточек
      const handleGameCardClick = (card) => {
        // Находим контейнер для отображения загрузки
        const container = card.querySelector('._container_tin0x_1, ._loading_r5hue_115');
        // Получаем название игры из атрибута alt или текста
        const gameName = card.querySelector('[alt]')?.alt ||
          card.querySelector('._name_r5hue_27')?.textContent;

        if (gameName) {
          // Показываем индикатор загрузки, если есть
          if (container) {
            container.style.display = 'flex';
          }

          // Через 3 секунды переходим на страницу игры
          setTimeout(() => {
            const formattedName = gameName.trim().toLowerCase().replace(/\s+/g, '_');
            window.location.href = `/all_games.php?game=${encodeURIComponent(formattedName)}`;
          }, 3000);
        }
      };

      // Обработчики для всех типов игровых карточек
      const gameCardSelectors = [
        '._card_r5hue_1',
        '._gameItem_jackpot_1jj5g_120',
        'div[aria-hidden="true"] .lazyload-wrapper'
      ];

      gameCardSelectors.forEach(selector => {
        document.querySelectorAll(selector).forEach(card => {
          card.addEventListener('click', function () {
            handleGameCardClick(this);
          });
        });
      });

      // Обработка UTM-метки ?game= при загрузке страницы
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('game')) {
        const casinoContent = document.querySelector('.casino__content');
        const blockLoader = document.querySelector('.block-loader');

        if (casinoContent && blockLoader) {
          casinoContent.style.display = 'none';
          blockLoader.style.display = 'flex';
        }
      }

      // Обработка live категории
      if (urlParams.has('categorías') && urlParams.get('categorías') === 'live') {
        document.querySelectorAll('._link_active_p19s5_42').forEach(el => {
          el.classList.remove('_link_active_p19s5_42');
        });

        const desktopLink = document.querySelector(
          'a._link_p19s5_1[href="/all_games.php?categorías=live"]:not(._link_mobile_p19s5_45)'
        );
        const mobileLink = document.querySelector(
          'a._link_mobile_p19s5_45[href="/all_games.php?categorías=live"]'
        );

        if (desktopLink) desktopLink.classList.add('_link_active_p19s5_42');
        if (mobileLink) mobileLink.classList.add('_link_active_p19s5_42');
      }
    });

    document.addEventListener("DOMContentLoaded", function () {
      // Используем делегирование событий для обработки кликов
      document.addEventListener('click', function (e) {
        // Проверяем клик по игровому элементу
        const gameItem = e.target.closest('[data-testid="game-item"], ._gameItem_1jj5g_1');

        if (gameItem) {
          // Находим элементы внутри игрового блока
          const loadingContainer = gameItem.querySelector('._imagePlaceholder_1jj5g_124, ._loading_r5hue_115');
          const gameNameElement = gameItem.querySelector('._name_1jj5g_59, [alt]');

          // Получаем название игры
          let gameName = '';
          if (gameNameElement) {
            gameName = gameNameElement.textContent || gameNameElement.getAttribute('alt');
          }

          if (gameName) {
            // Показываем индикатор загрузки, если есть
            if (loadingContainer) {
              loadingContainer.style.display = 'flex';
            }

            // Форматируем название игры
            const formattedName = gameName.trim()
              .toLowerCase()
              .replace(/\s+/g, '_')
              .replace(/[^\w-]/g, '');

            // Через 3 секунды переходим на страницу игры
            setTimeout(() => {
              window.location.href = `/all_games.php?game=${encodeURIComponent(formattedName)}`;
            }, 3000);
          } else {
            console.error('Не удалось определить название игры');
          }
        }
      });

      // Обработка UTM-метки ?game= при загрузке страницы
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('game')) {
        const casinoContent = document.querySelector('.casino__content');
        const blockLoader = document.querySelector('.block-loader');

        if (casinoContent && blockLoader) {
          casinoContent.style.display = 'none';
          blockLoader.style.display = 'flex';
        }
      }
    });
    
    // Инициализация Owl Carousel
    function initOwlCarousel() {
      if (typeof $ !== 'undefined' && $('.owl-carousel').length) {
        $('#bannerCarousel').owlCarousel({
          items: 1.5,
          loop: true,
          autoplay: false,
          autoplayTimeout: 5000,
          autoplayHoverPause: false,
          dots: true,
          nav: true,
          mouseDrag: true,
          touchDrag: true,
          pullDrag: false,
          freeDrag: false,
          margin: 15,
          stagePadding: 0,
          responsive: {
            0: {
              items: 1,
            },
            768: {
              items: 1.5,
              margin: 15
            }
          },
          onInitialized: function() {
            // Принудительно задаем высоту после инициализации
            const isMobile = window.innerWidth <= 768;
            const height = isMobile ? '250px' : '400px';
            
            $('#bannerCarousel').css({
              'height': height,
              'max-height': height,
              'min-height': height,
              'overflow': 'hidden'
            });
            
            $('#bannerCarousel .owl-item').css({
              'height': height,
              'max-height': height,
              'min-height': height,
              'overflow': 'hidden',
            });
            
            $('#bannerCarousel .owl-item img').css({
              'height': height,
              'max-height': height,
              'min-height': height,
              'object-fit': 'cover',
              'object-position': 'center center',
              'overflow': 'hidden'
            });
            
            $('#bannerCarousel .owl-stage-outer, #bannerCarousel .owl-stage').css({
              'height': height,
              'max-height': height,
              'overflow': 'hidden'
            });
          },
          onResized: function() {
            // При изменении размера также контролируем высоту
            setTimeout(function() {
              const isMobile = window.innerWidth <= 768;
              const height = isMobile ? '250px' : '400px';
              
              $('#bannerCarousel, #bannerCarousel .owl-item, #bannerCarousel .owl-item img').css({
                'height': height,
                'max-height': height,
                'min-height': height
              });
            }, 100);
          }
        });
      }
    }
    
    // Выполняем при загрузке страницы
    $(document).ready(function() {
      // Принудительно показываем слайдер перед инициализацией
      $('#bannerCarousel').show().css({
        'display': 'block !important',
        'visibility': 'visible !important',
        'opacity': '1 !important'
      });
      
      initOwlCarousel();
      
      // Дополнительная принудительная фиксация размеров
      setTimeout(function() {
        fixCarouselHeight();
      }, 1000);
      
      // Еще одна проверка через 3 секунды
      setTimeout(function() {
        $('#bannerCarousel').show();
        fixCarouselHeight();
      }, 3000);
    });
    
    // Запасной вариант для случая если jQuery загружается позже
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        // Принудительно показываем элемент
        const carousel = document.getElementById('bannerCarousel');
        if (carousel) {
          carousel.style.display = 'block';
          carousel.style.visibility = 'visible';
          carousel.style.opacity = '1';
        }
        initOwlCarousel();
      }, 500);
      
      setTimeout(fixCarouselHeight, 1500);
    });
    
    // Функция принудительной фиксации высоты
    function fixCarouselHeight() {
      const isMobile = window.innerWidth <= 768;
      const height = isMobile ? '100px' : '400px';
      
    }
    
    // Фиксация при изменении размера окна
    $(window).resize(function() {
      setTimeout(fixCarouselHeight, 100);
    });
  </script>
  
  <?php
        echo $script_tag;
  ?>

</body>

</html>