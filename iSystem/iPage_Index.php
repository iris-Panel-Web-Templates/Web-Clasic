<?php
// ============================================================
// iPage_Index — Sayfa Düzeyinde GET/POST İstek Yöneticisi
// ============================================================
// Bu dosya index.php'den include edilir ve her sayfa yüklemesinde çalışır.
//
// POST İşlemleri:
//   /timezoneset   — Tarayıcı saat dilimini session'a kaydeder (iApi_UserAgent.php tarafından tetiklenir)
//
// GET İşlemleri:
//   ?s=logout      — Oturumu sonlandırır ve /home'a yönlendirir
//   ?s=profil      — Giriş yapılmamışsa /home'a yönlendirir (koruma)
//   ?s=pwchange    — Giriş yapılmamışsa /home'a yönlendirir (koruma)
//   ?s=characters  — Giriş yapılmamışsa /home'a yönlendirir (koruma)
//   ?s=accrecover  — Giriş yapılmamışsa /home'a yönlendirir (koruma)
//   ?token=XXXX    — Token türüne göre ilgili sayfaya yönlendirir (aktivasyon, şifre sıfırlama)
// ============================================================

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // :::::: (Check Post Data) ::::::
    $postData = file_get_contents('php://input');
    if(empty($postData)) {
        $response = [
            "ResponseCode"    => 1001,
            "ResponseMessage" => "POST NULL!",
            "ProcessTime"     => 0,
            "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
            "RequestQueryPage"=> $_GET['s'] ?? ""
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // :::::: (Kullanıcının Browser zaman dilimini kaydet) ::::::
    if($_SERVER['REQUEST_URI'] === '/timezoneset'  || (isset($_GET['s']) && $_GET['s'] === 'timezoneset')){
        $postJson = json_decode($postData);
        if (isset($postJson->timezone) && in_array($postJson->timezone, DateTimeZone::listIdentifiers())) {
            global $_SESSION;
            $_SESSION['user_timezone']=$postJson->timezone;
            setcookie('user_timezone', $postJson->timezone, time() + 3600 * 24 * 30, "/");

            $response = [
                "ResponseCode"    => 0,
                "ResponseMessage" => "Time zone seted. Zone: ".$postJson->timezone,
                "ProcessTime"     => 0,
                "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
                "RequestQueryPage"=> $_GET['s'] ?? ""
            ];
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    if (isset($_GET['s'])){
        // URL'de ?s=logout varsa, hemen logout işlemini yap
        if ($_GET['s'] === 'logout') {
            session_destroy();
            header("Location: ".(iFunctions::IsLocal() ? "?s=home" : "/home"));
            exit;
        }

        // Giriş gerektiren sayfalara yetkisiz erişimi engelle.
        // REQUEST_URI (production: /profil) ve GET['s'] (local: ?s=profil) her ikisi de kontrol edilir.
        if (!isset($_SESSION['account_id'])) {
            if ($_SERVER['REQUEST_URI'] === '/profil'     || $_GET['s'] === 'profil')     { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
            if ($_SERVER['REQUEST_URI'] === '/pwchange'   || $_GET['s'] === 'pwchange')   { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
            if ($_SERVER['REQUEST_URI'] === '/characters' || $_GET['s'] === 'characters') { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
            if ($_SERVER['REQUEST_URI'] === '/accrecover' || $_GET['s'] === 'accrecover') { header("location: ".(iFunctions::IsLocal() ? "?s=home" : "/home")); exit; }
        }
        // Her GET isteğinde oturumu tazele (Account_Refresh kendi içinde 30s throttle uygular).
        // Bu sayede kullanıcı herhangi bir sayfayı ziyaret ettiğinde güncel hesap verisi olur.
        if ( isset($_SESSION['account_id'])) {
            irisApi::$Account->Account_Refresh();
        }
    }

    // :::::: (Token İşlemleri) ::::::
    if (isset($_GET['token']) && strlen($_GET['token']) == 16) {
        $iToken = $_GET['token'];
        if (isset($_GET['s']) && $_GET['s'] == 'forgotpassword'){
            $tResult = irisApi::$Account->TokenInfo($iToken);
            if ($tResult->responseCode != 0) {
                header("Location: ".(iFunctions::IsLocal() ? "?s=home" : "/home"));
                exit;
            }
        }

        if (!isset($_GET['s'])) {
            $tResult = irisApi::$Account->TokenInfo($iToken);
            if ($tResult->responseCode == 0) {
                // 0:None, 1:Activation, 2:ForgetPassword
                if ($tResult->tokenInfo->tokenType == 0) { header("Location: ".(iFunctions::IsLocal() ? "?s=home"                         : "/home")); exit; }
                if ($tResult->tokenInfo->tokenType == 1) { header("Location: ".(iFunctions::IsLocal() ? "?s=activation&success=true"      : "/activation")); exit; }
                if ($tResult->tokenInfo->tokenType == 2) { header("Location: ".(iFunctions::IsLocal() ? "?s=forgotpassword&token".$iToken : "/forgotpassword?token=".$iToken)); exit; }
            }
        }

    }

}