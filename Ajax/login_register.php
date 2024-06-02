<?php
require('../Admin/Inc1/essentials.php');
require('../Admin/Inc1/db_config.php');
require("../Inc/sendgrid/sendgrid-php.php");
date_default_timezone_get();


function send_mail($uemail,$token,$type)
{
    if($type == "email_confirmation")
    {
        $page = 'email_confirm.php';
        $subject = "Account Verification Link";
        $content = "confirm your email";
    }
    else
    {
        $page = 'index.php';
        $subject = "Account Reset Link";
        $content = "reset your account";
    }
    $email = new \SendGrid\Mail\Mail(); 
    $email->setFrom(SENDGRID_EMAIL,SENDGRID_NAME);
    $email->setSubject($subject);
    $email->addTo($uemail);

    $email->addContent(
        "text/html",
         "
         Click the link to $content <br>
         <a href='".SITE_URL."$page?$type&email=$uemail&token=$token"."'>
            CLICK ME
         </a>
         "
    );
    $sendgrid = new \SendGrid(SENDGRID_API_KEY);
    if($sendgrid->send($email))
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

if (isset($_POST['register'])) {
    $data = filteration($_POST);

    // Validate password and confirm password
    if ($data['pass'] != $data['cpass']) {
        echo 'pass_mismatch';
        exit();
    }

    // Check if user already exists
    $user_exist_query = "SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1";
    $u_exist = select($user_exist_query, [$data['email'], $data['phonenum']], "ss");

    if (mysqli_num_rows($u_exist) != 0) {
        $u_exist_fectch = mysqli_fetch_assoc($u_exist);

        if ($u_exist_fectch['email'] == $data['email']) {
            echo 'email_already';
        } else {
            echo 'phone_already';
        }
        exit;
    }

    // Upload user image to server
    $img = uploadUserImage($_FILES['profile']);
    if ($img == 'inv_img') {
        echo 'inv_img';
        exit;
    } elseif ($img == 'upd_failed') {
        echo 'upd_failed';
        exit;
    }

    //send email
    $token = bin2hex(random_bytes(16));

    if(!send_mail($data['email'],$token,"email_confirmation"))
    {
        echo 'mail_failed';
        exit;
    }
    

    // Encrypt password
    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

    // Insert user data into database
    $insert_query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `dob`,`profile`, `password`,`token`)
    VALUES (?, ?, ?, ?, ?, ?, ?,?)";
    $insert_values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['dob'],$img, $enc_pass,$token];


    if (insert($insert_query, $insert_values, 'ssssssss')) {
        echo 'success';
    } else {
        echo 'ins_failed';
    }
}


if (isset($_POST['login'])) {
    // Xác minh và lấy dữ liệu từ form đăng nhập
    $data = filteration($_POST);
    $email_mob = $data['email_mob']; // Lấy dữ liệu từ email hoặc số điện thoại

    // Kiểm tra xem người dùng tồn tại trong cơ sở dữ liệu
    $user_exist_query = "SELECT * FROM `user_cred` WHERE `email`=? OR `phonenum`=? LIMIT 1";
    $u_exist = select($user_exist_query, [$email_mob, $email_mob], "ss");

    if (mysqli_num_rows($u_exist) != 0) {
        // Lấy dữ liệu của người dùng
        $u_exist_fectch = mysqli_fetch_assoc($u_exist);
        
        
        // Xác minh mật khẩu
        $password = $data['pass']; // Lấy mật khẩu từ dữ liệu nhập vào
        if (password_verify($password, $u_exist_fectch['password'])) {
            // Bắt đầu phiên đăng nhập
            session_start();
            $_SESSION['login'] = true;
            $_SESSION['uId'] = $u_exist_fectch['id'];
            $_SESSION['uName'] = $u_exist_fectch['name'];
            $_SESSION['uPic'] = $u_exist_fectch['profile'];
            $_SESSION['uPhone'] = $u_exist_fectch['phonenum'];

            echo 1; // Đăng nhập thành công
        } else {
            echo 'invalid_pass'; // Mật khẩu không hợp lệ
        }
    } else {
        echo 'inv_email_mob'; // Người dùng không tồn tại
    }
}

if (isset($_POST['forgot_pass'])) {
    $data = filteration($_POST);
    $email_mob = $data['email']; // Lấy dữ liệu từ email hoặc số điện thoại

    // Kiểm tra xem người dùng tồn tại trong cơ sở dữ liệu
    $user_exist_query = "SELECT * FROM `user_cred` WHERE `email`=? LIMIT 1";
    $u_exist = select($user_exist_query, [$email_mob], "s");

    if (mysqli_num_rows($u_exist) == 0) {
        // Lấy dữ liệu của người dùng
        echo 'inv_email';
    }
    else
    {
        $u_exist_fectch = mysqli_fetch_assoc($u_exist);
        if($u_exist_fectch['is_verified']==0)
        {
            echo 'not_verified';
        }
        else if($u_exist_fectch['status']==0)
        {
            echo 'inactive';
        }
        else
        {
            //send reset
            $token = bin2hex(random_bytes(16));
            if(!send_mail($data['email'],$token,'account_recovery'))
            {
                echo 'mail_failed';
            }
            else
            {
                $date = date("Y-m-d");

                $query = mysqli_query($con,"UPDATE `user_cred` SET `token`='$token' ,`t_expire`='$date'  
                WHERE `id`='$u_exist_fectch[id]'");

                if($query)
                {
                    echo 1;
                }
                else{
                    echo 'upd_failed';
                }
            }
        }

    }
  

}
if (isset($_POST['recover_user'])) {
    $data = filteration($_POST);

    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=?, `token`=?, `t_expire`=? WHERE `email`=? AND `token`=?";
    $values = [$enc_pass, null, null, $data['email'], $data['token']];

    if (update($query, $values, 'sssss')) {
        echo 1;
    } else {
        echo 'failed';
    }
}






