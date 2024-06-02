<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <?php require('Inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - Tour Details</title>

</head>

<body class="bg-light">
    <?php require('Inc/header.php') ?>

    <?php
    if (!isset($_GET['id'])) {
        redirect('tour.php');
    }
    $data = filteration($_GET);

    $tour_res = select("SELECT * FROM `tours` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

    if (mysqli_num_rows($tour_res) == 0) {
        redirect('tour.php');
    }

    $tour_data = mysqli_fetch_assoc($tour_res);
    ?>

    <!-- Our tour -->

    <div class="container">
        <div class="row">

            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold"><?php echo $tour_data['name'] ?></h2>
                <div style="font-size: 14px">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="tour.php" class="text-secondary text-decoration-none">TOURS</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4 ">
                <div id="tourCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $tour_img = TOURS_IMG_PATH . "thumbnail.jpg";
                        $img_q = mysqli_query($con, "SELECT * FROM `tours_images` WHERE `tour_id`='{$tour_data['id']}'");

                        if (mysqli_num_rows($img_q) > 0) {
                            $active_class = 'active'; // Khởi tạo active_class ở đây

                            while ($img_res = mysqli_fetch_assoc($img_q)) {
                                echo "
                                <div class='carousel-item $active_class'>
                                    <img src='" . TOURS_IMG_PATH . $img_res['image'] . "' class='d-block w-100 rounded' >
                                </div>
                                ";
                                $active_class = ''; // Loại bỏ active_class sau khi sử dụng nó cho mục đầu tiên
                            }
                        } else {
                            // Không có hình ảnh nào được tìm thấy, hiển thị hình ảnh mặc định
                            echo "
                                <div class='carousel-item active'>
                                <img src='$tour_img' class='d-block w-100' >
                                </div>
                                ";
                        }
                        ?>

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#tourCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#tourCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <?php
                        echo <<<price
                                                <h4>$tour_data[price] VNĐ / Người</h4>
                                price;
                            $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review1`
                            WHERE `tour_id`='$tour_data[id]' ORDER BY `sr_no` DESC LIMIT 20";
            
                            $rating_res = mysqli_query($con,$rating_q);
                            $rating_fectch = mysqli_fetch_assoc($rating_res);
            
                            $rating_data = "";
            
                            if($rating_fectch['avg_rating']!=NULL)
                            {
                                for($i = 1 ;$i <= $rating_fectch['avg_rating']; $i++)
                                {
                                    $rating_data .="<i class='bi bi-star-fill text-warning'></i>";
                                }
                            }
                              

                        echo <<<rating
                                                <div class="mb-3">
                                                   $rating_data
                                                </div>
                                rating;
                        if(!$settings_r['shutdown'])
                            {
                                $login =0;
                                if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                                    $login=1;
                                }
                                echo <<<book
                                            <button onclick='checkLoginToBook1($login,$tour_data[id])' class="btn w-100 text-white custom-bg shadow-none mb-1">Đặt Ngay</button>
                                        book;
                                
                            }

                        ?>
                    </div>
                </div>

            </div>
            <div class="col-12 mt-4 px-4">
                <div class="mb-5">
                    <h5>Description</h5>
                    <p>
                        <?php
                        echo $tour_data['description']
                        ?>
                    </p>
                </div>
                <div>
                    <h5 class="mb-3">Review & Rating</h5>
                    <?php
                        // Truy vấn để lấy đánh giá cùng với thông tin người dùng và phòng
                        $review_q = "SELECT rr.*, uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review1` rr 
                            INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                            INNER JOIN `tours` r ON rr.tour_id = r.id
                            WHERE rr.tour_id = '$tour_data[id]'
                            ORDER BY `sr_no` DESC LIMIT 15";

                        // Thực hiện truy vấn
                        $review_res = mysqli_query($con, $review_q);

                        // Định nghĩa đường dẫn hình ảnh
                        $img_path = USERS_IMG_PATH;

                        // Kiểm tra xem có đánh giá nào không
                        if (mysqli_num_rows($review_res) == 0) {
                            echo 'Chưa có đánh giá nào!';
                        } else {
                            // Lấy và hiển thị từng đánh giá
                            while ($row = mysqli_fetch_assoc($review_res)) 
                            {
                                $stars = "";
                                for ($i = 0; $i < $row['rating']; $i++) {
                                    $stars .= "<i class='bi bi-star-fill text-warning'></i>";
                                }
                                echo<<<reviews
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="$img_path$row[profile]" class="rounded-circle" width="30px">
                                            <h6 class="m-0 ms-2">$row[uname]</h6>
                                        </div>
                                        <p class="mb-1">
                                            {$row['review']}
                                        </p>
                                        <div>
                                            $stars
                                        </div>
                                    </div>
                                reviews;
                            }
                        }
                    ?>
                </div>
            </div>
        </div>

        <?php require('Inc/footer.php') ?>

</body>

</html>