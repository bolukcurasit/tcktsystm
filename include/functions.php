<?php

function check_login($con)
{
    if(!isset($_SESSION['user']))
        return false;
    
    $user = $_SESSION['user'];
    
    $query = "SELECT * FROM users WHERE id = {$user['id']} AND name = '{$user['name']}' AND username = '{$user['username']}' AND type = '{$user['type']}'";
    
    $user = mysqli_query($con, $query);
    
    if(mysqli_num_rows($user) != 1)
        return false;
    
    return true;
}

function mailgonder($kime,$konu,$mesaj){
    if(filter_var($kime,FILTER_VALIDATE_EMAIL)){
        $mesaj .= '<hr /><span style="font-size:10px;color:#bbbbbb;">Bu mesaj '. date('H:i:s d.m.Y') .' tarihinde ' . $_SERVER['REMOTE_ADDR'] . ' ip adresinden gönderilmiştir. | <a href="https://retasoft.com">RETASOFT</a> |</span>';
        require_once "class.phpmailer.php";
        $mail=new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPDebug = 1;
        $mail->SMTPAuth = true;
        $mail->Host = "mail.retasoft.com";
        $mail->Port = 587;
        $mail->Username= "bilgi@retasoft.com";
        $mail->Password= "3259604rb";
        $mail->From= "bilgi@retasoft.com";
        $mail->FromName= "Destek Sistemi V1.0.0";
        $mail->CharSet="utf-8";
        $mail->AddAddress($kime);
        $mail->Subject=$konu;
        $mail->IsHTML(true);
        $mail->Body=$mesaj;
        if($mail->Send()) return true;
        else echo $mail->ErrorInfo;
    }
}

?>