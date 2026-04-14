<?php
// ============================================================
// login.php — Giriş Formu + POST İşleyicisi (Tek Dosya Deseni)
// ============================================================
// Bu dosya hem formu gösterir hem de POST isteğini işler.
// Register (iki dosya) yerine bu "tek dosya" deseni tercih edilmiştir.
//
// AKIŞ:
//   GET  isteği → Formu göster (hata varsa $_SESSION['last_error'] mesajı)
//   POST isteği → Login API çağır:
//     Başarısız → $_SESSION['last_error']'a mesajı yaz, ?fail=true ile aynı sayfaya yönlendir
//     Başarılı  → /profil sayfasına yönlendir (SessionSet otomatik çalışır)
//
// HATA GÖSTERİM DESENİ:
//   POST sonucu redirect + session ile taşınır:
//   $_SESSION['last_error'] = "hata mesajı";
//   header("Location: ...?fail=true");
//   Sayfada: if (isset($_GET['fail'])) { echo $_SESSION['last_error']; }
//   Bu desen, POST → Redirect → GET akışı sağlar (sayfa yenilemede çift POST olmaz).
//
// FORM ALANLARI → API KARŞILIĞI:
//   loginName → $loginName  (kullanıcı adı)
//   loginPass → $loginPass  (şifre)
//   pinCode   → ""          (burada boş; gerekirse forma PIN alanı eklenebilir)
// ============================================================
?>

<div id="login">
	<div class="content content-last">
		<div class="content-bg">
			<div class="content-bg-bottom">
				<h2>Metin2 - Giriş</h2>
				<div class="inner-form-border">
					<div class="inner-form-box">
						<h3><a id="topwLost" href="<?= iFunctions::IsLocal() ? "?s=passwordlost": "/passwordlost" ?>" title="Elfelejtett jelszó?">Parolanızı mı unuttunuz?</a>Giriş</h3>
						<div class="trenner"></div>
						<form name="loginForm" id="loginForm" action="<?= iFunctions::IsLocal() ? "?s=login": "/login" ?>" method="post">
							<div>
								<label for="username">Kullanıcı adı: *</label>
								<input type="text" class="validate[required,custom[noSpecialCharacters],length[3,16]]" id="username" name="loginName" maxlength="16" value="" />
							</div>
							<div>
								<label for="password">Şifre: *</label>
								<input type="password" class="validate[required,length[5,16]]" id="password" name="loginPass" maxlength="16" value="" />
							</div>
							<div id="checkerror">
								<p>Giriş yaparak <a href="/imprint.html" target="_blank"><strong>kullanım şartları</strong></a>'nı kabul etmiş olursunuz!</p>
							</div>
							<input id="submitBtn" class="btn-big" type="submit" name="login" value="Login" />
							<?php
								if (isset($_GET['fail'])) {
									echo "<div class='center'><span style='color:darkred; font-size:16px'><strong>".$_SESSION['last_error']."</strong></span></div>";
                                    $_SESSION['last_error'] = "";
								}
							?>
						</form>
						<p id="regLegend">* gerekli</p>
						<div class="trenner"></div>
						<div id="subscribe">
							<h3>Henüz bir hesabınız yok mu?</h3>
							<p style="margin-top: 0;">Kayıt işlemi basit, hızlı ve ücretsizdir.</p>
							<a class="btn-big" href="<?= iFunctions::IsLocal() ? "?s=register": "/register" ?>" title="Bir hesap oluşturun">Kayıt</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="shadow">&nbsp;</div>
</div>
<?php
	if(isset($_POST['login']) && $_POST['login'] == 'Login') {
        $lResult = irisApi::$Account->Login($_POST['loginName'], $_POST['loginPass'], "");
		if($lResult->responseCode != 0) {
            $_SESSION['last_error'] = $lResult->responseMessage;
            header("Location: ".(iFunctions::IsLocal()?"?s=login&fail=true":"/login?fail=true"));
            exit;
		}
        header("Location: ".(iFunctions::IsLocal()?"?s=profil":"/profil"));
	}
?>