<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .nav-link.active {
            background-color: #007bff;
            color: white !important;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container-fluid bg-dark text-light p-3 d-flex align-item-center justify-content-between sticky-top">
        <h3 class="mb-0 h-font">VENTURE</h3>
        <a class="btn btn-light btn-sm" href="logout.php">LOG OUT</a>
    </div>
    <div class="col-lg-2 bg-dark border-top border-3 border-secondary" id="dashboard-menu">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid flex-lg-column align-items-stretch">
                <h4 class="mt-2 text-light">ADMIN PANEL</h4>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminDropdown" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="adminDropdown">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard.php">Dashboard Hotel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard1.php">Dashboard Tour</a>
                        </li>
                        <li class="nav-item">
                            <button class="btn text-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#bookingLinksHotel">
                                <span>Bookings Hotel</span>
                                <span><i class="bi bi-caret-down-fill"></i></span>
                            </button>
                            <div class="collapse show px-3 small mb-1" id="bookingLinksHotel">
                                <ul class="nav nav-pills flex-column rounded border border-secondary">
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="new_bookings.php">New Bookings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="refund_bookings.php">Refund Bookings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="booking_records.php">Booking Records</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <button class="btn text-white px-3 w-100 shadow-none text-start d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#bookingLinksTour">
                                <span>Bookings Tour</span>
                                <span><i class="bi bi-caret-down-fill"></i></span>
                            </button>
                            <div class="collapse show px-3 small mb-1" id="bookingLinksTour">
                                <ul class="nav nav-pills flex-column rounded border border-secondary">
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="new_bookings1.php">New Bookings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="refund_bookings1.php">Refund Bookings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="booking_records1.php">Booking Records</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="users.php">User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="user_queries.php">User Queries</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="rate_review.php">Rating & Reviews Hotel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="rate_review1.php">Rating & Reviews Tour</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="rooms.php">Rooms</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="tours.php">Tours</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="features_facilities.php">Features & Facilities</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="carousel.php">Carousel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="setting.php">Setttings</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>

    <script>
        function setActive() {
            let navbar = document.getElementById('dashboard-menu');
            let a_tags = navbar.getElementsByTagName('a');
            let currentFileName = document.location.pathname.split('/').pop();

            for (let i = 0; i < a_tags.length; i++) {
                a_tags[i].classList.remove('active');
                let file = a_tags[i].href.split('/').pop();

                if (file === currentFileName) {
                    a_tags[i].classList.add('active');
                }
            }
        }
        setActive();
    </script>


</body>

</html>
