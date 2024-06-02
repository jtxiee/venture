<?php
require('../Admin/Inc1/essentials.php');
require('../Admin/Inc1/db_config.php');

session_start();

if (isset($_GET['fetch_tours'])) {
    $output = "";

    // Fetching settings table to check if the website is shutdown
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
    $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

    // Prepare query to fetch tours with optional name search
    $query = "SELECT * FROM `tours` WHERE `status`=1 AND `removed`=0";
    if (isset($_GET['name_search']) && !empty($_GET['name_search'])) {
        $name_search = '%' . mysqli_real_escape_string($con, $_GET['name_search']) . '%';
        $query .= " AND `name` LIKE '$name_search'";
    }

    $tour_res = mysqli_query($con, $query);

    while ($tour_data = mysqli_fetch_assoc($tour_res)) {
        // Get thumbnail image of the tour
        $tour_thumb = TOURS_IMG_PATH . "thumbnail.jpg";
        $thum_q = mysqli_query($con, "SELECT * FROM `tours_images` 
                WHERE `tour_id`='$tour_data[id]' AND `thumb` = '1'");

        if (mysqli_num_rows($thum_q) > 0) {
            $thum_res = mysqli_fetch_assoc($thum_q);
            $tour_thumb = TOURS_IMG_PATH . $thum_res['image'];
        }

        $book_btn = "";

        if (!$settings_r['shutdown']) {
            $login = 0;
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $login = 1;
            }
            $book_btn = " <button onclick='checkLoginToBook1($login,$tour_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Đặt Ngay</button>";
        }

        // Print tour card
        $output .= "
        <div class='card mb-4 border-0 shadow tour-item'>
            <div class='row g-0'>
                <div class='col-md-6'>
                    <img src='$tour_thumb' class='img-fluid rounded'>
                </div>
        
                <div class='col-md-6'>
                    <div class='card-body'>
                        <h5 class='card-title text-center tour-name'>$tour_data[name]</h5>
                        <h6 class='card-subtitle mb-3 text-center'>Giá: $tour_data[price] VNĐ</h6>
                        <div class='d-grid gap-2'>
                            $book_btn
                            <a href='tour_details.php?id=$tour_data[id]' class='btn btn-sm btn-outline-dark shadow-none'>Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }

    if (mysqli_num_rows($tour_res) > 0) {
        echo $output;
    } else {
        echo "<h3 class='text-center text-danger'>No tour to show!</h3>";
    }
}
?>
