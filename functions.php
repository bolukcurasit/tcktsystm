<?php
function redirectTo($url){
	header("Location: ".$url);
	exit();	
}

function acik_oturum(){
	$rv = false;
	if(isset($_SESSION['user'])){
		$user_id = $_SESSION['user']['id'];
		$query = "SELECT * FROM kullanicilar WHERE user_id = {$user_id}";
		$user = mysql_fetch_array(mysql_query($query));
		$md5_session_id = md5(session_id());
		$sec_code = md5($user['password'].$md5_session_id);
		if($sec_code === $_SESSION['sec_code'])
			$rv = true;
	}
	return $rv;
}

function mysql_prep( $value ) {
	$value = trim($value);
	$magic_quotes_active = get_magic_quotes_gpc();
	$new_enough_php = function_exists( "mysql_real_escape_string" );
	if( $new_enough_php ) { 
		if( $magic_quotes_active ) { $value = stripslashes( $value ); }
		$value = mysql_real_escape_string( $value );
	} else { 
		if( !$magic_quotes_active ) { $value = addslashes( $value ); }
	}
	return $value;
}

function upload_file($file, $folder){	
	$allowed = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
	$parts = explode(".", $_FILES[$file]["name"]);
	$name_first = "";
	for($i = 0; $i < (count($parts) - 1); $i++){
		if($name_first == "")
			$name_first = $parts[$i];
		else
			$name_first .= $parts[$i];	
	}
	$name_first = ayikla($name_first);
	$extension = end($parts);	
	do{
		$name = substr(md5(rand(1,999999)),5,5);
	}while(file_exists($folder ."/".$name_first."-".$name.".".$extension));
	
	
	if(($_FILES[$file]["size"] < 5*1024*1024) && in_array($extension,$allowed)){
	  	if($_FILES[$file]["error"] > 0){
			return "Hata!";
		}else{
		 	move_uploaded_file($_FILES[$file]["tmp_name"],
		 	$folder ."/".$name_first."-".$name.".".$extension);
		  	return $name_first."-".$name.".".$extension ;
		}
	}else{
		return "Hatalı Dosya!";
	}	
}

function req_fields($arr){
	$rv = true;
	foreach ($arr as $value) {
		if(!isset($_POST[$value]) || empty($_POST[$value])){
			$rv = false;
			break;
		}
	}
	return $rv;
}

function ayikla($cumle){
    $ex1    =   array(",", " ", "'", ":", ";", "\"", "?", "*", "(", ")", "[", "]", "&", "%", "+", "#", ",", "!", "ı", "ö", "ç", "ğ", "ü", "ş", "İ","Ö","Ç","Ğ","Ü","Ş");
    $ex2    =   array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "i", "o", "c", "g", "u", "s", "i","o","c","g","u","s");
    return str_replace($ex1, $ex2, $cumle);
}

function read_form($arr){
	$rv = array();
	foreach($arr as $field){
		if(isset($_POST[$field])){
			if(!empty($_POST[$field]))
				$rv[$field] = mysql_prep($_POST[$field]);
			else
				$rv[$field] = '';
		}else{
			$rv[$field] = '';
		}
	}
	return $rv;
}

function mailgonder($kime,$konu,$mesaj,$kimden){
   if(filter_var($kime,FILTER_VALIDATE_EMAIL)){
	    $mesaj .= '<hr /><span style="font-size:10px;color:#bbbbbb;">Bu mesaj '. date('H:i:s d.m.Y') .' tarihinde ' . $_SERVER['REMOTE_ADDR'] . ' ip adresinden gönderilmiştir. | <a href="https://retasoft.com">RETASOFT</a> |</span>'; 
		require_once "class.phpmailer.php";
		$mail=new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 1;
		$mail->SMTPAuth = true;
		$mail->Host = ayarGetir("site_mail_server");
		$mail->Port = 587; 
		$mail->Username= ayarGetir("site_ana_mail");
		$mail->Password= ayarGetir("site_ana_mail_sifre");
		$mail->From= ayarGetir("site_ana_mail");
		$mail->FromName=$kimden;
		$mail->CharSet="utf-8";
		$mail->AddAddress($kime);
		$mail->Subject=$konu;
		$mail->IsHTML(true);
		$mail->Body=$mesaj;
		if($mail->Send()) return true;
			else echo $mail->ErrorInfo;
   }     
}

function ayarGetir($str){
	$query = "SELECT * FROM settings WHERE name = '{$str}'";
	$deger = mysql_fetch_array(mysql_query($query));
	if(mysql_num_rows(mysql_query($query)))
		return $deger['value'];
	else
		return "";
}

function ayarGuncelle($name,$val){
	$query = "SELECT * FROM settings WHERE name = '{$name}'";
	if(mysql_num_rows(mysql_query($query)) > 0){
		$query = "UPDATE settings SET value = '{$val}' WHERE name = '{$name}'";
	}else{
		$query = "INSERT INTO settings (name, value) VALUES ('{$name}', '{$val}')";
	}
	if(mysql_query($query))
		return true;
	else
		return false;
}

function home_get($str){
	$query = "SELECT * FROM home WHERE name = '{$str}'";
	$deger = mysql_fetch_array(mysql_query($query));
	if(mysql_num_rows(mysql_query($query)))
		return $deger['value'];
	else
		return "";
}

function home_set($name,$val){
	$query = "SELECT * FROM home WHERE name = '{$name}'";
	if(mysql_num_rows(mysql_query($query)) > 0){
		$query = "UPDATE home SET value = '{$val}' WHERE name = '{$name}'";
	}else{
		$query = "INSERT INTO home (name, value) VALUES ('{$name}', '{$val}')";
	}
	if(mysql_query($query))
		return true;
	else
		return false;
}

function make_thumb($src, $dest, $desired_width = 320) {
  /* read the source image */
  $source_image = imagecreatefromjpeg($src);
  $width = imagesx($source_image);
  $height = imagesy($source_image);

  /* find the “desired height” of this thumbnail, relative to the desired width  */
  $desired_height = floor($height * ($desired_width / $width));

  /* create a new, “virtual” image */
  $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

  /* copy source image at a resized size */
  imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

  /* create the physical thumbnail image to its destination */
  imagejpeg($virtual_image, $dest);
}

?>