<?php /** @noinspection PhpUnused */
    // ============================================================
    // iApi_Setting — Yapılandırma ve Sistem Sabitleri
    // ============================================================
    // PHP Versiyon : 8.3.22 x64 Thread Safe (TS)
    // APCu Versiyon: 5.1.24 x64
    // File Encoding: UTF-8
    // Line Ending  : CRLF (\r\n)
    //
    // MİMARİ GENEL BAKIŞ:
    // - Tüm veri işlemleri SQL yerine irisApi üzerinden yapılır.
    //   Web sunucusunun veritabanına doğrudan erişimi yoktur.
    // - API sunucusu hem kimlik doğrulama (AuthKey) hem de IP whitelist kontrolü yapar.
    //   İki katmanlı güvenlik sayesinde key çalınsa bile izinsiz IP erişemez.
    // - Sık okunan veriler (sıralama, haberler, istatistik) APCu ile önbelleğe alınır.
    //   Bu sayede her sayfa yüklemesinde API çağrısı yapılmaz; performans artar.
    // - Api sistemine erişebilmek için 'irisAuthKey' kullanılır, bu anahtar bir kaç tane olabilir.
    //      Admin panel üzerinden yeni keyler eklenebilir, var olanlar kaldırılabilir.
    //      Sadece key ile erişim olmaz, sitenin çalıştığı sunucunun IP Adresine Admin panelden izin vermelisiniz.
    //      Eğer IrisPanel hosting sunucunusu kullanıyor iseniz, IP Adresi iznine gerek yok.
    //      Eğer Local'de çalışıyor iseniz, IP Adresinize panel üzerinden izin vermelisiniz. 'https://www.ipsorgu.com' sitesidnen IP Adresinizi öğrenebilirsiniz.
    //      Eğer kendi hosting sunucunuzu kullanıyor iseniz, Hosting sunucusunun IP Adresine Admin panel üzerinden izin vermelisiniz. Sunucu hizmeti aldığınız yer ile görüşüp öğrenebilirsiniz.

    // Localde çalışacaklar için indirme ve ayarlar.
    // PHP  İndir: https://windows.php.net/downloads/releases/php-8.3.22-Win32-vs16-x64.zip
    // Apcu İndir: https://downloads.php.net/~windows/pecl/releases/apcu/5.1.24/php_apcu-5.1.24-8.3-ts-vs16-x64.zip
    // php.ini Ayarlar:
    //      1) [PHP] altında ';extension=openssl' satırını bulun ve 'extension=openssl' şeklinde düzeltin, tırnakları eklemeyin!
    //      2) [PHP] altında 'extension=zip' satırını bulun ve altına 'extension=php_apcu.dll' satırını ekleyin, tırnakları eklemeyin!
    //      3) [CLI Server] altında 'cli_server.color = On' satırını bulun ve altına 'apc.enable_cli=1' satırını ekleyin, tırnakları eklemeyin!
    // Editör İndir: https://www.jetbrains.com/phpstorm/download/?section=windows
    // Editör Serial: https://gitee.com/superbeyone/J2_B5_A5_C4/blob/master/licenses/2025/2025-06-20.md
    //      - Soldaki listeden en son tarihli seriali kullanın!


    // Çoklu dil kullanıyor iseniz tanımı Session'da tutmalısınız. Sessiondaki adını 'language' olmalı.
    //      - Alabileceği değerler CodePage kısaltmaları içermeli 2 karakter olmalı!
    //      - tr,en,de,pt,cz,fr,es,hu,pl,ro,it,nl,gr,pt,ae

    // ============================================================
    // ORTAK DÖNÜŞ ALANLARI — TÜM API METODLARI İÇİN GEÇERLİ
    // ============================================================
    // Her API metodu en az şu üç alanı döndürür:
    //   responseCode    : int    — 0: başarılı | sıfırdan farklı: hata
    //   responseMessage : string — Hata veya bilgi mesajı (kullanıcıya doğrudan gösterilebilir)
    //   processTime     : long   — API sunucusunun isteği işleme süresi (milisaniye, PHP'de int gelir)
    //                              Beklenen aralık: 1–50 ms. Daha yüksek değerler API yükünü gösterir.
    //                              Şu an kullanılmıyor; ileride performans izleme için değerlendirilebilir.
    // ============================================================

    // ============================================================
    // JSON ALAN ADI KASASI KURALI — TÜM API DOSYALARI İÇİN GEÇERLİ
    // ============================================================
    // API C# (.NET) ile yazılmıştır; JSON serializasyonu camelCase çıktı üretir.
    // PHP'de $response nesnesi üzerinden erişirken:
    //
    //   Tek kelime          : ilk harf küçük  → Job      → $line->job
    //   Çok kelime camelCase: ilk harf küçük  → LoginName → $line->loginName
    //   Alt çizgili         : ilk bölüm küçük → Content_Preview → $line->content_Preview
    //                                           Online_Total    → $stat->online_Total
    //
    // Belgelerde PascalCase gösterilmiş olsa da PHP kodu camelCase kullanmalıdır.
    // ============================================================



    // irisAuthKey'yi Admin Panel → [Sistem Yönetimi] → [Web Ayarları] → [Web Api Ayarları] sayfasında "Güvenlik Anahtarları" bölümünden oluşturduğunuz anahtar.
    // Birden fazla anahtar tanımlanabilir; kullanılmayanlar Admin Panel'den silinebilir.
    const irisAuthKey = "8OTWUXU4HTDH75A2";

    // API sunucusunun adresi. "https://api.SiteAdınız.com" formatında olmalıdır.
    // PhpStorm dahili sunucusunda çalışırken ayrı bir URL tanımlanabilir (örn. localhost:7107).
    if (str_starts_with($_SERVER['SERVER_NAME'], 'PhpStorm')) {
        //define("irisAuthUrl", "https://localhost:7107");
        define("irisAuthUrl", "https://api.tugramt2.com");
    }
    else { define("irisAuthUrl", "https://api.tugramt2.com"); }

    // APCu anahtarlarına eklenen ön ek. AuthKey ile aynı tutulur;
    // böylece aynı sunucuda birden fazla site çalışıyorsa cache çakışmaz.
    const APCU_PREFIX = irisAuthKey;

    // Varsayılan dil (Session ve tarayıcı dili yoksa kullanılır).
    const Default_Language     = "tr"; // tr, en, de, fr, es, pl, ro, it, pt, cz, hu, nl, gr, ae

    // Cache süreleri (saniye). Düşük değer = daha güncel veri, daha fazla API çağrısı.
    const Account_RefreshTime  = 180; // iApi_Account::Account_Refresh() throttle süresi (min. 15 saniye olmalı)
    const Downloads_CacheTime  = 300; // İndirme listesi (nadiren değişir)
    const News_CacheTime       = 300; // Haber listesi
    const PlayerList_CacheTime =  70; // Oyuncu sıralaması (sık güncellenir)
    const GuildList_CacheTime  = 300; // Lonca sıralaması
    const Statistics_CacheTime =  10; // Anlık istatistikler (çok sık güncellenir)
    const Events_CacheTime     = 300; // Etkinlik listesi

    // Hata loglamasını aktif eder. Production'da true bırakın; debug sonrası false yapın.
    const ErrorLogWrite = true;

    // Hata log dosyasının tam yolu. logs/ klasörü web erişimine kapalıdır (.htaccess ile).
    define('ErrorLogFile', __DIR__ . '/../logs/error.log');

    // Geliştirme sırasında tarayıcı konsoluna debug mesajları yazdırmak için aktif edin.
    // Production ortamında bu satır yorum satırı kalmalıdır!
    apcu_store(APCU_PREFIX.'Console_Debug_Write',  true);




