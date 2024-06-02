<?php
require('../Admin/Inc1/essentials.php');
require('../Admin/Inc1/db_config.php');

if(isset($_POST['info_form']))
{
    $frm_data =filteration($_POST);
    session_start();

    // Check if user already exists
    $user_exist_query = "SELECT * FROM `user_cred` WHERE `phonenum`=? AND `id`!=? LIMIT 1";
    $u_exist = select($user_exist_query, [$data['phonenum'],$_SESSION['uId']], "ss");
    
    if (mysqli_num_rows($u_exist) != 0) {
            echo 'phone_already';
        exit;
    }

    $query = "UPDATE `user_cred` SET `name`=?,`address`=?,`phonenum`=?,`dob`=? WHERE `id`=? LIMIT 1";
    $values=[$frm_data['name'],$frm_data['address'],$frm_data['phonenum'],$frm_data['dob'],$_SESSION['uId']];

    if(update($query,$values,'sssss'))
    {
        $_SESSION['uName'] = $frm_data['name'];
        echo 1;
    }
    else
    {
        echo 0;
    }
}

if(isset($_POST['profile_form']))
{
    session_start();

    $img = uploadUserImage($_FILES['profile']);
    if ($img == 'inv_img') {
        echo 'inv_img';
        exit;
    } elseif ($img == 'upd_failed') {
        echo 'upd_failed';
        exit;
    }

    // fetchinh old image and deleting it
    $user_exist_query = "SELECT `profile` FROM `user_cred` WHERE `id`!=? LIMIT 1";
    $u_exist = select($user_exist_query, [$_SESSION['uId']], "s");

    $u_exist_fectch = mysqli_fetch_assoc($u_exist);

    deleteImage($u_exist_fectch['profile'],USERS_FOLDER);
    

    $query = "UPDATE `user_cred` SET `profile`=? WHERE `id`=? LIMIT 1";
    $values=[$img,$_SESSION['uId']];

    if(update($query,$values,'ss'))
    {
        $_SESSION['uPic'] = $img;
        echo 1;
    }
    else
    {
        echo 0;
    }
}

if(isset($_POST['pass_form']))
{
    $frm_data = filteration($_POST);
    session_start();

    if($frm_data['new_pass']!=$frm_data['confirm_pass'])
    {
        echo 'mismatch';
        exit();
    }

    $enc_pass = password_hash($frm_data['new_pass'], PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=? WHERE `id`=? LIMIT 1";
    $values=[$enc_pass,$_SESSION['uId']];

    if(update($query,$values,'ss'))
    {
        echo 1;
    }
    else
    {
        echo 0;
    }
}

?>