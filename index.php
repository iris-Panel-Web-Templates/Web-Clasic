<?php
    // :::::: (Session Güvenlik Ayarları) ::::::
    // Aşağıdaki blok, session çerezini daha güvenli hale getirir.
    // Yalnızca uzun süreli oturum gerekiyorsa etkinleştirin (ör. "beni hatırla" özelliği).
    //session_set_cookie_params([
    //    'lifetime' => 60*60*24*365, // Session ve çerezler için saklanma süresi (saniye)
    //    'path'     => '/',
    //    'domain'   => $_SERVER['HTTP_HOST'],
    //    'secure'   => true,   // Yalnızca HTTPS üzerinden erişilebilmesini sağlar
    //    'httponly' => true,   // JavaScript ve 3. taraf scriptlerin session çerezine erişimini engeller
    //    'samesite' => 'Strict'
    //]);
    session_start();

    // :::::: (Tarayıcı Parmak İzi / Güvenlik Test Çerezi) ::::::
    // "power-x" çerezi, istemcinin güvenli çerez desteğini (Secure + HttpOnly + SameSite) test etmek
    // amacıyla atılır. İçeriği işlevsel değil; varlığı ve ayarları önemlidir.
    // Örneğin: CSRF koruması veya bot tespiti için bu çerezin varlığı kontrol edilebilir.
    setcookie("power-x", "none", [
        'expires'  => time() + (60*60*24*30),
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'], // Dikkat: subdomain dahil edilmek isteniyorsa başına "." ekleyin
        'secure'   => true,   // Yalnızca HTTPS bağlantılarında gönderilir
        'httponly' => true,   // JavaScript ile okunamaz (XSS koruması)
        'samesite' => 'Strict' // Başka sitelerden yapılan isteklerde çerez gönderilmez (CSRF koruması)
    ]);

    include("iSystem/iApi.php");
    include("iSystem/iPage_Index.php");

    // Çıktı tamponlamayı başlat.
    // Tüm echo/print çıktıları önce belleğe alınır; header() çağrıları içerik yazıldıktan sonra da çalışır.
    // Sayfa tamamlandığında ob_end_flush() ile tampon tarayıcıya gönderilir.
    ob_start();
?>

<!DOCTYPE html>
<html lang="tr" >
	<!--suppress HtmlRequiredTitleElement -->
    <head>
		<?php include("./head.php"); ?>
        <?php irisApi::$UserAgent->IncludePage(); ?>
    </head>
	<body>
		<?php
			include("./main.php");
			include("./footer.php");
		?>
	</body>
</html>
<?php ob_end_flush(); // Çıktı tamponlamayı tamamla ?>
