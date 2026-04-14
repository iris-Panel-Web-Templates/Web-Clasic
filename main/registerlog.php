<?php
// ============================================================
// registerlog.php — Kayıt POST İşleyicisi
// ============================================================
// register.php formundan gelen POST isteğini işler.
//
// AKIŞ:
//   1. $_POST alanlarını oku
//   2. PHP tarafında doğrulama yap (uzunluk, format, şifre eşleşme)
//   3. Hata varsa → geri dön butonu ile hata mesajı göster (exit)
//   4. Hata yoksa → irisApi::$Account->SingUp() çağır
//   5. API hatası varsa → responseCode + responseMessage göster (exit)
//   6. Başarılıysa → kayıt özeti göster + indirme sayfasına yönlendirme butonu
//
// DOĞRULAMA KURALLARI:
//   loginName  : 3-16 karakter, yalnızca harf ve rakam (ctype_alnum)
//   loginPass  : 4-16 karakter, yalnızca harf ve rakam
//   email      : 4-25 karakter, geçerli e-posta formatı (FILTER_VALIDATE_EMAIL)
//   userName   : 4-25 karakter
//   deleteCode : tam 7 karakter, yalnızca harf ve rakam
//   Password == Password2 kontrolü
//
// NOT: API ayrıca sunucu tarafında da doğrulama yapar.
//      PHP kontrolü yalnızca gereksiz API çağrısını önlemek içindir.
//
// FORM İŞLEME DESENLERİ (İKİ YÖNTEM):
//   Desen A — Tek dosya  (login.php örneği):
//     Aynı dosya hem GET (form) hem POST (işlem) isteğini karşılar.
//     Hata durumunda $_SESSION['last_error'] ile redirect yapılır.
//     Kısa formlar ve anlık geri bildirim gereken durumlar için uygundur.
//
//   Desen B — İki dosya  (bu dosya örneği, register.php → registerlog.php):
//     form.php    → yalnızca HTML formu gösterir
//     formlog.php → yalnızca POST işler, sonucu HTML ile gösterir
//     Daha uzun formlar veya sonuç sayfasının farklı görünmesi gerektiğinde uygundur.
// ============================================================
?>
<div class="content content-last">
	<div class="content-bg">
		<div class="content-bg-bottom">
			<div class="administration-inner-content">
				<div class="input-data-box2">
					<h2>Metin2 - Kayıt</h2>
					<?php
                    $loginName     = $_POST["UserID"];
                    $loginPass1    = $_POST["Password"];
                    $loginPass2    = $_POST["Password2"];
                    $email         = $_POST["Email"];
                    $phone         = "";
                    $pinCode       = "";
                    $userName      = $_POST["UserName"];
                    $deleteCode    = $_POST["DeleteCode"];
                    $referanceName = "";
                    $error = "";
                    if (strlen($loginName)< 3 || strlen($loginName)> 16 || !ctype_alnum($loginName))                        { $error = "Kullanıcı adı hatalı!<br/>En fazla 16 karakter, en az 3.<br/>"; }
                    if (strlen($loginPass1) < 4 || strlen($loginPass1) > 16 || !ctype_alnum($loginPass1))                   { $error = "<br/>Şifre hatalı!<br/>En fazla 16 karakter, en az 5 karakter.<br/>"; }
                    if (strlen($email)    < 4 || strlen($email)    > 25 || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = "<br/>E-posta adresi hatalı!<br/>En fazla 25 karakter, ancak en az 5.<br/>"; }
                    if (strlen($userName) < 4 || strlen($userName) > 25 )      { $error= "<br/>İsim yanlış!<br/>En fazla 16 karakter, ancak en az 5.<br/>"; }
                    if (strlen($deleteCode) != 7 || !ctype_alnum($deleteCode)) { $error= "<br/>Karakter silme kodunun 7 karakter uzunluğunda olması gerekiyor!<br/>"; }
                    if ($loginPass1 != $loginPass2) { $error="Şifreler uyuşmuyor!<br/>"; }
                    if (strlen($error) > 0) {
                        echo "<strong>$error</strong><a class='btn' href=\"javascript:history.back();\">Geri</a><br />";
                        exit;
                    }

                    $cResult = irisApi::$Account->SingUp($loginName, $loginPass1, $email, $phone, $userName, $pinCode, $deleteCode, $referanceName);
                    if ($cResult->responseCode != 0) {
                        echo "<br/>
                            <strong style='color: brown;'>Hata Kodu: </strong>$cResult->responseCode<br/>
                            <strong style='color: brown;'>Hata Mesaj: </strong>$cResult->responseMessage<br/><br/>
                            <a class='btn' href=\"javascript:history.back();\">Geri</a><br />";
                        exit;
                    }

					?>
					<br /><h4 style="font-size: 16px">Kayıt başarılı!</h4>
					<ul>
						<li>Kullanıcı adı:		<?= $loginName?></li>
						<li>Şifre:		<?= $loginPass1?></li>
						<li>Email:		<?= $email?></li>
						<li>Gerçek Ad:		<?= $userName?></li>
						<li>Karakter Silme Kodu:		<?= $deleteCode?></li>
					</ul>
					<div class="administration-box"><a href="<?= iFunctions::IsLocal() ? "?s=download": "/download" ?>" class="btn">İndir</a></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="shadow">&nbsp;</div>
