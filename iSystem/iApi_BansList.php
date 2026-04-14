<?php
// ============================================================
// BansList_Class — Yasaklı Oyuncu Listesi Modülü
// ============================================================
// API'den sunucudaki tüm ban'lı hesapların listesini çeker
// ve APCu'da önbelleğe alır.
//
// Endpoint : POST /BansList/Get
// Cache    : BansList_CacheTime saniye (iApi_Setting.php)
//
// Cache mantığı:
//   _Time anahtarı son API çağrısının zamanını tutar.
//   Hata durumunda _Time, CacheTime-15 saniye geri alınır;
//   böylece bir sonraki istekte tekrar denenir (hata döngüsü oluşmaz).
// ============================================================

class BansList_Class {

    // Yasaklı oyuncu listesini döndürür.
    // Dönüş: array — BanProto nesneleri dizisi (yapı dosyanın altında belgelenmiştir)
    public static function BansList_Get(): array {
        if (apcu_exists(APCU_PREFIX.'BansList_Time') && apcu_fetch(APCU_PREFIX.'BansList_Time') + BansList_CacheTime > time()) {
            if (apcu_exists(APCU_PREFIX.'BansList_Data')) {
                iFunctions::ConsoleLog("BansList for CACHE.");
                return apcu_fetch(APCU_PREFIX.'BansList_Data');
            }
        }
        apcu_store(APCU_PREFIX.'BansList_Time', time(), 3600);

        $post_url = irisAuthUrl . "/BansList/Get";
        $response = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("BansList Get Error: " . $response->responseMessage);
            apcu_store(APCU_PREFIX.'BansList_Data', []);
            apcu_store(APCU_PREFIX.'BansList_Time', time() - (BansList_CacheTime - 15));
            return [];
        }

        $list = is_array($response->banlist) ? $response->banlist : [];
        apcu_store(APCU_PREFIX.'BansList_Data', $list, 3600);
        iFunctions::ConsoleLog("BansList for API.");
        return $list;

        // BanProto içeriği (JSON camelCase döner)
        //
        //   string   playerName     — Karakter adı.
        //   DateTime dateStart      — Ban başlangıç tarihi (UTC). iFunctions::DateTime_UtcToZone() ile dönüştürün.
        //   DateTime dateFinish     — Ban bitiş tarihi (UTC).    iFunctions::DateTime_UtcToZone() ile dönüştürün.
        //   string   banDescription — Ban nedeni / açıklaması. Boş olabilir.
    }

}
