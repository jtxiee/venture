<?php
require('../Inc1/essentials.php');
require('../Inc1/db_config.php');
adminLogin();

if(isset($_POST['get_bookings']))
{
    $frm_data = filteration($_POST);

    // Kiểm tra xem biến $frm_data['search'] có tồn tại không trước khi sử dụng
    $search = isset($frm_data['search']) ? $frm_data['search'] : '';

    $limit = 5;
    $page = isset($frm_data['page']) ? $frm_data['page'] : 1; // Kiểm tra xem biến $frm_data['page'] có tồn tại không
    $start = ($page-1) *  $limit;

    // Thực hiện truy vấn SQL
    $query = "SELECT bo.*, bd.* FROM `booking_order` bo
    INNER JOIN `booking_details` bd ON bo.booking_id = bd.booking_id
    WHERE ((bo.booking_status = 'booker' AND bo.arrival = 1) 
    OR (bo.booking_status = 'cancelled' AND bo.refund=1)
    OR (bo.booking_status = 'payment failed'))
    AND (bo.order_id LIKE ? OR bd.phonenum LIKE ? OR bd.user_name LIKE ?)
    ORDER BY bo.booking_id DESC";

    $res = select($query, ["%$search%", "%$search%", "%$search%"], 'sss');

    $limit_query = $query . " LIMIT $start, $limit";
    $limit_res = select($limit_query, ["%$search%", "%$search%", "%$search%"], 'sss');
    // Kiểm tra xem có kết quả trả về hay không
        $i = 1;
        $table_data = "";
        
       $total_rows = mysqli_num_rows($res);
       if ($total_rows == 0) {
        $output = json_encode(['table_data' => "<b>No Data Found!</b>", "pagination" => '']);
        echo $output;
        exit();
        }

        $i=$start+1;
        $table_data = "";

        // Duyệt qua các dòng dữ liệu
        while($data = mysqli_fetch_assoc($limit_res))
        {
            // Chuẩn bị dữ liệu để hiển thị
            $date = date("d-m-Y", strtotime($data['datetime']));
            $checkin = date("d-m-Y", strtotime($data['check_in']));
            $checkout = date("d-m-Y", strtotime($data['check_out']));


            if($data['booking_status']=='booker')
            {
                $status_bg = 'bg-success';
            }
            else if($data['booking_status']=='cancelled')
            {
                $status_bg = 'bg-danger';
            }
            else
            {
                $status_bg = 'bg-warning text-dark';
            }


            // Thêm dữ liệu vào chuỗi HTML
            $table_data .= "
                <tr>
                    <td>$i</td>
                    <td>
                        <span class='badge bg-primary'>
                            Order ID : {$data['order_id']}
                        </span>
                        <br>
                        <b>Name :</b> {$data['user_name']}
                        <br>
                        <b>Phone No :</b> {$data['phonenum']} 
                    </td>
                    <td>
                        <b>Room :</b> {$data['room_name']} 
                        <br>
                        <b>Price :</b> {$data['price']} VNĐ 
                    </td>
                    <td>
                        <b>Amout :</b> {$data['trans_amt']} VNĐ 
                        <br>
                        <b>Date :</b> $date 
                    </td>
                    <td>
                        <span class='badge $status_bg'>$data[booking_status]</span>
                    </td>
                    <td>
                        <button type='button' onclick='download($data[booking_id])' class='btn btn-outline-success btn-sm fw-bold shadow-none'>
                        <i class='bi bi-filetype-pdf'></i>
                        </button>
                    </td>
                </tr>
            ";
            $i++;
        }

        $pagination = "";
        if($total_rows>$limit)
        {
           $total_pages = ceil($total_rows/$limit);

           if($page!=1)
           {
            $pagination .="<li class='page-item'><button onclick='change_page(1)' class='page-link'>First</button></li>";
           }
           $disabled = ($page==1) ? "disabled" : "";//
           $prev =$page-1;
           $pagination .="<li class='page-item $disabled'><button onclick='change_page($prev)' class='page-link shadow-none'>Prev</button></li>";

           $disabled = ($page==$total_pages) ? "disabled" : "";
           $next = $page+1;
           $pagination .="<li class='page-item $disabled'><button onclick='change_page($next)' class='page-link'>Next</button></li>";

           if($page!=$total_pages)
           {
            $pagination .="<li class='page-item'><button onclick='change_page($total_pages)' class='page-link'>Last</button></li>";
           }
        }

        $output = json_encode(["table_data"=>$table_data,"pagination"=>$pagination]);
        echo $output;
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