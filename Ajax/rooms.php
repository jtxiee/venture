<?php

// require('../Admin/Inc1/essentials.php');
// require('../Admin/Inc1/db_config.php');

// session_start();

// if (isset($_GET['fetch_rooms'])) {

//     //check availability data decode
//     $chk_avail = json_decode($_GET['chk_avail'],true);


//     // checkin and checkout filter availablity
//     if($chk_avail['checkin']!='' && $chk_avail['checkout']!='')
//     {
//         date("Y-m-d");
//         $today_date = new DateTime(date("Y-m-d"));
//         $checkin_date = new DateTime($chk_avail['checkin']);
//         $checkout_date = new DateTime($chk_avail['checkout']);
    
//         if ($checkin_date == $checkout_date) {
//             echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
//             exit;
//         } else if ($checkout_date < $checkin_date) {
//             echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
//             exit;
//         } else if ($checkin_date < $today_date) {
//             echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
//             exit;
//         }
//     }

//     // guests data decode
//     $guests =  json_decode($_GET['guests'],true);
//     $adults = ($guests['adults']!='') ? $guests['adults'] : 0;
//     $children = ($guests['children']!='') ? $guests['children'] : 0;

//     //facilities data decode
//     $facility_list = json_decode($_GET['facility_list'],true);  
    

//     $count_rooms = 0; // count no. of room and output variable to store room cards
//     $output = "";

//     // fectching settings table to check website is shutdowns or not
//     $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
//     $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));


//     // query for room cards with guests filter
//     $room_res = select("SELECT * FROM `rooms` WHERE `adult`>=? AND `children`>=? AND `status`=? AND `removed`=?", [$adults,$children,1, 0], 'iiii');
    
//     while ($room_data = mysqli_fetch_assoc($room_res)) {

//         // check availablity filter
//         if($chk_avail['checkin']!='' && $chk_avail['checkout']!='')
//         {
//             $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
//             WHERE booking_status=? AND room_id=?
//             AND check_out > ? AND check_in < ?
//             ";
    
//             $values = ['booker',$room_data['id'], $chk_avail['checkin'], $chk_avail['checkout']];
    
//             $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));
    
//             $rq_result = select("SELECT `quantity` FROM `rooms` WHERE `id`=?", [$_SESSION['room']['id']], 'i');
    
//             $rq_fecth = mysqli_fetch_assoc($rq_result);
    
//             if (($room_data['quantity'] - $tb_fetch['total_bookings']) == 0) {
//                 continue;
//             }
//         }

//         // get facilities of room width filter
//         $fac_count = 0;

//         $fac_q = mysqli_query($con, "SELECT f.name, f.id  FROM `facilities` f 
//             INNER JOIN `rooms_facilities` rfac ON f.id = rfac.facilities_id 
//              WHERE rfac.room_id = '$room_data[id]'");

//         $facilities_data = "";
//         while ($fac_row = mysqli_fetch_assoc($fac_q)) {
//             if(in_array($fac_row['id'],$facility_list['facilities']))
//             {
//                 $fac_count++;
//             }
//             $facilities_data .= " <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
//                 $fac_row[name]
//                 </span>";
//         }
//         if(count($facility_list['facilities'])!=$fac_count)
//         {
//             continue;
//         }

//         // get features of room
//         $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
//                 INNER JOIN `rooms_features` rfea ON f.id = rfea.features_id 
//                 WHERE rfea.room_id = '$room_data[id]'");

//         $features_data = "";
//         while ($fea_row = mysqli_fetch_assoc($fea_q)) {
//             $features_data .= " <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
//                         $fea_row[name]
//                 </span>";
//         }

//         // get thumb of image

//         $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
//         $thum_q = mysqli_query($con, "SELECT * FROM `rooms_images` 
//                 WHERE `room_id`='$room_data[id]' AND `thumb` = '1'");

//         if (mysqli_num_rows($thum_q) > 0) {
//             $thum_res = mysqli_fetch_assoc($thum_q);
//             $room_thumb = ROOMS_IMG_PATH . $thum_res['image'];
//         }

//         $book_btn = "";

//         if (!$settings_r['shutdown']) {
//             $login = 0;
//             if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
//                 $login = 1;
//             }
//             $book_btn = " <button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Đặt Ngay</button>";
//         }

//         //  print room card

//         $output.="
//                 <div class='card mb-4 border-0 shadow'>
//                     <div class='row g-0 p-3 align-items-center'>
//                         <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
//                             <img src='$room_thumb' class='img-fluid rounded'>
//                         </div>
//                         <div class='col-md-5 px-lg-3 px-md-3 px-0'>
//                             <h5 class='mb-3'>$room_data[name]</h5>
//                             <div class='features mb-3'>
//                                 <h6 class='mb-1'>Đặc Trưng</h6>
//                                 $features_data
//                             </div>
//                             <div class='facilities mb-3'>
//                             <h6 class='mb-1'>Tiện Ích</h6>
//                                 $facilities_data
//                             </div>
//                             <div class='guests'>
//                                 <h6 class='mb-1'>Khách</h6>
//                                 <span class='badge rounded-pill bg-light text-dark text-wrap'>
//                                     $room_data[adult] Adult
//                                 </span>
//                                 <span class='badge rounded-pill bg-light text-dark text-wrap'>
//                                     $room_data[children] Children
//                                 </span>
//                             </div>
//                         </div>
//                         <div class='col-md-2 mt-lg-0 mt-md-0 text-center'>
//                             <h6 class='mb-4'>$room_data[price] VNĐ</h6>
//                             $book_btn
//                             <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>Xem Chi Tiết</a>
//                         </div>
//                     </div>
//                 </div>
//             ";
//         $count_rooms++;
//     }
//     if($count_rooms>0)
//     {
//         echo $output;
//     }
//     else
//     {
//         echo "<h3 class='text-center text-danger'>No room to show!</h3>";
//     }
// }
require('../Admin/Inc1/essentials.php');
require('../Admin/Inc1/db_config.php');

