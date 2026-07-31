<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-xl fixed-top">

    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand" href="index.php">

            <img src="assets/images/logo/logo.png" alt="Hardware Logo">

        </a>

        <!-- Mobile Toggle -->

        <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainMenu">

            <i class="fas fa-bars"></i>

        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">

                    <a class="nav-link <?=($currentPage=='index.php')?'active':'';?>"
                        href="index.php">

                        Home

                    </a>

                </li>

                <!-- Products -->

                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle"

                        href="#"

                        role="button"

                        data-bs-toggle="dropdown">

                        Products

                    </a>

                    <ul class="dropdown-menu shadow-lg border-0">

                        <li><a class="dropdown-item" href="#">Power Tools</a></li>

                        <li><a class="dropdown-item" href="#">Hand Tools</a></li>

                        <li><a class="dropdown-item" href="#">Electrical</a></li>

                        <li><a class="dropdown-item" href="#">Plumbing</a></li>

                        <li><a class="dropdown-item" href="#">Paints</a></li>

                        <li><a class="dropdown-item" href="#">Safety Equipment</a></li>

                        <li><a class="dropdown-item" href="#">Fasteners</a></li>

                        <li><a class="dropdown-item" href="#">Building Materials</a></li>

                    </ul>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="brands.php">

                        Brands

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="authorized-dealers.php">

                        Authorized Dealer

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="trusted-dealers.php">

                        Trusted Dealer

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="about.php">

                        About

                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="contact.php">

                        Contact

                    </a>

                </li>

            </ul>

            <!-- Right Side -->

            <div class="d-flex align-items-center">

                <a href="#" class="search-btn me-3">

                    <i class="fas fa-search"></i>

                </a>

                <a href="tel:+9779800000000" class="phone-number me-3">

                    <i class="fas fa-phone"></i>

                    +977 9800000000

                </a>

                <a href="#" class="quote-btn">

                    Request Quote

                </a>

            </div>

        </div>

    </div>

</nav>