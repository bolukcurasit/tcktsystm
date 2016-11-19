<?php
if(!isset($_GET['v2']))
    header("Location: /not-found");

$id = $_GET['v2'];

$user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE id = {$id}"));
?>
<?php
if(isset($_POST['submit'])){
    $errs = [];

    if(!isset($_POST['username']) || empty($_POST['username'])){
        $errs[] = 'Email adresini giriniz.';
    }

    if(!isset($_POST['name']) || empty($_POST['name'])){
        $errs[] = 'İsim giriniz.';
    }


    if(isset($_POST['password']) && !empty($_POST['password'])) {

        if (!isset($_POST['password2']) || empty($_POST['password2'])) {
            $errs[] = 'Şifre onayını giriniz.';
        }

        if ($_POST['password'] != $_POST['password2']) {
            $errs[] = 'Şifre ve şifre onayı eşleşmiyor.';
        }
    }

    if(!count($errs)){
        $username = mysqli_real_escape_string($con, $_POST['username']);
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $password = md5(mysqli_real_escape_string($con, $_POST['password']));

        $query = "UPDATE users SET username = '{$username}', name = '{$name}'";
        if(isset($_POST['password']) && !empty($_POST['password'])) {
            $query .= ", password = '{$password}'";
        }

        $query .= " WHERE id ={$id} LIMIT 1";

        if(!mysqli_query($con, $query)){
            $errs[] = 'Kullanıcı güncellenirken bir hata meydana geldi.';
        }
    }
    $user = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE id = {$id}"));
}
?>

<h1>Kullanıcı Düzenle</h1>
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
            Kullanıcı güncellenmiştir.
        </div>
    <?php } ?>
<?php } ?>
<form action="" method="post">
    <div class="form-group">
        <label for="username">Email Adresi *</label>
        <input type="text" name="username" id="username" value="<?php echo $user['username'] ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="name">İsim *</label>
        <input type="text" name="name" id="name" value="<?php echo $user['name'] ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="password"><small>Şifresini değiştirmek istemiyorsanız aşağıdaki alanları boş bırakın.</small></label><br>
        <label for="password">Şifre</label>
        <input type="password" name="password" id="password" placeholder="Şifre" class="form-control">
    </div>
    <div class="form-group">
        <label for="password2">Şifre Onayı</label>
        <input type="password" name="password2" id="password2" placeholder="Şifre Onayı" class="form-control">
    </div>
    <div class="form-group">
        <button name="submit" value="submit" class="btn btn-primary form-control">
            <i class="fa fa-refresh"></i> Güncelle
        </button>
    </div>
</form>