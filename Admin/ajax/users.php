<?php
require('../Inc1/essentials.php');
require('../Inc1/db_config.php');
adminLogin();


if(isset($_POST['get_users']))
{
    $res = selectAll('user_cred');
    $i = 1;
    $path = USERS_IMG_PATH;
    $data = "";
    while($row = mysqli_fetch_assoc($res))
    {
        $del_btn = "<button type='button' onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i>
        </button>";

        $date = date("d-m-Y",strtotime($row['datetime']));

        $data.="
            <tr>
                <td>$i</td>
                <td>
                <img src='$path$row[profile]' width='55px'>
                <br>
                $row[name]
                </td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[address]</td>
                <td>$row[dob]</td>
                <td>$date</td>
                <td>$del_btn</td>
            </tr>
           
        ";
        $i++;
    }
    echo $data;
}


if (isset($_POST['remove_user']))
{
    $frm_data = filteration($_POST);
  
    $res = delete("DELETE FROM `user_cred` WHERE `id`=?",[$frm_data['user_id']],'i');


    if($res)
    {
        echo 1;
    }
    else
    {
        echo 0;
    }
}


if(isset($_POST['search_user']))
{
    $frm_data = filteration($_POST);

    $query = "SELECT * FROM `user_cred` WHERE `name` LIKE ?";
    $res = select($query,["%$frm_data[name]%"],'s');
    $i = 1;
    $path = USERS_IMG_PATH;
    $data = "";
    while($row = mysqli_fetch_assoc($res))
    {
        $del_btn = "<button type='button' onclick='remove_user($row[id])' class='btn btn-danger shadow-none btn-sm'>
        <i class='bi bi-trash'></i>
        </button>";

        $date = date("d-m-Y",strtotime($row['datetime']));

        $data.="
            <tr>
                <td>$i</td>
                <td>
                <img src='$path$row[profile]' width='55px'>
                <br>
                $row[name]
                </td>
                <td>$row[email]</td>
                <td>$row[phonenum]</td>
                <td>$row[address]</td>
                <td>$row[dob]</td>
                <td>$date</td>
                <td>$del_btn</td>
            </tr>
           
        ";
        $i++;
    }
    echo $data;
}
?>

