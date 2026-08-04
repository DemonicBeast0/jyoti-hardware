<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$currentPage = basename($_SERVER["PHP_SELF"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Character Encoding -->
    <meta charset="UTF-8">

    <!-- Responsive -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title>Jyoti Suppliers | Quality Hardware in Nepal</title>

    <meta name="description"
        content="Jyoti Suppliers is Nepal's trusted source for hardware, construction materials, plumbing, electrical supplies, paints and industrial tools.">

    <meta name="keywords"
        content="Jyoti Suppliers, Hardware Nepal, Construction Materials, Power Tools, Plumbing, Electrical, Bosch, Makita, Ingco, Stanley">

    <meta name="author"
        content="Jyoti Suppliers">

    <meta name="robots"
        content="index, follow">

    <meta name="theme-color"
        content="#152F57">

    <!-- Favicon -->
    <link rel="icon"
        type="image/svg+xml"
        href="assets/images/logo/jyoti-suppliers.svg">

    <!-- Google Fonts -->
    <link rel="preconnect"
        href="https://fonts.googleapis.com">

    <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Swiper -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!-- AOS -->
    <link rel="stylesheet"
        href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <!-- Main CSS -->
    <link rel="stylesheet"
        href="assets/css/style.css?v=<?= filemtime(
            __DIR__ . "/../assets/css/style.css",
        ) ?>">

    <!-- Load header styles last so cached imports cannot override its layout. -->
    <link rel="stylesheet"
        href="assets/css/navbar.css?v=<?= filemtime(
            __DIR__ . "/../assets/css/navbar.css",
        ) ?>">

    <link rel="stylesheet"
        href="assets/css/hero.css?v=<?= filemtime(
            __DIR__ . "/../assets/css/hero.css",
        ) ?>">

    <link rel="stylesheet"
        href="assets/css/brands.css?v=<?= filemtime(
            __DIR__ . "/../assets/css/brands.css",
        ) ?>">

</head>

<body>
