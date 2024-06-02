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
    AND (bo.booking_status = ? AND bo.arrival = ?) ORDER BY bo.booking_id ASC";

    // $res = mysqli_query($con, $query);
    $res = select($query,["%$frm_data[search]%","%$frm_data[search]%","%$frm_data[search]%","booker",0],'sssss');

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
                        <b>Price :</b> {$data['price']} VNĐ <!-- Sửa đóng thẻ b -->
                    </td>
                    <td>
                        <b>Check in :</b> $checkin <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Check out :</b> $checkout <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Pail :</b> {$data['trans_amt']} VNĐ <!-- Sửa đóng thẻ b -->
                        <br>
                        <b>Date :</b> $date <!-- Sửa đóng thẻ b -->
                    </td>
                    <td>
                        <button type='button' onclick='assign_room($data[booking_id])' class='btn text-white btn-sm fw-bold custom-bg shadow-none' data-bs-toggle='modal' data-bs-target='#assign-room'>
                            <i class='bi bi-check2-square'></i> Assign Room
                        </button>
                        <br>
                        <button type='button' onclick='cancel_booking($data[booking_id])' class='mt-2 btn btn-outline-danger btn-sm fw-bold shadow-none'>
                        <i class='bi bi-trash'></i> Cancel Booking
                        </button>
                    </td>
                </tr>
            ";
            $i++;
        }
        echo $table_data;
}

if(isset($_POST['assign_room']))
{
    $frm_data = filteration($_POST);

    $query = "UPDATE `booking_order` bo INNER JOIN `booking_details` db
    ON bo.booking_id = db.booking_id
    SET bo.arrival = ?, bo.rate_review = ?, db.room_no = ?
    WHERE bo.booking_id = ?";

    $values = [1,0,$frm_data['room_no'],$frm_data['booking_id']];

    $res = update($query,$values,'iisi');// it will update 2 rows so it will return 2

    echo ($res==2) ? 1 : 0;
}

if (isset($_POST['cancel_booking']))
{
    $frm_data = filteration($_POST);
  
    $query = "UPDATE `booking_order` 
    SET `booking_status`= ? , `refund`=?
    WHERE `booking_id` = ?";

    $values = ['cancelled',0,$frm_data['booking_id']];

    $res = update($query,$values,'sii');

    echo $res;
}

?>





