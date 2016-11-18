<?php
    ob_start();
    session_start();
    include("include/config.php");
    include("include/functions.php");

    $sayfa = 'ana-sayfa';
    if(isset($_GET['v1']) && !empty($_GET['v1'])){
        $sayfa = $_GET['v1'];
    }

    $folder = 'pages/user';
    if(isset($_SESSION['user']) && $_SESSION['user']['type'] == 0){
        $folder = 'pages/admin';
    }

    $pages = scandir($folder);
    $page = $sayfa . '.php';

    $subPage = $folder . '/not-found.php';

    if(in_array($page, $pages)){
        $subPage = $folder . '/' . $page;
    }

    if(!check_login($con) && $sayfa != 'login'){
        header('Location: /login');
    }
?>

<?php require("header.php"); ?>

<section class="row main-section">
    <div class="container">
        <?php include($subPage); ?>
    </div>
</section>

<?php include("footer.php"); ?> 