<?php

class Statistics_Class {

    // API ulaşılamaz olduğunda gösterim kırılmasın diye sıfır dolu varsayılan nesne döndürür.
    private static function _defaultStats(): object {
        return (object)[
            'online_Total'    => 0,
            'online_Empires'  => [0, 0, 0, 0],
            'player_Total'    => 0,
            'player_Empires'  => [0, 0, 0, 0],
            'player_Job'      => [0, 0, 0, 0, 0, 0, 0, 0, 0],
            'account_Total'   => 0,
            'account_24'      => 0,
            'account_Pc'      => 0,
            'account_Empires' => [0, 0, 0, 0],
            'guild_Total'     => 0,
            'channels'        => [],
            'shop_Total'      => 0,
            'shop_Item_Total' => 0,
            'item_Total'      => 0,
            'gold_Total'      => 0,
            'won_Total'       => 0,
            'gem_Total'       => 0,
        ];
    }

    public static function Statistics_Get(): array {
        if (apcu_exists(APCU_PREFIX.'Statistics_Time') && (apcu_fetch(APCU_PREFIX.'Statistics_Time') + Statistics_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Statistics_Reel') && apcu_exists(APCU_PREFIX.'Statistics_Fake')){
                iFunctions::ConsoleLog("Statistics for CACHE.");
                return [
                    'Reel'  => apcu_fetch(APCU_PREFIX.'Statistics_Reel'),
                    'Fake'  => apcu_fetch(APCU_PREFIX.'Statistics_Fake')
                ];
            }
        }
        apcu_store(APCU_PREFIX.'Statistics_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/Statistics/Get";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Statistics Get Error: ".$response->responseMessage);
            $default = self::_defaultStats();
            apcu_store(APCU_PREFIX.'Statistics_Reel', $default);
            apcu_store(APCU_PREFIX.'Statistics_Fake', $default);
            apcu_store(APCU_PREFIX.'Statistics_Time', time()-(Statistics_CacheTime-5));
            return [ 'Reel' => $default, 'Fake' => $default];
        }

        apcu_store(APCU_PREFIX.'Statistics_Reel', $response->reel, 3600);
        apcu_store(APCU_PREFIX.'Statistics_Fake', $response->fake, 3600);
        iFunctions::ConsoleLog("Statistics for API.");
        return [
            'Reel'  => $response->reel,
            'Fake'  => $response->fake
        ];

        // Statistics Reel/Fake içeriği (JSON camelCase döner)
        //
        // Reel = veritabanından okunan gerçek değerler (120 saniyede bir güncellenir)
        // Fake = Reel * Multiplier + Add formülüyle hesaplanan gösterim değerleri
        //        Çarpan ve ek miktarlar Admin Panel → İstatistik Ayarları'ndan yönetilir.
        //        Oyunculara Fake değerleri gösterin (Reel'i göstermeyin).
        //
        // online_Total ve online_Empires: oyun motorundan her 5 saniyede bir güncellenir.
        // Diğer tüm alanlar: her 120 saniyede bir MySQL sorgusuyla güncellenir.
        //
        // List türlerinde index 0 boş/kullanılmaz (0), 1-3 arası imparatorluklar:
        //   1:Shinsoo, 2:Chunjo, 3:Jinno
        // Örnek: $sFake->online_Empires[1] → Shinsoo online oyuncu sayısı (Fake)
        //        $sFake->online_Empires[0] → her zaman 0 (kullanılmaz)
        //
            //List<int> channels         — Her kanalın online oyuncu sayısı [ch1, ch2, ...] (oyun motorundan gelir)
            //int       account_Total    — Toplam kayıtlı hesap sayısı
            //int       account_24       — Son 24 saatte yeni kayıt olan hesap sayısı
            //int       account_Pc       — Son 24 saatte farklı HWID'den bağlanan PC sayısı (unique PC)
            //List<int> account_Empires  — İmparatorluğa göre hesap: [0(boş), 1:Shinsoo, 2:Chunjo, 3:Jinno]
            //int       player_Total     — Toplam oluşturulmuş karakter sayısı
            //List<int> player_Empires   — İmparatorluğa göre karakter: [0(boş), 1, 2, 3]
            //List<int> player_Job       — Sınıfa göre karakter sayısı: [0-8] (bkz. PlayerProto job enum)
            //int       guild_Total      — Toplam lonca sayısı
            //int       shop_Total       — Şu an açık özel dükkan sayısı
            //int       shop_Item_Total  — Özel dükkanların toplam item sayısı
            //int       item_Total       — Sunucudaki toplam item sayısı (tüm tablolar)
            //long      gold_Total       — Sunucudaki toplam altın miktarı
            //long      won_Total        — Toplam won (ikincil para birimi) miktarı
            //long      gem_Total        — Toplam gem miktarı
            //int       online_Total     — Şu an çevrimiçi oyuncu sayısı (oyun motorundan)
            //List<int> online_Empires   — Çevrimiçi oyuncular imparatorluğa göre: [0(boş), 1, 2, 3]
    }

}