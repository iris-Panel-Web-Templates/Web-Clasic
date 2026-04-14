<?php
	// session_start ();
	if (!isset($_SESSION['account_id'])) {
		include('loginmenu.inc.php');
	} else {
		echo "
		<div class='modul-box'>
			<div class='modul-box-bg'>
				<div class='modul-box-bg-bottom'>
					<h3>Kullanıcı</h3>
					<div style='text-align: center'>
						<br/><b>Selamlar,       </b> <span class='offset'>". $_SESSION['login_name'] ."</span><br/>
						<br/><b>Ejderha paranız:</b> <span class='offset'>". $_SESSION['cash']       ."</span><br/>
						<br/><b>Durum:          </b> <span class='offset'>". $_SESSION['status']     ."</span><br/>
						<br/><a style='margin: auto;' href='".(iFunctions::IsLocal() ? "?s=logout" : "/logout")."' class='btn'>Çıkış</a>
						<br/>
					</div>
				</div>
			</div>
		</div>
		<div class='boxes-middle'>&nbsp;</div>
		<div class='modul-box modul-box-2'>
			<div class='modul-box-bg'>
				<div class='modul-box-bg-bottom'>
					<ul class='main-nav' style='padding-bottom: 0'><li><a id='various2' href='?s=itemshop'>Nesne Market</a></li></ul>
					<a id='various3' href='https://shop.tugramt2.com?ltoken=".$_SESSION['shop_token']."' class='btn itemshop-btn'></a>
				</div>
			</div>
		</div>";
	}
?>