session_start();

if (isset($_GET['fetch_rooms'])) {

    // check availability data decode
    $chk_avail = json_decode($_GET['chk_avail'], true);

    // checkin and checkout filter availability
    if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
        date("Y-m-d");
        $today_date = new DateTime(date("Y-m-d"));
        $checkin_date = new DateTime($chk_avail['checkin']);
        $checkout_date = new DateTime($chk_avail['checkout']);

        if ($checkin_date == $checkout_date) {
            echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
            exit;
        } else if ($checkout_date < $checkin_date) {
            echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
            exit;
        } else if ($checkin_date < $today_date) {
            echo "<h3 class='text-center text-danger'>Invalid Dates Entered!</h3>";
            exit;
        }
    }

    // guests data decode
    $guests = json_decode($_GET['guests'], true);
    $adults = ($guests['adults'] != '') ? $guests['adults'] : 0;
    $children = ($guests['children'] != '') ? $guests['children'] : 0;

    // facilities data decode
    $facility_list = json_decode($_GET['facility_list'], true);

    // search name filter
    $name_search = isset($_GET['name_search']) ? $_GET['name_search'] : '';

    $count_rooms = 0; // count number of rooms and output variable to store room cards
    $output = "";

    // fetching settings table to check if the website is shutdown
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
    $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

    // query for room cards with guests filter
    $query = "SELECT * FROM `rooms` WHERE `adult` >= ? AND `children` >= ? AND `status` = ? AND `removed` = ?";
    $params = [$adults, $children, 1, 0];
    $types = 'iiii';

    // add name search filter to the query
    if ($name_search != '') {
        $query .= " AND `name` LIKE ?";
        $params[] = '%' . $name_search . '%';
        $types .= 's';
    }

    $room_res = select($query, $params, $types);

    while ($room_data = mysqli_fetch_assoc($room_res)) {

        // check availability filter
        if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
            $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `booking_order`
                WHERE booking_status=? AND room_id=?
                AND check_out > ? AND check_in < ?";

            $values = ['booker', $room_data['id'], $chk_avail['checkin'], $chk_avail['checkout']];

            $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));

            $rq_result = select("SELECT `quantity` FROM `rooms` WHERE `id`=?", [$room_data['id']], 'i');

            $rq_fecth = mysqli_fetch_assoc($rq_result);

            if (($room_data['quantity'] - $tb_fetch['total_bookings']) == 0) {
                continue;
            }
        }

        // get facilities of room with filter
        $fac_count = 0;
        $fac_q = mysqli_query($con, "SELECT f.name, f.id FROM `facilities` f 
            INNER JOIN `rooms_facilities` rfac ON f.id = rfac.facilities_id 
            WHERE rfac.room_id = '$room_data[id]'");

        $facilities_data = "";
        while ($fac_row = mysqli_fetch_assoc($fac_q)) {
            if (in_array($fac_row['id'], $facility_list['facilities'])) {
                $fac_count++;
            }
            $facilities_data .= " <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                $fac_row[name]
                </span>";
        }
        if (count($facility_list['facilities']) != $fac_count) {
            continue;
        }

        // get features of room
        $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
                INNER JOIN `rooms_features` rfea ON f.id = rfea.features_id 
                WHERE rfea.room_id = '$room_data[id]'");

        $features_data = "";
        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
            $features_data .= " <span class='badge rounded-pill bg-light text-dark text-wrap me-1 mb-1'>
                        $fea_row[name]
                </span>";
        }

        // get thumb of image
        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thum_q = mysqli_query($con, "SELECT * FROM `rooms_images` 
                WHERE `room_id`='$room_data[id]' AND `thumb` = '1'");

        if (mysqli_num_rows($thum_q) > 0) {
            $thum_res = mysqli_fetch_assoc($thum_q);
            $room_thumb = ROOMS_IMG_PATH . $thum_res['image'];
        }

        $book_btn = "";

        if (!$settings_r['shutdown']) {
            $login = 0;
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $login = 1;
            }
            $book_btn = " <button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Đặt Ngay</button>";
        }

        // print room card
        $output .= "
                <div class='card mb-4 border-0 shadow'>
                    <div class='row g-0 p-3 align-items-center'>
                        <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                            <img src='$room_thumb' class='img-fluid rounded'>
                        </div>
                        <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                            <h5 class='mb-3'>$room_data[name]</h5>
                            <div class='features mb-3'>
                                <h6 class='mb-1'>Features</h6>
                                $features_data
                            </div>
                            <div class='facilities mb-3'>
                            <h6 class='mb-1'>Facilities</h6>
                                $facilities_data
                            </div>
                            <div class='guests'>
                                <h6 class='mb-1'>Guest</h6>
                                <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                    $room_data[adult] Adult
                                </span>
                                <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                    $room_data[children] Children
                                </span>
                            </div>
                        </div>
                        <div class='col-md-2 mt-lg-0 mt-md-0 text-center'>
                            <h6 class='mb-4'>$room_data[price] VNĐ</h6>
                            $book_btn
                            <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
            ";
        $count_rooms++;
    }
    if ($count_rooms > 0) {
        echo $output;
    } else {
        echo "<h3 class='text-center text-danger'>No room to show!</h3>";
    }
}


?>
