<?php
require('../Inc1/essentials.php');
require('../Inc1/db_config.php');
adminLogin();

if(isset($_POST['add_tour']))
{
    $frm_data = filteration($_POST);
    $flag = 0;
  
    $q1 = "INSERT INTO `tours`(`name`,`price`,`description`) VALUES (?,?,?)";
    $values = [$frm_data['name'],$frm_data['price'],$frm_data['desc']];

    if(insert($q1,$values,'sis'))
    {
        $flag = 1;
    }
    $tour_id = mysqli_insert_id($con);

    if($flag)
    {
        echo 1;
    }
    else
    {
        echo 0;
    }
}


if(isset($_POST['get_all_tours']))
{
    $res = select("SELECT * FROM `tours` WHERE `removed`=?",[0],'i');
    $i = 1;
    $data = "";
    while($row = mysqli_fetch_assoc($res))
    {
        if($row['status']==1)
        {   
            $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>active</button>";
        }
        else
        {
            $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>inactive</button>";
        }

        $data.="
            <tr class='align-middle'> 
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[price]</td>
                <td>$status</td>
                <td>
                <button type='button' onclick='edit_details($row[id])' class='btn btn-primary shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#edit-tour'>
                    <i class='bi bi-pencil-square'></i>
                </button>
                <button type='button' onclick=\"tour_images($row[id],'$row[name]')\" class='btn btn-info shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#tour-images'>
                <i class='bi bi-images'></i>
                </button>
                <button type='button' onclick='remove_tour($row[id])' class='btn btn-danger shadow-none btn-sm'>
                <i class='bi bi-trash'></i>
                </button>
                </td>
            </tr>
        ";
        $i++;
    }
    echo $data;
}


if(isset($_POST['get_tour']))
{
    $frm_data = filteration($_POST);

    $res1 = select("SELECT * FROM `tours` WHERE `id`=?",[$frm_data['get_tour']],'i');
    $roomdata  = mysqli_fetch_assoc($res1);

    $data = ["tourdata" => $roomdata];

    $data = json_encode($data);

    echo $data;
}


if(isset($_POST['edit_tour']))
{
    $frm_data = filteration($_POST);
    $flag = 0;

    $q1 = "UPDATE `tours` SET `name`=?, `price`=?, `description`=? WHERE `id`=?";

    $values = [$frm_data['name'], $frm_data['price'], $frm_data['desc'], $frm_data['tour_id']];

    if(update($q1, $values, 'sisi'))
    {
        $flag = 1;
    }

    if($flag)
    {
        echo 1;
    }
    else
    {
        echo 0;
    }
}


if(isset($_POST['toggle_status']))
{   
    $frm_data = filteration($_POST);
    $q = "UPDATE `tours` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'],$frm_data['toggle_status']];

    if(update($q,$v,'ii'))
    {   
        echo 1;
    }
    else{
        echo 0;
    }
}

if (isset($_POST['add_image'])) {
    $frm_data = filteration($_POST);

    $img_r =  uploadImage($_FILES['image'],TOURS_FOLDER);

    if ($img_r == 'inv_img') {
        echo $img_r;
    } else if ($img_r == 'inv_size') {
        echo $img_r;
    } else if ($img_r == 'upd_failed') {
        echo $img_r;
    } else {
        $q = "INSERT INTO `tours_images`(`tour_id`, `image`) VALUES (?,?)";
        $values = [$frm_data['tour_id'], $img_r];
        $res = insert($q, $values, 'is');
        echo $res;
    }
}

if (isset($_POST['get_tour_images'])) {
    $frm_data = filteration($_POST);
    $res = select("SELECT * FROM `tours_images` WHERE `tour_id`=?",[$frm_data['get_tour_images']],'i');

    $path = TOURS_IMG_PATH;

    // Kiểm tra xem $_FILES['image'] có tồn tại không
    if(isset($_FILES['image'])) {
        $img_r = uploadImage($_FILES['image'], TOURS_FOLDER);
    }

    while($row = mysqli_fetch_assoc($res))
    {
        if($row['thumb']==1)
        {
            $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
        }
        else
        {
            $thumb_btn = "<button onclick='thumb_image($row[sr_no],$row[tour_id])' class='btn btn-secondary  shadow-none'>
            <i class='bi bi-check-lg'></i>
            </button>";
        }
        echo <<<data
            <tr class='align-middle'>
                <td><img src='$path$row[image]' class='img-fluid'></td>
                <td>$thumb_btn</td>
                <td>
                    <button onclick='rem_image($row[sr_no],$row[tour_id])' class='btn btn-danger  shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>
                </td>
            </tr>
        data;
    }
}

if (isset($_POST['rem_image'])) {
    $frm_data = filteration($_POST);


    $value = [$frm_data['image_id'],$frm_data['tour_id']];

    $pre_q = "SELECT * FROM `tours_images` WHERE `sr_no`=? AND `tour_id`=?";
    $res = select($pre_q,$value,'ii');
    $img = mysqli_fetch_assoc($res);

    if(deleteImage($img['image'],TOURS_FOLDER))
    {
        $q = "DELETE FROM  `tours_images` WHERE  `sr_no`=? AND `tour_id`=?";
        $res = delete($q,$value,'ii');
        echo $res;
    }
    else
    {
        echo 0;
    }

}

if (isset($_POST['thumb_image'])) {
    $frm_data = filteration($_POST);

    // Ghi log
    file_put_contents('thumb_image_log.txt', print_r($frm_data, true) . PHP_EOL, FILE_APPEND);

    $pre_q = "UPDATE `tours_images` SET `thumb`=? WHERE  `tour_id`=?";
    $pre_v = [0,$frm_data['tour_id']];
    $pre_res = update($pre_q, $pre_v,'ii');

    $q = "UPDATE `tours_images` SET `thumb`=? WHERE `sr_no`=? AND `tour_id`=?";
    $v = [1,$frm_data['image_id'], $frm_data['tour_id']];
    $res = update($q,$v,'iii');

    // Ghi log
    file_put_contents('thumb_image_log.txt', "pre_res: $pre_res, res: $res" . PHP_EOL, FILE_APPEND);

    echo $res;
}

if (isset($_POST['remove_tour']))
{
    $frm_data = filteration($_POST);
    $res1 = select("SELECT * FROM `tours_images` WHERE `tour_id`=?",[$frm_data['tour_id']],'i');

    while($row = mysqli_fetch_assoc($res1))
    {
        deleteImage($row['image'], TOURS_FOLDER);
    }

    $res2 = delete("DELETE FROM `tours_images` WHERE `tour_id`=?",[$frm_data['tour_id']],'i');
    $res3 = update("UPDATE `tours` SET `removed`=? WHERE `id`=?", [1, $frm_data['tour_id']], 'ii');

    if($res2 || $res3)
    {
        echo 1;
    }
    else
    {
        echo 0;
    }
}


?>

