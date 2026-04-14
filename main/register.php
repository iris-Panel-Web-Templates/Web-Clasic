<?php
// ============================================================
// register.php — Kayıt Formu (Görsel)
// ============================================================
// Yalnızca HTML formunu gösterir. İşlem yapmaz.
//
// FORM AKIŞI:
//   register.php → POST → registerlog.php (action="?s=registerlog" veya "/registerlog")
//
// FORM ALANLARI → API KARŞILIĞI (SingUp parametreleri):
//   UserID    → $loginName    (giriş adı, 4-16 karakter, yalnızca harf+rakam)
//   UserName  → $userName     (oyun içi ad, 4-25 karakter)
//   Email     → $email        (e-posta)
//   Password  → $loginPass    (şifre, 4-16 karakter)
//   Password2 → doğrulama     (API'ye gönderilmez, PHP'de kontrol edilir)
//   DeleteCode→ $deleteCode   (karakter silme kodu, tam 7 karakter)
//
//   Formda BULUNMAYAN ama API'nin kabul ettiği alanlar:
//   $phone        = "" (boş bırakılmış, isteğe bağlı)
//   $pinCode      = "" (boş bırakılmış, isteğe bağlı)
//   $referanceName= "" (boş bırakılmış; referans kodu iApi_UserAgent cookie'den otomatik alınır)
//
// YENİ ALAN EKLEMEK İSTERSENİZ:
//   1. register.php'ye HTML input ekleyin
//   2. registerlog.php'de $_POST'tan okuyun
//   3. irisApi::$Account->SingUp() parametresine geçirin
// ============================================================
?>
<div id="register">
	<div class="content content-last">
		<div class="content-bg">
			<div class="content-bg-bottom">
				<h2>Metin2 - Kayıt</h2>
				<div class="inner-form-border">
					<div class="inner-form-box">
						<h3><a id="toLogin" href="<?= iFunctions::IsLocal() ? "?s=login": "/login" ?>" title="bejelentkezés">Giriş yap</a>Kayıt</h3>
						<div class="trenner"></div>
						<form  id="form2" name="form2" method="post" action="<?= iFunctions::IsLocal() ? "?s=registerlog": "/registerlog" ?>" onSubmit="return CheckValid(this)">
							<div class="center">
								<div class="form-item">
									<label for="UserID">Kullanıcı adı:</label>
									<input name="UserID" type="text" id="UserID" minlength="4" maxlength="16" size="16" value="" onFocus="change(1)" class="validate[required,custom[noSpecialCharacters],length[5,16]]" required />
								</div>
								<div class="form-item">
									<label for="UserName">Gerçek ad:</label>
									<input name="UserName" type="text" id="UserName" minlength="4" maxlength="25" size="25" value="" class="inputbox2" onFocus="change(5)" required />
								</div>
								<div class="form-item">
									<label for="Email">Email:</label>
									<input type="text" name="Email" id="Email" minlength="4" maxlength="25" size="25" required />
								</div>
								<div class="form-item">
									<label for="Password">Şifre:</label>
									<input name="Password" type="password" id="Password" minlength="4" maxlength="16" size="16" class="inputbox2" onFocus="change(2)" required />
								</div>
								<div class="form-item">
									<label for="Password2">Şifre tekrar:</label>
									<input name="Password2" type="password" id="Password2" minlength="4" maxlength="16" size="16" class="inputbox2" onFocus="change(2)" required />
								</div>
								<div class="form-item">
									<label for="DeleteCode">Karakter silme kodu:</label>
									<input type="text" name="DeleteCode" id="DeleteCode" minlength="7" maxlength="7" size="7" required />
								</div>
								<br />
								<input id="submitBtn" type="submit" name="SubmitRegisterForm" value="Kayıt" class="btn-big" />
							</div>
						</form>
						<p id="regLegend" align="left">
                            Tüm alanların doldurulması zorunludur!<br/>
                            Lütfen gerçek bir e-posta adresi girin!<br/>
                            Kaydolarak kabul etmiş olursunuz!<br/>
                            <a href="/imprint.html" target="_blank"><strong>kullanım şartları</strong></a>
                        </p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="shadow">&nbsp;</div>
