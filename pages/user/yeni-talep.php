<?php
    if(isset($_POST['submit'])){
        $errs = [];

        if(!isset($_POST['subject']) || empty($_POST['subject'])){
            $errs[] = "Lütfen konu giriniz.";
        }

        if(!isset($_POST['content']) || empty($_POST['content'])){
            $errs[] = "Lütfen talebinizi giriniz.";
        }

        if(!count($errs)){
            $user = $_SESSION['user'];

            $subject = mysqli_real_escape_string($con, strip_tags($_POST['subject']));
            $content = mysqli_real_escape_string($con, strip_tags($_POST['content']));

            $query = "INSERT INTO tickets (user_id, subject, content) VALUES ({$user['id']}, '{$subject}', '{$content}');";

            if(mysqli_query($con, $query)){
                $ticket_id = mysqli_insert_id($con);

                if(isset($_FILES['files']) && count($_FILES['files']['name'])){
                    $allow = ['png', 'jpg', 'gif', 'pdf', 'jpeg', 'doc', 'docx', 'xls', 'xlsx'];
                    for ($i = 0; $i < count($_FILES['files']['name']); $i++){
                        $original_name = $_FILES['files']['name'][$i];
                        $parts = explode(".", $original_name);
                        $extension = end($parts);
                        if($_FILES['files']['error'][$i]) {
                            $errs[] = "Talebiniz oluşturuldu ancak " . $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Dilerseniz daha sonra talep detayından dosya yükleyebilirsiniz.";
                        }else if(!in_array(strtolower($extension), $allow)){
                            $errs[] = "Talebiniz oluşturuldu ancak " . $_FILES['files']['name'][$i] . " isimli dosya türüne izin verilemektedir. Dilerseniz daha sonra talep detayından dosya yükleyebilirsiniz.";
                        }else if($_FILES['files']['size'][$i] > 1000000){
                            $errs[] = "Talebiniz oluşturuldu ancak " . $_FILES['files']['name'][$i] . " isimli dosyanın boyutu 1MB değerinden yüksek olduğu için yüklenemdi. Dilerseniz daha sonra talep detayından dosya yükleyebilirsiniz.";
                        }else{
                            $name = time() . '-' . $original_name;
                            $destination = 'upload/'.$name;
                            $source = $_FILES['files']['tmp_name'][$i];

                            if(!move_uploaded_file($source, $destination)){
                                $errs[] = "Talebiniz oluşturuldu ancak " . $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Dilerseniz daha sonra talep detayından dosya yükleyebilirsiniz.";
                            }else{
                                $query = "INSERT INTO files (name, ticket_id, original_name) VALUES ('{$name}', '{$ticket_id}', '{$original_name}')";
                                if(!mysqli_query($con, $query)){
                                    $errs[] = "Talebiniz oluşturuldu ancak " . $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Dilerseniz daha sonra talep detayından dosya yükleyebilirsiniz.";
                                }
                            }
                        }
                    }
                }
                $konu = "Yeni Destek Talebi";
                $msg = "Siteniz üzerinden yeni bir talep aldınız. Talep detayları aşağıdadır. Sisteme giriş yaparak yanıtlayabilirsiniz.";
                $content = str_replace('\r\n', '<br>', nl2br($content));
                $msg .= '<table>
                            <tr>
                                <td>Kullanıcı</td>
                                <td>:</td>
                                <td>'.$user['name'].'</td>
                            </tr>
                            <tr>
                                <td>Konu</td>
                                <td>:</td>
                                <td>'.$subject.'</td>
                            </tr>
                            <tr>
                                <td>Talep</td>
                                <td>:</td>
                                <td>'.$content.'</td>
                            </tr>
                        </table>';
                mailgonder(ADMIN_MAIL, $konu, $msg);
}else{
                $errs[] = "Talebiniz kaydedilirken bir hata meydana geldi. Lütfen tekrar deneyiniz ya da bizimle iletişime geçiniz.";
            }
        }
    }
?>
<h1>Yeni Talep</h1>
<div class="clearfix"></div>
<a href="/taleplerim" class="btn btn-success pull-right"><i class="fa fa-list"></i> Taleplerim</a>
<div class="clearfix mb20"></div>
<div class="col-md-12">
    <?php if(isset($errs)){ ?>
        <?php if(count($errs)){ ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errs as $error){ ?>
                    <li><?php echo $error; ?></li>
                <?php } ?>
            </ul>
        </div>
        <?php }else{ ?>
        <div class="alert alert-success">
            Talebiniz kaydedilmiştir. En kısa sürede incelenecektir. Taleplerim sayfanızdan talebinizi takip edebilirsiniz.
        </div>
        <?php } ?>
    <?php } ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="subject">Konu *</label>
            <input type="text" name="subject" id="subject" class="form-control" required placeholder="Konu" maxlength="256">
        </div>
        <div class="form-group">
            <label for="content">Talebiniz *</label>
            <textarea id="content" name="content" rows="10" class="form-control" placeholder="Talebiniz" required></textarea>
        </div>
        <div class="form-group">
            <label for="files">Eklemek İstediğiniz Dosyalar</label>
            <input type="file" multiple id="files" name="files[]">
            <label for="files"><small>Birden çok dosya için ctrl tuşu ile birlikte seçebilirsiniz.<br>Sadece png, jpg, gif, pdf, doc, xls türünde dosyalar yükleyebilirsiniz.<br>Maksimum dosya boyutu 1MB'tır. 1MB'tan büyük dosyalar yükleyemezsiniz.</small></label>
        </div>
        <div class="form-group">
            <button type="submit" name="submit" value="submit" class="btn btn-primary form-control"><i class="fa fa-paper-plane"></i> Gönder</button>
        </div>
        <div class="form-group">
            <label>* işaretli alanların doldurulması zorunludur.</label>
        </div>
    </form>
</div>