<?php

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
if(isset($_POST['pay_now'])) {
    // Tạo ORDER_ID mới
    $ORDER_ID = "ORD_".$_SESSION['uId'].random_int(1111,9999999);

    // Lấy thông tin cần thiết từ POST data
    $frm_data = filteration($_POST);
    $CUST_ID = $_SESSION['uId'];
    $TXN_AMOUNT = $frm_data['trans_amt'];
    $guests = $frm_data['guests']; // Số lượng người

    // Thêm thông tin đặt tour vào cơ sở dữ liệu
    $query1 = "INSERT INTO `booking_order1`(`user_id`, `tour_id`, `order_id`,`guests`,`trans_amt`) VALUES (?,?,?,?,?)";
    $booking_params = [$CUST_ID, $_SESSION['tour']['id'], $ORDER_ID,$guests,$TXN_AMOUNT];
    $result1 = insert($query1, $booking_params, 'issss');

    // Kiểm tra xem câu lệnh SQL đã thực thi thành công hay không
    if($result1) {
        $booking_id = mysqli_insert_id($con);

        $query2 = "INSERT INTO `booking_details1`(`booking_id`, `tour_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
        $details_params = [$booking_id, $_SESSION['tour']['name'], $_SESSION['tour']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']];
        $result2 = insert($query2, $details_params, 'issssss');

        // Kiểm tra xem câu lệnh SQL đã thực thi thành công hay không
        if($result2) {
            // Tạo URL thanh toán VNPay
            $vnp_TxnRef = $ORDER_ID;
            $vnp_OrderInfo = 'Thanh toán đơn hàng của ' . $frm_data['name']; // Thêm tên vào thông tin thanh toán
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
            // Nếu câu lệnh SQL thất bại, chuyển hướng người dùng đến trang lỗi
            header("Location: error.php");
            exit;
        }
    } else {
        // Nếu câu lệnh SQL thất bại, chuyển hướng người dùng đến trang lỗi
        header("Location: error.php");
        exit;
    }
} else {
    // Nếu không có yêu cầu "pay_now", chuyển hướng người dùng đến trang lỗi
    header("Location: error.php");
    exit;
}
?>
