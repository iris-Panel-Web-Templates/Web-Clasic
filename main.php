<?php
// ============================================================
// main.php — Ana Layout ve Sayfa Yönlendirici
// ============================================================
// Bu dosya index.php tarafından include edilir ve sitenin
// tüm görsel iskeletini oluşturur.
//
// SAYFA YÖNLENDİRME SİSTEMİ:
//   URL'deki ?s= parametresine göre main/ klasöründen ilgili .php
//   dosyası col-2 (içerik alanı) içine include edilir.
//   Örnek: ?s=rankings   → main/rankings.php dahil edilir
//          ?s=register   → main/register.php dahil edilir
//          (parametre yok veya hatalı) → main/home.php veya main/404.php
//
// URL FORMAT KURALI (tüm sayfalarda geçerli):
//   iFunctions::IsLocal() ? "?s=sayfaadi" : "/sayfaadi"
//   Local (PhpStorm/localhost) → sorgu parametresi   → index.php?s=home
//   Production (hosting)       → temiz URL (mod_rewrite) → /home
//
// 3 KOLONLU LAYOUT:
//   col-1 (sol)     → Ana menü, indirme butonu, istatistik widget
//   col-2 (merkez)  → ?s= parametresine göre değişen sayfa içeriği
//   col-3 (sağ)     → Küçük sıralama listesi (user/ranksmall.php) + login kutusu
//
// GİRİŞ DURUMUNA GÖRE DEĞİŞEN ALANLAR:
//   Header: Giriş yok → downloadmenu.php | Giriş var → upmenu.php
//   Menü : Giriş yok → "Kayıt" ve "Giriş Yap" linkleri gösterilir
// ============================================================
?>
<!--suppress CssUnusedSymbol -->
<style>
    .statisticsNav{ padding: 0 15px 0 10px; background: url(/img/modul-box-bg.jpg) no-repeat 0 100%; }
    .statisticsNav > li { width: 100%; height: 16px; line-height: 16px; border-radius: 3px; border: 1px solid transparent; }
    .statisticsNav > .title { background: url(/img/light_gf.gif) no-repeat; }

    .statisticsName  { float: left;  white-space: nowrap; font-size: 10px; text-align: left; padding-left:  5px; color: #515151; }
    .statisticsValue { float: right; white-space: nowrap; font-size: 10px; text-align: right;padding-right: 5px; color: #4a4a4a; font-weight: bold; }

    .iEmpire1{ padding-left: 25px; background-image: url(/img/1_s.png); background-repeat: no-repeat; background-position: left top; }
    .iEmpire2{ padding-left: 25px; background-image: url(/img/2_s.png); background-repeat: no-repeat; background-position: left top; }
    .iEmpire3{ padding-left: 25px; background-image: url(/img/3_s.png); background-repeat: no-repeat; background-position: left top; }
    input[type=text], input[type=password], input[type=number] { box-sizing: unset; }
</style>
<div id="page">
	<div class="header-wrapper">
		<div id="header">
			<a class="logo" style="margin-top:0;" href="<?= iFunctions::IsLocal() ? "?s=home": "/home" ?>"><strong>Metin2</strong></a>
			<?php
				if(!isset($_SESSION['account_id'])) {
					include('user/downloadmenu.php');
				} else { include('user/upmenu.php'); }
			?>
		</div>
	</div>
	<div class="container-wrapper">
		<div class="container">
			<!-- COL1 -->
			<div class="col-1">
				<div class="boxes-top">&nbsp;</div>
				<div class="modul-box">
					<div class="modul-box-bg">
						<div class="modul-box-bg-bottom">
							<ul class="main-nav">
								<li class="active"><a href="<?= iFunctions::IsLocal() ? "?s=home": "/home" ?>">Ana Sayfa</a></li>
								<li><a href="<?= iFunctions::IsLocal() ? "?s=thegame": "/thegame" ?>">Oyun hakkında</a></li>
								<?php if(!isset($_SESSION['account_id'])) {
                                    echo "<li><a href='". (iFunctions::IsLocal() ? "?s=register": "/register") ."'>Kayıt</a></li>";
                                    echo "<li><a href='". (iFunctions::IsLocal() ? "?s=login": "/login") ."'>Giriş Yap</a></li>";
                                } ?>
                                <li><a href="<?= iFunctions::IsLocal() ? "?s=download": "/download" ?>">İndir</a></li>
                                <li><a href="<?= iFunctions::IsLocal() ? "?s=howto": "/howto" ?>">İlk Adımlar</a></li>
                                <li><a href="<?= iFunctions::IsLocal() ? "?s=media": "/media" ?>">Galeri</a></li>
								<li><a href="/forum" target="_blank">Forum</a></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="boxes-middle">&nbsp;</div>

                <div class="modul-box modul-box-2">
                    <div class="modul-box-bg">
                        <div class="modul-box-bg-bottom">
                            <ul class="main-nav" style="padding-bottom: 0;"><li><a href="<?= iFunctions::IsLocal() ? "?s=download": "/download" ?>">İndir</a></li></ul>
                            <a href="<?= iFunctions::IsLocal() ? "?s=download": "/download" ?>" class="btn download-btn"></a>
                        </div>
                    </div>
                </div>
                <div class="boxes-bottom">&nbsp;</div>
                <div class="boxes-middle">&nbsp;</div>

                <div class="modul-box modul-box-2">
                    <div class="modul-box-bg">
                        <div class="modul-box-bg-bottom">
                            <ul class="main-nav" style="padding-bottom: 0;"><li><a href="javascript:void(0)">İstatistikler</a></li></ul>
                            <ul class="statisticsNav">
                                <?php
                                    $sData = irisApi::$Statistics->Statistics_Get();
                                    $sReel = $sData["Reel"];
                                    $sFake = $sData["Fake"];

                                    echo "<li class='title'><div class='statisticsName'>Toplam Online   </div><div class='statisticsValue'>{$sFake->online_Total}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire1'>Shinsoo</div><div class='statisticsValue'>{$sFake->online_Empires[1]}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire2'>Chunjo </div><div class='statisticsValue'>{$sFake->online_Empires[2]}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire3'>Jinno  </div><div class='statisticsValue'>{$sFake->online_Empires[3]}</div></li>";
                                    echo "<br/>";
                                    echo "<li class='title'><div class='statisticsName'>Toplam Oyuncu   </div><div class='statisticsValue'>{$sFake->player_Total}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire1'>Shinsoo</div><div class='statisticsValue'>{$sFake->player_Empires[1]}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire2'>Chunjo </div><div class='statisticsValue'>{$sFake->player_Empires[2]}</div></li>";
                                    echo "<li><div class='statisticsName iEmpire3'>Jinno  </div><div class='statisticsValue'>{$sFake->player_Empires[3]}</div></li>";
                                ?>
                            </ul>>

                        </div>
                    </div>
                </div>
                <div class="boxes-bottom">&nbsp;</div>
			</div>
			<div class="col-2">
				<?php
					if(!empty($_GET['s'])) {
						if(file_exists("./main/".$_GET['s'].".php")) {
							include("./main/".$_GET['s'].".php");
						} else { include("main/404.php"); }
					} else { include("main/home.php"); }
				?>
			</div>
			<div class="col-3"><?php include("user/ranksmall.php"); ?></div>
		</div>
	</div>
</div>