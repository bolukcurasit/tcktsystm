<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Menü</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Destek Sistemi V1.0.0</a>
        </div>
        <?php if(isset($_SESSION['user'])){ ?>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php if($_SESSION['user']['type'] == 0){ ?>
                <li<?php echo $sayfa == 'gelen-talepler' ? ' class="active"' : ''; ?>><a href="/cikis"><i class="fa fa-list"></i> Gelen Talepler <span class="badge">2</span></a></li>
                <li<?php echo $sayfa == 'kullanicilar' ? ' class="active"' : ''; ?>><a href="/kullanicilar"><i class="fa fa-users"></i> Kullanıcılar</a></li>
                <?php }else{ ?>
                <li<?php echo $sayfa == 'taleplerim' ? ' class="active"' : ''; ?>><a href="/taleplerim"><i class="fa fa-list"></i> Taleplerim</a></li>
                <li<?php echo $sayfa == 'yeni-talep' ? ' class="active"' : ''; ?>><a href="/yeni-talep"><i class="fa fa-plus"></i> Yeni Talep</a></li>
                <?php } ?>
                <li><a href="/cikis"><i class="fa fa-sign-out"></i> Çıkış</a></li>
            </ul>
        </div><!--/.nav-collapse -->
        <?php } ?>
    </div>
</nav>