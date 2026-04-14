<?php
// ============================================================
// UserAgent_Class — Tarayıcı Saat Dilimi ve Referans Kodu Yönetimi
// ============================================================
// Bu modül iki işlevi yerine getirir:
//
// 1. SAAT DİLİMİ TESPİTİ:
//    Sunucu tarafında tarayıcı saat dilimine erişilemez.
//    Bu nedenle JavaScript'in Intl API'si ile saat dilimi alınır
//    ve /timezoneset endpoint'ine POST ile gönderilir.
//    iPage_Index.php bu isteği yakalar ve $_SESSION['user_timezone']'a yazar.
//    Bir kez kaydedildikten sonra her sayfada tekrar gönderilmez.
//
// 2. REFERANS KODU AKIŞI:
//    Referans kodu, kullanıcının hangi kişi/kampanya üzerinden geldiğini takip eder.
//    Öncelik sırası:
//      a) URL parametresi (?referancecode=XXXX) → localStorage'ye yaz (yalnızca boşsa)
//      b) localStorage'deki değer → Cookie'ye taşı (PHP okuyabilsin diye)
//      c) Cookie → Session'a kopyala ve döndür
//      d) Session → Cookie'yi yenile ve döndür
//    Kayıt işleminde irisApi::$UserAgent->ReferanceCode_Get() çağrılarak
//    bu kod Account/Create isteğine eklenir.
//
// NOT: Referans kodu açık kaynak kodda (sayfa kaynağında) görünebilir.
//      Daha güvenli bir yaklaşım için kodun şifreli şekilde sayfaya
//      yazılması ve sunucu tarafında çözülmesi önerilir.
// ============================================================

class UserAgent_Class {

    // <head> bölümüne dahil edilecek JavaScript parçacıklarını üretir.
    // - Saat dilimi henüz bilinmiyorsa tespit scriptini ekler.
    // - Referans kodu akışını (URL → localStorage → Cookie) yönetir.
    // Bu metod index.php'den irisApi::$UserAgent->IncludePage() ile çağrılır.
    public static function IncludePage(): void {
        global $_SESSION;
        $userTimezone = $_SESSION['user_timezone'] ?? 'UTC';

        // Saat dilimi daha önce kaydedilmemişse tarayıcıdan al ve gönder.
        if ($userTimezone == 'UTC') {
            echo "
                <script type='text/javascript' language='javascript' defer='defer' title='useragent_timezone'>
                    const timeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    fetch('".(iFunctions::IsLocal() ? "index.php?s=timezoneset" : "/timezoneset")."', {
                    method: 'POST', headers: {'Content-Type': 'application/json'},
                    body  : JSON.stringify({ timezone: timeZone })
                    }).then(() => { });
                </script>";
        }

        // NOT: Referans kodu sayfa kaynağında görünmektedir.
        // Daha güvenli bir yapı için şifreli token kullanımı önerilir.

        // :::::: (URL'de ReferanceCode var ise, LocalStorage'de ReferanceCode yok ise LocalStorage'ye yaz) ::::::
        // Aynı cihazda daha önce başka bir referans kodu varsa üzerine yazılmaz.
        if (isset($_GET['referancecode']) && strlen($_GET['referancecode']) == 16){
            echo "
            <script type='text/javascript' language='javascript' defer='defer'>
                if (localStorage.getItem('referance_code') === '' || localStorage.getItem('referance_code') === null || localStorage.getItem('referance_code') === undefined)
                    localStorage.setItem('referance_code', '".$_GET['referancecode']."');
            </script>";
        }

        // :::::: (LocalStorage'de ReferanceCode var ise Cookie'e ekle) ::::::
        // PHP localStorage'i okuyamaz; cookie üzerinden aktarım yapılır.
        if (self::ReferanceCode_Get() === "") {
            echo "
            <script type='text/javascript' language='javascript' defer='defer'>
                let token = localStorage.getItem('referance_code');
                if (token !== \"\" && token !== null && token !== undefined)
                    document.cookie = \"referance_code=\"+token+\"; path=/; max-age=3600; samesite=Lax\";
            </script>";
        }
    }

    // Aktif referans kodunu döndürür. Kaynak önceliği: Cookie → Session → boş string.
    // Cookie varsa Session'a da kopyalanır (tutarlılık için).
    // Session varsa Cookie 1 yıl uzatılır (yenileme).
    // Dönüş: string — 16 karakterlik referans kodu veya "" (kod yok)
    public static function ReferanceCode_Get(): string {
        global $_COOKIE;
        global $_SESSION;
        if (isset($_COOKIE["referance_code"]))  {
            $_SESSION['referance_code'] = $_COOKIE["referance_code"];
            return $_COOKIE["referance_code"];
        }
        if (isset($_SESSION['referance_code'])) {
            setcookie("referance_code", $_SESSION['referance_code'], time() + (60*60*24*365), "/");
            return $_SESSION['referance_code'];
        }

        return "";
    }
}

