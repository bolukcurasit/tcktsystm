<h1>Gelen Talepler</h1>
<?php
$page = 1;
if(isset($_GET['v2'])){
    $page = $_GET['v2'];
}
$kayit_sayisi = 50;

$query = "SELECT * FROM tickets ORDER BY date DESC";
$page_count = ceil(mysqli_num_rows(mysqli_query($con, $query)) / $kayit_sayisi);
$alt = ($page - 1) * $kayit_sayisi;


$user = $_SESSION['user'];

$query = "SELECT * FROM tickets ORDER BY date DESC LIMIT {$alt}, {$kayit_sayisi}";

$result = mysqli_query($con, $query);

if(mysqli_num_rows($result) == 0){
    ?>
    <div class="alert alert-info">
        Henüz bir talep bulunmamaktadır.
    </div>
<?php }else{ ?>
    <table class="table table-bordered">
        <tr>
            <th width="180">Kullanıcı</th>
            <th width="180">Talep Tarihi</th>
            <th>Talep Konusu</th>
            <th width="200">Talep Durumu</th>
            <th width="80">Detay</th>
        </tr>
        <?php $statuses = ['İncelenme Bekliyor', 'İşlem Yapılıyor', 'Cevaplandı', 'Kapatıldı']; ?>
        <?php while($row = mysqli_fetch_assoc($result)){ ?>
            <?php $t_user = mysqli_fetch_assoc(mysqli_query($con, "SELECT name FROM users WHERE id = {$row['user_id']}"));?>
            <tr<?php echo $row['status'] == 0 ? ' style="font-weight:bold;"' : '' ?>>
                <td><?php echo $t_user['name'] ?></td>
                <td><?php echo $row['date'] ?></td>
                <td><?php echo $row['subject'] ?></td>
                <td><?php echo $statuses[$row['status']] ?></td>
                <td><a href="/talep-detay/<?php echo $row['id'] ?>" class="btn btn-sm btn-primary">Detay</a></td>
            </tr>
        <?php } ?>
    </table>
    <?php if($page_count > 1){ ?>
    <div class="col-md-12 text-center">
        <ul class="pagination">
            <?php
            for($i = 1; $i <= $page_count; $i++){
                ?>
                <li><a href="/talepler/<?php echo $i ?>"><?php echo $i ?></a></li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php } ?>
<?php } ?>