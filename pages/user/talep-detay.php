<?php
    if(!isset($_GET['v2']))
        header("Location: /not-found");

    $id = $_GET['v2'];
    $user = $_SESSION['user'];

    $query = "SELECT * FROM tickets WHERE id = {$id} AND user_id ={$user['id']}";

    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) != 1)
        header("Location: /not-found");

    $ticket = mysqli_fetch_assoc($result);

    $statuses = ['İncelenme Bekliyor', 'İşlem Yapılıyor', 'Cevaplandı', 'Kapatıldı'];
?>
<?php
    if(isset($_POST['submit'])){
        $errs = [];
        if(!isset($_POST['content']) || empty($_POST['content'])){
            $errs[] = "Lütfen mesajınızı giriniz.";
        }

        if(!count($errs)){
            $content = mysqli_real_escape_string($con, strip_tags($_POST['content']));
            $query = "INSERT INTO messages (ticket_id, user_id, content) VALUES ({$id}, {$user['id']}, '{$content}')";

            if(!mysqli_query($con, $query)){
                $errs[] = 'Mesajınız iletilirken bir hata meydana geldi. Lütfen daha sonra tekrar deneyiniz veya bizimle iletişime geçiniz.'.mysqli_error($con);
            } else {
                $konu = "Yeni Mesaj";
                $msg = "Destek taleplerinden birine yeni bir mesaj yazılmıştır. Mesaj detayları aşağıdadır. Sisteme giriş yaparak inceleyebilirsiniz.";
                $content = str_replace('\r\n', '<br>', nl2br($content));
                $msg .= '<table>
                            <tr>
                                <td>Kullanıcı</td>
                                <td>:</td>
                                <td>' . $user['name'] . '</td>
                            </tr>
                            <tr>
                                <td>Mesaj</td>
                                <td>:</td>
                                <td>' . $content . '</td>
                            </tr>
                        </table>';
                mailgonder(ADMIN_MAIL, $konu, $msg);
            }
        }
    }

    if(isset($_POST['f_submit'])){
        $errs = [];

        if(isset($_FILES['files']) && count($_FILES['files']['name'])){
            $allow = ['png', 'jpg', 'gif', 'pdf', 'jpeg', 'doc', 'docx', 'xls', 'xlsx'];
            for ($i = 0; $i < count($_FILES['files']['name']); $i++){
                $original_name = $_FILES['files']['name'][$i];
                $parts = explode(".", $original_name);
                $extension = end($parts);
                if($_FILES['files']['error'][$i]) {
                    $errs[] = $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Lütfen tekrar deneyiniz veya bizimle iletişime geçiniz.";
                }else if(!in_array(strtolower($extension), $allow)){
                    $errs[] = $_FILES['files']['name'][$i] . " isimli dosya türüne izin verilemektedir.";
                }else if($_FILES['files']['size'][$i] > 1000000){
                    $errs[] = $_FILES['files']['name'][$i] . " isimli dosyanın boyutu 1MB değerinden yüksek olduğu için yüklenemdi.";
                }else{
                    $name = time() . '-' . $original_name;
                    $destination = 'upload/'.$name;
                    $source = $_FILES['files']['tmp_name'][$i];

                    if(!move_uploaded_file($source, $destination)){
                        $errs[] = $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Lütfen tekrar deneyiniz veya bizimle iletişime geçiniz.";
                    }else{
                        $query = "INSERT INTO files (name, ticket_id, original_name) VALUES ('{$name}', '{$ticket['id']}', '{$original_name}')";
                        if(!mysqli_query($con, $query)){
                            $errs[] = $_FILES['files']['name'][$i] . " isimli dosya yüklenemedi. Lütfen tekrar deneyiniz veya bizimle iletişime geçiniz.";
                        }
                    }
                }
            }
        }else{
            $errs[] = "Lütfen dosya seçiniz.";
        }
    }
?>
<h1>Talep Detayları</h1>
<?php if(isset($errs)){ ?>
    <div class="col-md-12">
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
            İşlem başarılı.
        </div>
    <?php } ?>
    </div>
<?php } ?>
<div class="col-md-4">
    <h4>Eklenmiş Dosyalar</h4>
    <?php
    $query = "SELECT * FROM files WHERE ticket_id = '{$id}'";
    $results = mysqli_query($con, $query);
    if(mysqli_num_rows($results) > 0){
        ?>
        <ul>
            <?php while($file = mysqli_fetch_assoc($results)){ ?>
                <li><a href="/upload/<?php echo $file['name']; ?>" target="_blank"><?php echo $file['original_name']; ?></a></li>
            <?php } ?>
        </ul>
    <?php }else{ ?>
    <div class="alert alert-warning">
        Eklenmiş dosya bulunmamaktadır.
    </div>
    <?php } ?>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="files">Dosya Ekle</label>
            <input type="file" multiple id="files" name="files[]">
            <label for="files"><small>Birden çok dosya için ctrl tuşu ile birlikte seçebilirsiniz.<br>Sadece png, jpg, gif, pdf, doc, xls türünde dosyalar yükleyebilirsiniz.<br>Maksimum dosya boyutu 1MB'tır. 1MB'tan büyük dosyalar yükleyemezsiniz.</small></label>
        </div>
        <div class="form-group">
            <button type="submit" name="f_submit" value="f_submit" class="btn btn-warning form-control"><i class="fa fa-file"></i> Dosya Ekle</button>
        </div>
    </form>
</div>
<div class="col-md-8">
    <h4>Talep Detayları</h4>
    <div class="alert alert-info">
        <table class="table table-bordered border-white">
            <tr>
                <th width="120">Tarih</th>
                <td><?php echo date("d.m.Y H:i:s", strtotime($ticket['date'])); ?></td>

            </tr>
            <tr>
                <th>Durum</th>
                <td>
                    <?php echo $statuses[$ticket['status']]; ?>
                </td>
            </tr>
            <tr>
                <th width="120">Konu</th>
                <td><?php echo $ticket['subject']; ?></td>

            </tr>
            <tr>
                <th>Talep</th>
                <td>
                    <?php echo nl2br($ticket['content']); ?>
                </td>
            </tr>
        </table>
    </div>
</div>
<div class="clearfix mb20"></div>
<?php
    $query = "SELECT * FROM messages WHERE ticket_id = {$id} ORDER BY date ASC";
    $results = mysqli_query($con, $query);

    while($message = mysqli_fetch_assoc($results)){
        $m_user = mysqli_fetch_assoc(mysqli_query($con, "SELECT name, name FROM users WHERE id = {$message['user_id']}"));
?>
    <div class="col-md-7 alert alert-<?php echo $user['id'] == $message['user_id'] ? 'success pull-left' : 'warning pull-right'  ?>">
        <table class="table table-bordered border-white">
            <tr>
                <td>
                    <?php echo date("d.m.Y H:i:s", strtotime($message['date'])); ?> tarihinde <?php echo $m_user['name'] ?> tarafindan yazılmıştır.
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo nl2br($message['content']); ?>
                </td>
            </tr>
        </table>
    </div>
    <div class="clearfix mb20"></div>
<?php } ?>
<h4>Yanıtla</h4>
<form method="post" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="content">Mesajınız *</label>
        <textarea id="content" name="content" rows="10" class="form-control" placeholder="Mesajınız" required></textarea>
    </div>
    <div class="form-group">
        <button type="submit" name="submit" value="submit" class="btn btn-primary form-control"><i class="fa fa-paper-plane"></i> Gönder</button>
    </div>
    <div class="form-group">
        <label>* işaretli alanların doldurulması zorunludur.</label>
    </div>
</form>

