<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <?php require('Inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - CONFIRM BOOKING</title>
</head>

<body class="bg-light">
    <?php require('Inc/header.php') ?>

    <?php
    // Kiểm tra và lọc dữ liệu
    if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
        redirect('tour.php');
    } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('tour.php');
    }

    $data = filteration($_GET);
    $tour_res = select("SELECT * FROM `tours` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');
    if (mysqli_num_rows($tour_res) == 0) {
        redirect('tour.php');
    }
    $tour_data = mysqli_fetch_assoc($tour_res);

    $_SESSION['tour'] = [
        "id" => $tour_data['id'],
        "name" => $tour_data['name'],
        "price" => $tour_data['price'],
        "payment" => null,
        "available" => false,
    ];

    $user_res = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
    $user_data = mysqli_fetch_assoc($user_res);
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold">CONFIRM BOOKING</h2>
                <div style="font-size: 14px">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="tour.php" class="text-secondary text-decoration-none">TOURS</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4 ">
                <?php
                $tour_thumb = TOURS_IMG_PATH . "thumbnail.jpg";
                $thum_q = mysqli_query($con, "SELECT * FROM `tours_images` WHERE `tour_id`='$tour_data[id]' AND `thumb` = '1'");
                if (mysqli_num_rows($thum_q) > 0) {
                    $thum_res = mysqli_fetch_assoc($thum_q);
                    $tour_thumb = TOURS_IMG_PATH . $thum_res['image'];
                }
                echo <<<data
                    <div class="card p-3 shadow-sm rounded">
                    <img src="$tour_thumb" class="img-fluid rounded mb-3">
                    <h5>$tour_data[name]</h5>
                    <h6>$tour_data[price] VNĐ/Đêm</h6>
                    </div>
                data;
                ?>
            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <form action="pay_now1.php" method="POST" id="booking_form">
                            <h6 class="mb-3">BOOKING DETAILS</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input name="phonenum" type="number" value="<?php echo $user_data['phonenum'] ?>" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['address'] ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số lượng người</label>
                                    <input name="guests" id="guests" type="number" min="1" value="1" onchange="calculateTotal()" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <h6 class="mb-3 text-danger" id="total_amount">Total Amount: <?php echo $tour_data['price'] ?> VNĐ</h6>
                                    <input type="hidden" name="trans_amt" id="trans_amt" value="<?php echo $tour_data['price']; ?>">
                                </div>
                                <div class="col-12">
                                    <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-4">Pay Now</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const tourPrice = <?php echo $tour_data['price']; ?>;

            function calculateTotal() {
                const guests = document.getElementById('guests').value;
                const totalAmount = guests * tourPrice;
                document.getElementById('total_amount').innerText = `Total Amount: ${totalAmount} VNĐ`;
                document.getElementById('trans_amt').value = totalAmount;
            }
        </script>

    <?php require('Inc/footer.php') ?>
</body>

</html>
