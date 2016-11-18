<?php
    if(isset($_POST['submit'])){
        $errs = [];
        
        if(!isset($_POST['username']) || empty($_POST['username'])){
            $errs[] = 'Email adresinizi giriniz.';
        }
        
        if(!isset($_POST['password']) || empty($_POST['password'])){
            $errs[] = 'Şifrenizi giriniz.';
        }
        
        if(!count($errs)){
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $password = md5(mysqli_real_escape_string($con, $_POST['password']));
            $query = "SELECT id, username, type, name FROM users WHERE username = '{$username}' AND password = '{$password}'";
            
            $result = mysqli_query($con, $query);
            
            if(mysqli_num_rows($result) != 1){
                $errs[] = "Kullanıcı bulunamadı.";
            }else{
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user'] = $user;
            }
        }        
    }

    if(check_login($con)){
        header("Location: /");
    }
?>
<div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
    <div class="well" id="login-form">
        <form action="/?v1=login" method="POST">
            <h2 class="text-center">Destek Sistemi V1.0.0</h2>
            <h4 class="text-center">Sistemi kullanabilmek için lütfen giriş yapınız.</h4>
            <div class="form-group">
                <label for="username">
                    <i class="fa fa-user"></i> Email Adresiniz
                </label>
                <input class="form-control" type="text" id="username" name="username" placeholder="Email Adresiniz">
            </div>
            <div class="form-group">
                <label for="password">
                    <i class="fa fa-lock"></i> Şifreniz
                </label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Şifreniz">
            </div>
            <div class="form-group">
                <button type="submit" name="submit" value="submit" class="form-control btn btn-primary">
                    Giriş <i class="fa fa-chevron-right"></i>
                </button>
            </div>
            <?php if(isset($errs) && count($errs)){ ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errs as $error){ ?>
                    <li><?php echo $error; ?></li>
                    <?php } ?>
                </ul>
            </div>
            <?php } ?>
        </form>
    </div>
</div>