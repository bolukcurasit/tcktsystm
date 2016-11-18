<h1>Taleplerim</h1>
<div class="clearfix"></div>
<a href="/yeni-talep" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Yeni Talep</a>
<div class="clearfix mb20"></div>
<?php
    $user = $_SESSION['user'];

    $query = "SELECT * FROM tickets WHERE user_id = {$user['id']} ORDER BY date DESC";

    $result = mysqli_query($con, $query);
    
    if(mysqli_num_rows($result) == 0){
?>
    <div class="alert alert-info">
        Henüz bir talebiniz bulunmamaktadır. <a href="/yeni-talep">Buraya</a> tıklayarak yeni talep oluşturabilirsiniz.
    </div>
<?php }else{ ?>
    <table class="table table-bordered">
        <tr>
            <th width="180">Talep Tarihi</th>
            <th>Talep Konusu</th>
            <th width="200">Talep Durumu</th>
            <th width="80">Detay</th>
        </tr>
        <?php $statuses = ['İncelenme Bekliyor', 'İşlem Yapılıyor', 'Cevaplandı', 'Kapatıldı']; ?>
        <?php while($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
            <td><?php echo $row['date'] ?></td>
            <td><?php echo $row['subject'] ?></td>
            <td><?php echo $statuses[$row['status']] ?></td>
            <td><a href="/talep-detay/<?php echo $row['id'] ?>" class="btn btn-sm btn-primary">Detay</a></td>
        </tr>
        <?php } ?>
    </table>
<?php } ?>