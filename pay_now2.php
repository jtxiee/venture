<?php
ob_start(); // Bắt đầu bộ đệm đầu ra

require('Admin/Inc1/db_config.php');
require('Admin/Inc1/essentials.php');
require('Inc/vnpay_php/index.php');

// Kiểm tra nếu phiên đã bắt đầu trước khi gọi session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra nếu người dùng đã đăng nhập
if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php'); 
}

// Kiểm tra nếu có yêu cầu "pay_now"
if (isset($_POST['pay_now'])) {
    // Tạo ORDER_ID mới
    $ORDER_ID = "ORD_".$_SESSION['uId'].random_int(1111,9999999);

    // Lấy thông tin cần thiết từ POST data
    $frm_data = filteration($_POST);
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $_SESSION['room']['payment'];

    // Thêm thông tin đặt phòng vào cơ sở dữ liệu
    $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `order_id`, `trans_amt`) VALUES (?,?,?,?,?,?)";
    $booking_params = [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout'], $ORDER_ID, $TXN_AMOUNT];
    $result1 = insert($query1, $booking_params, 'isssss');

    // Kiểm tra xem câu lệnh SQL đã thực thi thành công hay không
    if ($result1) {
        $booking_id = mysqli_insert_id($con);

        $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
        $details_params = [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']];
        $result2 = insert($query2, $details_params, 'issssss');

        // Kiểm tra xem câu lệnh SQL đã thực thi thành công hay không
        if ($result2) {
            // Chuyển hướng người dùng đến trang index.php của vnpay_php nếu quá trình đặt phòng thành công
            ob_end_clean();
            header("Location: Inc/vnpay_php/index.php");
            exit;
        } else {
            // Nếu câu lệnh SQL thất bại, chuyển hướng người dùng đến trang lỗi
            ob_end_clean();
            header("Location: error.php");
            exit;
        }
    } else {
        // Nếu câu lệnh SQL thất bại, chuyển hướng người dùng đến trang lỗi
        ob_end_clean();
        header("Location: error.php");
        exit;
    }
} else {
    // Nếu không có yêu cầu "pay_now", chuyển hướng người dùng đến trang lỗi
    ob_end_clean();
    header("Location: error.php");
    exit;
}

ob_end_flush(); // Gửi tất cả đầu ra đã đệm (nếu có) và tắt bộ đệm
?>
