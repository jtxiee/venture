<?php
ob_start(); // Bắt đầu bộ đệm đầu ra

require('Admin/Inc1/db_config.php');
require('Admin/Inc1/essentials.php');
require('Inc/vnpay_php/config.php');

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
    $ORDER_ID = "ORD_" . $_SESSION['uId'] . random_int(1111, 9999999);

    // Lấy thông tin cần thiết từ POST data
    $frm_data = filteration($_POST);
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $_SESSION['room']['payment'];
    $uName = $_SESSION['uName']; // Lấy tên từ session

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
            // Tạo URL thanh toán VNPay
            $vnp_TxnRef = $ORDER_ID;
            $vnp_OrderInfo = 'Thanh toán đơn hàng của  ' . $uName; // Thêm tên vào thông tin thanh toán
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $TXN_AMOUNT * 100; // Số tiền tính bằng VND (nhân với 100)
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; 

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";

            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }

            header("Location: " . $vnp_Url);
            exit;
        } else {
            ob_end_clean();
            header("Location: error.php");
            exit;
        }
    } else {
        ob_end_clean();
        header("Location: error.php");
        exit;
    }
} else {
    ob_end_clean();
    header("Location: error.php");
    exit;
}

ob_end_flush(); // Gửi tất cả đầu ra đã đệm (nếu có) và tắt bộ đệm

?>
