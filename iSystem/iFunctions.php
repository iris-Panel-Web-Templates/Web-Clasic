<?php
// ============================================================
// iFunctions — Genel Yardımcı Fonksiyonlar
// ============================================================
// Proje genelinde kullanılan statik yardımcı metodları içerir:
//   - İstemci IP tespiti (Cloudflare desteği dahil)
//   - Dil yönetimi (session → tarayıcı → varsayılan)
//   - Session bazlı kimlik doğrulama
//   - Ortam tespiti (local / production)
//   - API yanıt doğrulama
//   - Tarih/saat dönüşümü (UTC → kullanıcı saat dilimi)
//   - Yardımcı araçlar (debug log, rastgele string)
// ============================================================

class iFunctions {

    // ----------------------------------------------------------
    // IP TESPİT FONKSİYONLARI
    // ----------------------------------------------------------

    // Çeşitli HTTP başlıklarından ham IP adresini döndürür.
    // Öncelik sırası: REMOTE_ADDR (en güvenilir) → proxy başlıkları.
    // NOT: Bu fonksiyon doğrudan kullanılmaz; GetRequestIP() üzerinden çağrılır.
    private static function _getUserRealIP(): string {
        if (isset($_SERVER['REMOTE_ADDR']))              { return $_SERVER['REMOTE_ADDR']; }
        if (isset($_SERVER['HTTP_CLIENT_IP']))           { return $_SERVER['HTTP_CLIENT_IP']; }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))     { return $_SERVER['HTTP_X_FORWARDED_FOR']; }
        if (isset($_SERVER['HTTP_X_FORWARDED']))         { return $_SERVER['HTTP_X_FORWARDED']; }
        if (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) { return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP']; }
        if (isset($_SERVER['HTTP_FORWARDED_FOR']))       { return $_SERVER['HTTP_FORWARDED_FOR']; }
        if (isset($_SERVER['HTTP_FORWARDED']))           { return $_SERVER['HTTP_FORWARDED']; }
        return 'UNKNOWN';
    }

    // Verilen IP adresinin CIDR formatındaki bir aralıkta olup olmadığını kontrol eder.
    // Örnek: _checkIpInRange("1.2.3.4", "1.2.3.0/24") → true
    private static function _checkIpInRange($ip, $range): bool {
        if (strpos($range, '/') === false) { $range .= '/32'; }

        // $range is in IP/CIDR format eg 127.0.0.1/24
        list($range, $netmask) = explode('/', $range, 2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32 - $netmask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    }

    // Gelen IP adresinin Cloudflare altyapısına ait olup olmadığını kontrol eder.
    // IP aralıkları Cloudflare'in resmi listesinden alınmıştır: https://www.cloudflare.com/ips/
    // Cloudflare IP listesi değişebilir; periyodik olarak güncellenmelidir.
    private static function _checkIsCloudflare($ip): bool {
        $cf_ips = array(
            "173.245.48.0/20",
            "103.21.244.0/22",
            "103.22.200.0/22",
            "103.31.4.0/22",
            "141.101.64.0/18",
            "108.162.192.0/18",
            "190.93.240.0/20",
            "188.114.96.0/20",
            "197.234.240.0/22",
            "198.41.128.0/17",
            "162.158.0.0/15",
            "104.16.0.0/13",
            "104.24.0.0/14",
            "172.64.0.0/13",
            "131.0.72.0/22"
        );
        $is_cf_ip = false;
        foreach ($cf_ips as $cf_ip) {
            if (self::_checkIpInRange($ip, $cf_ip)) {
                $is_cf_ip = true;
                break;
            }
        }
        return $is_cf_ip;
    }

    // Kullanıcının gerçek IP adresini döndürür. Cloudflare koruması varsa
    // CF-Connecting-IP başlığını okur (bu başlık gerçek kullanıcı IP'sini taşır).
    // Cloudflare yoksa _getUserRealIP() tarafından döndürülen IP kullanılır.
    // Dönüş: string — IPv4 veya IPv6 adresi, tespit edilemezse "UNKNOWN"
    public  static function GetRequestIP(): string {
        $httpIp = self::_getUserRealIP();
        $check = self::_checkIsCloudflare($httpIp);
        if ($check) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }else{
            return $httpIp;
        }
    }

    // ----------------------------------------------------------
    // DİL YÖNETİMİ
    // ----------------------------------------------------------

    // Kullanıcının aktif dil kodunu döndürür.
    // Öncelik sırası:
    //   1. $_SESSION['language']   — Kullanıcı daha önce bir dil seçtiyse
    //   2. HTTP_ACCEPT_LANGUAGE    — Tarayıcı dili destekleniyorsa
    //   3. Default_Language        — Hiçbiri uygun değilse (iApi_Setting.php'de tanımlı)
    // Dönüş: string — 2 karakterli dil kodu (ör. "tr", "en", "de")
    public  static function GetLanguage(): string {
        global $_SESSION;
        $supportedLangs = ['tr', 'en', 'de', 'fr', 'es', 'pl', 'ro', 'it', 'pt', 'cz', 'hu', 'nl', 'gr', 'ae'];
        if (isset($_SESSION['language']) && strlen($_SESSION['language'])>=2) {
            $lang = strtolower(substr($_SESSION['language'],0,2));
            if (in_array($lang, $supportedLangs)) { return $lang; }
        }

        // Tarayıcı dilini al, destekleniyorsa session'a kaydet ve döndür
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strlen($_SERVER['HTTP_ACCEPT_LANGUAGE'])>=2) {
            $browserLang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            if (in_array($browserLang, $supportedLangs)) { $_SESSION['language'] = $browserLang; return $browserLang; }
        }

        $_SESSION['language'] = Default_Language;
        return Default_Language;
    }

    // ----------------------------------------------------------
    // KİMLİK DOĞRULAMA
    // ----------------------------------------------------------

    // Giriş yapmış kullanıcının AccountID değerini döndürür.
    // Kullanıcı giriş yapmamışsa 0 döner. Tüm giriş gerektiren işlemlerde kontrol edilmelidir.
    // Dönüş: int — AccountID (0 = giriş yok)
    public  static function GetAccountID(): int {
        global $_SESSION;
        $accountID = 0;
        if (isset($_SESSION['account_id'])) { $accountID = $_SESSION['account_id']; }
        return $accountID;
    }

    // ----------------------------------------------------------
    // ORTAM TESPİTİ
    // ----------------------------------------------------------

    // Sitenin yerel geliştirme ortamında mı çalıştığını döndürür.
    // URL yönlendirmelerinde kullanılır: local'de "?s=page", production'da "/page" formatı.
    // PhpStorm dahili sunucusu da local olarak kabul edilir.
    // Dönüş: bool
    public  static function IsLocal(): bool {
        if ($_SERVER['SERVER_NAME'] == 'localhost') { return true; }
        if ($_SERVER['SERVER_NAME'] == '127.0.0.1') { return true; }
        if (str_starts_with($_SERVER['SERVER_NAME'], 'PhpStorm')) { return true; }

        $server_ip = self::GetRequestIP();
        if ($server_ip == '127.0.0.1') { return true; }
        if ($server_ip == '::1')       { return true; }

        return false;
    }

    // ----------------------------------------------------------
    // DEBUG ARAÇLARI
    // ----------------------------------------------------------

    // Tarayıcı konsoluna debug mesajı yazar.
    // Yalnızca iApi_Setting.php'de şu satır aktifse çalışır:
    //   apcu_store(APCU_PREFIX.'Console_Debug_Write', true);
    // Production ortamında bu satır yorum satırı olmalıdır.
    public  static function ConsoleLog($text): void {
        if (apcu_fetch(APCU_PREFIX.'Console_Debug_Write')) {
            // json_encode: tırnak, ters eğik çizgi ve </script> içeren değerleri güvenli biçimde kaçırır.
            echo "<script>console.log(" . json_encode((string)$text) . ");</script>";
        }
    }

    // Belirtilen uzunlukta rastgele büyük harf + rakam kombinasyonu üretir.
    // Kullanım alanları: aktivasyon kodu, token, referans kodu vb.
    // Dönüş: string — yalnızca A-Z ve 0-9 içerir
    public  static function Random_String($stringSize): string {
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $stringSize) as $k) $rand .= $seed[$k];

        return $rand;
    }

    // ----------------------------------------------------------
    // TARİH / SAAT DÖNÜŞÜMÜ
    // ----------------------------------------------------------

    // API'den gelen UTC tarih/saati, kullanıcının tarayıcı saat dilimine dönüştürür.
    // Kullanıcı saat dilimi $_SESSION['user_timezone']'da tutulur;
    // bu değer iApi_UserAgent.php tarafından JavaScript ile otomatik tespit edilir.
    //
    // $utcDate : string — UTC formatında tarih (ör. "2025-01-01 12:00:00")
    // $isFormat: bool   — true: "Y-m-d H:i:s" string döner | false: DateTime nesnesi döner
    public  static function DateTime_UtcToZone($utcDate, bool $isFormat): DateTime|string {
        global $_SESSION;
        $userTimezone = "UTC";
        if (isset($_SESSION['user_timezone'])) { $userTimezone = $_SESSION['user_timezone']; }

        // Zamanı UTC olarak oluştur.
        $utcTime = new DateTime($utcDate, new DateTimeZone("UTC"));

        // Kullanıcının saat dilimini ata
        $utcTime->setTimezone(new DateTimeZone($userTimezone));

        // Format yok ise Object ver.
        if (!$isFormat) { return $utcTime; }

        // Format var ise String ver.
        return $utcTime->format('Y-m-d H:i:s');
    }

    // ----------------------------------------------------------
    // API YANIT DOĞRULAMA
    // ----------------------------------------------------------

    // Tüm iSystem API çağrılarının tek giriş noktası.
    // cURL ile JSON POST yapar; başarılıysa decode edilmiş nesneyi döndürür,
    // hata durumunda responseCode != 0 olan bir hata nesnesi döndürür (asla false/null dönmez).
    //
    // Avantajları (file_get_contents'e göre):
    //   - CURLOPT_TIMEOUT ile tam timeout kontrolü (askıda kalmaz)
    //   - HTTP durum kodunu doğrudan curl_getinfo'dan alır (header parse gerekmez)
    //   - allow_url_fopen ayarından bağımsızdır
    //   - İleride async/parallel geçiş için uygun zemin
    //
    // $url  : string — Tam endpoint URL'i (irisAuthUrl . "/Endpoint/Path")
    // $data : array  — POST body olarak JSON'a dönüştürülecek anahtar-değer dizisi
    //
    // Dönüş: object — Her zaman bir nesne döner:
    //   responseCode == 0  → Başarılı; diğer alanlar API'den gelen veri
    //   responseCode != 0  → Hata; responseMessage açıklamayı içerir
    //
    // Timeout ayarları (iApi_Setting.php'de merkezi olarak değiştirilebilir):
    //   Bağlantı zaman aşımı : 5 saniye
    //   Toplam istek zaman aşımı: 10 saniye
    public static function ApiPost(string $url, array $data): object {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data),
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => [
                "Content-Type: application/json",
                "Accept: application/json",
                "UserAgent: iWebApi-Client",
                "AuthKey: " . irisAuthKey,
            ],
            CURLOPT_SSL_VERIFYPEER => !self::IsLocal(), // Lokal geliştirmede sertifika doğrulaması kapalı
            CURLOPT_SSL_VERIFYHOST => self::IsLocal() ? 0 : 2,
        ]);
        $result   = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno    = curl_errno($ch);
        $errMsg   = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0 || $result === false) {
            $err = self::_ApiError(999, "Sunucu yanıt vermedi! ($errMsg)");
            self::ErrorLog($url, $data, $err->responseCode, $err->responseMessage);
            return $err;
        }
        if ($httpCode === 403) {
            $err = self::_ApiError(403, "IP Erişim izni yok! Admin Panelden IP izinlerini kontrol edin.");
            self::ErrorLog($url, $data, $err->responseCode, $err->responseMessage);
            return $err;
        }
        if ($httpCode === 404) {
            $err = self::_ApiError(404, "404 Page Not Found.");
            self::ErrorLog($url, $data, $err->responseCode, $err->responseMessage);
            return $err;
        }
        if ($httpCode !== 200) {
            $err = self::_ApiError($httpCode, $httpCode . " ERROR!");
            self::ErrorLog($url, $data, $err->responseCode, $err->responseMessage);
            return $err;
        }

        $decoded = json_decode($result);
        if ($decoded === null) {
            $err = self::_ApiError(999, "Geçersiz JSON yanıtı.");
            self::ErrorLog($url, $data, $err->responseCode, $err->responseMessage);
            return $err;
        }
        return $decoded;
    }

    // Hassas alanları maskeler. Yalnızca ErrorLog() içinden çağrılır.
    // Maskelenen alanlar: parola, pin, token ve benzeri kimlik bilgileri.
    private static function _maskSensitiveData(array $data): array {
        $sensitiveKeys = [
            'LoginPass', 'Password', 'PasswordOld', 'PasswordNew',
            'PinCode', 'PinCodeOld', 'PinCodeNew',
            'DeleteCode', 'DeleteCodeOld', 'DeleteCodeNew',
            'Token', 'TokenCode'
        ];
        foreach ($sensitiveKeys as $key) {
            if (array_key_exists($key, $data)) { $data[$key] = '***'; }
        }
        return $data;
    }

    // API hatalarını logs/error.log dosyasına yazar.
    // Her hata 3 satır olarak kaydedilir:
    //   Satır 1: [tarih] [IP] POST {url}
    //   Satır 2: [tarih] [IP] DATA {post_verisi — hassas alanlar maskelenir}
    //   Satır 3: [tarih] [IP] ERROR {kod} — {mesaj}
    // iApi_Setting.php'de ErrorLogWrite = false yapılırsa loglama devre dışı kalır.
    // logs/ klasörü yoksa otomatik oluşturulur.
    public static function ErrorLog(string $url, array $data, int $code, string $message): void {
        if (!ErrorLogWrite) { return; }

        $logDir = dirname(ErrorLogFile);
        if (!is_dir($logDir)) { mkdir($logDir, 0755, true); }

        $ts   = date('Y-m-d H:i:s');
        $ip   = self::GetRequestIP();
        $safe = self::_maskSensitiveData($data);

        $entry  = "[$ts] [$ip] POST $url\n";
        $entry .= "[$ts] [$ip] DATA " . json_encode($safe) . "\n";
        $entry .= "[$ts] [$ip] ERROR $code — $message\n";

        error_log($entry, 3, ErrorLogFile);
    }

    // Standart hata nesnesi üretir. Yalnızca ApiPost() içinden çağrılır.
    private static function _ApiError(int $code, string $message): object {
        return (object)[
            "responseCode"    => $code,
            "responseMessage" => $message,
            "processTime"     => 0
        ];
    }

    // ResultCheck: Geriye dönük uyumluluk için korunmaktadır.
    // Yeni kod ApiPost() kullanmalıdır.
    // file_get_contents() ile yapılan API isteğinin sonucunu kontrol eder.
    // İstek başarılıysa JSON'u decode eder ve döndürür.
    // İstek başarısızsa (false) HTTP hata koduna göre anlamlı bir hata nesnesi oluşturur:
    //   403 → IP izin hatası (Admin Panel'den IP eklenmemiş)
    //   404 → Endpoint bulunamadı
    //   999 → Sunucu yanıt vermedi veya bağlantı hatası
    public  static function ResultCheck($result) {
        if ($result === false) {
            $responseCode = 999; $responseMessage = "";
            if (isset($http_response_header[0])) {
                /** @noinspection RegExpRedundantEscape */
                preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
                $responseCode = $match[1] ?? 999;

                $responseMessage = $responseCode." ERROR!";
                if ($responseCode == 403) { $responseMessage = "IP Erişim izni yok! Admin Panelden IP izinlerini kontrol edin."; }
                if ($responseCode == 404) { $responseMessage = "404 Page Not Found."; }
            }
            else { $responseMessage = "Sunucu yanıt vermedi!"; }

            $data= [
                "responseCode"    => $responseCode,
                "responseMessage" => $responseMessage,
                "processTime"     => 0
            ];
            $result = json_encode($data);
            return json_decode($result);
        }
        return json_decode($result);
    }


}

