<?php
require('../Inc1/essentials.php');
require('../Inc1/db_config.php');
adminLogin();

if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);
    // Thực hiện truy vấn SQL
    $query = "SELECT bo.*, bd.* FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)
    AND (bo.booking_status = ? AND bo.refund = ?) ORDER BY bo.booking_id ASC";

    // $res = mysqli_query($con, $query);
    $res = select($query,["%$frm_data[search]%","%$frm_data[search]%","%$frm_data[search]%","cancelled",0],'sssss');

    // Kiểm tra xem có kết quả trả về hay không
        $i = 1;
        $table_data = "";
        
        if(mysqli_num_rows($res)==0)
        {
            echo"<b>No Data Found!</b>";
            exit();
        }

        // Duyệt qua các dòng dữ liệu
        while($data = mysqli_fetch_assoc($res))
        {
            // Chuẩn bị dữ liệu để hiển thị
            $date = date("d-m-Y", strtotime($data['datetime']));
            $checkin = date("d-m-Y", strtotime($data['check_in']));
            $checkout = date("d-m-Y", strtotime($data['check_out']));


            // Thêm dữ liệu vào chuỗi HTML
            $table_data .= "
                <tr>
                    <td>$i</td>
                    <td>
                        <span class='badge bg-primary'>
                            Order ID : {$data['order_id']}
                        </span>
                        <br>
                        <b>Name :</b> {$data['user_name']} <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Phone No :</b> {$data['phonenum']} <!-- Sửa đóng thẻ b -->
                    </td>
                    <td>
                        <b>Room :</b> {$data['room_name']} <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Check in :</b> $checkin <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Check out :</b> $checkout <!-- Sửa đóng thẻ b -->
                        <br> 
                        <b>Date :</b> $date <!-- Sửa đóng thẻ b -->
                    </td>
                    <td>
                        <b>{$data['trans_amt']} VNĐ</b>
                    </td>
                    <td>
                        <button type='button' onclick='refund_booking($data[booking_id])' class='btn btn-success btn-sm fw-bold shadow-none'>
                        <i class='bi bi-cash'></i> Refund
                        </button>
                    </td>
                </tr>
            ";
            $i++;
        }
        echo $table_data;
}


if (isset($_POST['refund_booking']))
{
    $frm_data = filteration($_POST);
  
    $query = "UPDATE `booking_order` SET `refund`=? WHERE `booking_id` = ?";
    $values = [1,$frm_data['booking_id']];
    $res = update($query,$values,'ii');

    echo $res;
}

?>




