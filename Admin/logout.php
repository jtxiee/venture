<?php
    require('Inc1/essentials.php');
    session_start();
    session_destroy();
    redirect('index.php');
?>