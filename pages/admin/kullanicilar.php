<?php
    if(isset($_POST['submit'])){
        $errs = [];

        if(!isset($_POST['username']) || empty($_POST['username'])){
            $errs[] = 'Email adresini giriniz.';
        }

        if(!isset($_POST['name']) || empty($_POST['name'])){
            $errs[] = 'İsim giriniz.';
        }

        if(!isset($_POST['password']) || empty($_POST['password'])){
            $errs[] = 'Şifre giriniz.';
        }

        if(!isset($_POST['password2']) || empty($_POST['password2'])){
            $errs[] = 'Şifre onayını giriniz.';
        }

        if($_POST['password'] != $_POST['password2']){
            $errs[] = 'Şifre ve şifre onayı eşleşmiyor.';
        }

        if(!count($errs)){
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $name = mysqli_real_escape_string($con, $_POST['name']);
            $password = md5(mysqli_real_escape_string($con, $_POST['password']));

            $query = "INSERT INTO users (name, username, password) VALUES ('{$name}', '{$username}', '{$password}')";

            if(!mysqli_query($con, $query)){
                $errs[] = 'Kullanıcı eklenirken bir hata meydana geldi.';
            }
        }
    }
?>
<h1>Kullanıcılar</h1>
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
            Kullanıcı kaydedilmiştir.
        </div>
    <?php } ?>
<?php } ?>
<h4>Kullanıcı Ekle</h4>
<form action="" method="post">
    <div class="form-group">
        <label for="username">Email Adresi *</label>
        <input type="text" name="username" id="username" placeholder="Email Adresi" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="name">İsim *</label>
        <input type="text" name="name" id="name" placeholder="İsim" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password">Şifre *</label>
        <input type="password" name="password" id="password" placeholder="Şifre" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password2">Şifre Onayı *</label>
        <input type="password" name="password2" id="password2" placeholder="Şifre Onayı" class="form-control" required>
    </div>
    <div class="form-group">
        <button name="submit" value="submit" class="btn btn-primary form-control">
            Ekle <i class="fa fa-chevron-right"></i>
        </button>
    </div>
</form>
<h4>Mevcut Kullanıcılar</h4>
<?php
    $users = mysqli_query($con, "SELECT * FROM users WHERE type = 1 ORDER BY create_date ASC");
    if(mysqli_num_rows($users) == 0){
?>
    <div class="alert alert-info">Eklenmiş kullanıcı bulunmamaktadır.</div>
<?php }else{ ?>
        <table class="table table-bordered">
            <tr>
                <th>İsim</th>
                <th>Email</th>
                <th>Düzenle</th>
                <th>Sil</th>
            </tr>
            <?php while($user = mysqli_fetch_assoc($users)){ ?>
            <tr>
                <td><?php echo $user['name'] ?></td>
                <td><?php echo $user['username'] ?></td>
                <td><a class="btn btn-primary btn-sm" href="/kullanici-duzenle/<?php echo $user['id']; ?>">Düzenle</a></td>
                <td><a class="btn btn-danger btn-sm beSure" href="/kullanici-sil/<?php echo $user['id']; ?>">Sil</a></td>
            </tr>
            <?php } ?>
        </table>
<?php } ?>
