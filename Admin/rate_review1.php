<?php
require('Inc1/essentials.php');
require('Inc1/db_config.php');
adminLogin();

if (isset($_GET['seen'])) {
    $frm_data = filteration($_GET);
    if ($frm_data['seen'] == 'all') {
        $q = "UPDATE `rating_review1` SET `seen`=?";
        $values = [1];
        if (update($q, $values, 'i')) {
            alert('success', 'Marked All As Read!');
        } else {
            alert('error', 'Operation Failed!');
        }
    } else {
        $q = "UPDATE `rating_review1` SET `seen`=? WHERE `sr_no`=?";
        $values = [1, $frm_data['seen']];
        if (update($q, $values, 'ii')) {
            alert('success', 'Marked As Read!');
        } else {
            alert('error', 'Operation Failed!');
        }
    }
}

if (isset($_GET['del'])) {
    $frm_data = filteration($_GET);
    if ($frm_data['del'] == 'all') {
        $q = "DELETE FROM `rating_review`";
        if (mysqli_query($con, $q)) {
            alert('success', 'All Data Delete!');
        } else {
            alert('error', 'Operation Failed!');
        }
    } else {
        $q = "DELETE FROM `rating_review1` WHERE `sr_no`=?";
        $values = [$frm_data['del']];
        if (delete($q, $values, 'i')) {
            alert('success', 'Data Delete!');
        } else {
            alert('error', 'Operation Failed!');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rating & Reviews</title>
    <?php require('Inc1/link.php'); ?>

</head>

<body class="bg-light">
    <?php require('Inc1/header.php') ?>
    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Rating & Reviews</h3>
                <div class="card border-0 shadow mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4">
                            <a href="?seen=all" class="btn btn-dark rounded-pill shadow-none btn-sm">
                                <i class="bi bi-check-all"></i> Mask all read
                            </a>
                            <a href="?del=all" class="btn btn-danger rounded-pill shadow-none btn-sm">
                                <i class="bi bi-trash"></i> Delete all
                            </a>
                        </div>

                        <div class="table-responsive-md">
                            <table class="table table-hover border">
                                <thead >
                                    <tr class="bg-dark text-light">
                                        <th scope="col">#</th>
                                        <th scope="col">Tour Name</th>
                                        <th scope="col">User Name</th>
                                        <th scope="col">Rating</th>
                                        <th scope="col" width="30%">Review</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $q = "SELECT rr.*,uc.name AS uname, r.name AS rname FROM `rating_review1` rr 
                                    INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                                    INNER JOIN `tours` r ON rr.tour_id = r.id
                                    ORDER BY `sr_no` DESC ";
                                    $data = mysqli_query($con, $q);
                                    $i = 1;
                                    while ($row = mysqli_fetch_assoc($data)) {

                                        $date = date('d-m-Y',strtotime($row['datetime']));
                                        $seen = ''; // Reset biến $seen trước khi thêm chuỗi <a> vào
                                        if ($row['seen'] != 1) {
                                            $seen .= "<a href='?seen={$row['sr_no']}' class='btn btn-sm rounded-pill btn-primary '>Mark as read</a>";
                                        }
                                        $seen .= "<a href='?del={$row['sr_no']}' class='btn btn-sm rounded-pill btn-danger '>Delete</a>";
                                        echo <<<query
                    <tr>
                        <td>$i</td>
                        <td>{$row['rname']}</td>
                        <td>{$row['uname']}</td>
                        <td>{$row['rating']}</td>
                        <td>{$row['review']}</td>
                        <td>$date</td>
                        <td>$seen</td>
                    </tr>
                query;
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    <?php require('Inc1/script.php'); ?>
</body>

</html>