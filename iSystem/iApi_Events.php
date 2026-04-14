<?php
// ============================================================
// Events_Class — Sunucu/Mağaza Etkinlikleri API Modülü
// ============================================================
// Oyun motorundaki aktif etkinlikleri (happy hour, indirim vb.) API'den çeker
// ve APCu'da önbelleğe alır.
//
// Endpoint : POST /iShop/Events  (ShopController → Events_List)
// İçerik   : Oyun motorundaki tüm tanımlı etkinlikler; dil filtresi yoktur,
//             tümü tüm kullanıcılara gösterilir.
//
// Cache mantığı:
//   Events_CacheTime süresi dolduğunda API'ye tekrar gidilir.
//   Hata durumunda süre geri alınır; bir sonraki istekte tekrar denenir.
//   NOT: API sunucu tarafında Events her zaman taze veriyle gelir (no server cache).
//
// Admin Panel → iPage_System.php ActionID=15 ile PHP APCu cache'i manuel temizlenebilir.
// ============================================================

class Events_Class {

    // API'den etkinlik listesini çeker ve APCu'ya kaydeder.
    // Direkt çağrılmaz; Events_List() üzerinden kullanılır.
    // Dönüş: array — EventProto nesneleri dizisi (yapı dosyanın altında belgelenmiştir)
    private static function Events_Get(): array {
        if (apcu_exists(APCU_PREFIX.'Events_Time') && (apcu_fetch(APCU_PREFIX.'Events_Time') + Events_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Events_List')) {
                iFunctions::ConsoleLog("Events for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Events_List');
            }
        }
        // Cache süresi dolmuş veya yok; API'ye git ve sonucu kaydet.
        apcu_store(APCU_PREFIX.'Events_Time', time(), 3600);

        $response = iFunctions::ApiPost(irisAuthUrl . "/iShop/Events", []);

        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Events Get Error: ".$response->responseMessage."<br/>");
            apcu_store(APCU_PREFIX.'Events_List', []);
            // Hata durumunda cache zamanını geri al; bir sonraki istekte tekrar API'ye gitsin.
            apcu_store(APCU_PREFIX.'Events_Time', time()-(Events_CacheTime-15));
            return [];
        }

        apcu_store(APCU_PREFIX.'Events_List', $response->events, 3600);
        iFunctions::ConsoleLog("Events for API.");
        return $response->events;

        // EventProto yapısı (JSON camelCase döner — iApi_Setting.php JSON kuralı geçerli)
        //
        // Örnek kullanım:
        //   foreach (irisApi::$Events->Events_List() as $event) {
        //       if ($event->activeNow) { echo $event->name; }
        //   }
        //
            //int      id               — Etkinlik ID
            //string   name             — Etkinlik adı (ör. "Happy Hour", "Çift Tecrübe")
            //string   type             — Etkinlik türü kodu: "ItemShop_HapyHour", "ItemShop_Discount", vb.
            //DateTime dateStart        — Etkinlik başlangıç tarihi/saati (UTC)
            //long     dateStart_Unix   — Başlangıç Unix timestamp (JavaScript için kullanışlı)
            //DateTime dateFinish       — Etkinlik bitiş tarihi/saati (UTC)
            //long     dateFinish_Unix  — Bitiş Unix timestamp
            //double   value            — Etkinlik değeri (ör. Happy Hour için %25 → 25.0)
            //bool     activeNow        — Şu an aktif mi (sunucu tarih kontrolü)
    }

    // Tüm etkinlik listesini döndürür.
    // Events dil filtresi içermez; tüm etkinlikler herkese gösterilir.
    // Dönüş: array — EventProto nesneleri dizisi
    public static function Events_List(): array {
        return self::Events_Get();
    }

}
