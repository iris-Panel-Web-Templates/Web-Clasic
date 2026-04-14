<div class="center">
	<div id="userBox">
		<br/>
		<ul class="header-box-nav-login" style="margin-left:15px;">
			<li class="stepdown"><a id='various4' href='https://shop.tugramt2.com?ltoken=<?= $_SESSION['shop_token'] ?>' class="nav-box-btn nav-box-btn-1">Nesne Market</a></li>
			<li class="stepdown"><a href="<?= iFunctions::IsLocal() ? "?s=profil": "/profil" ?>" class="nav-box-btn nav-box-btn-2">Profil</a></li>
			<li class="stepdown"><a href="<?= iFunctions::IsLocal() ? "?s=logout": "/logout" ?>" class="nav-box-btn nav-box-btn-4">Çıkış</a></li>
		</ul>
	</div>
</div>
