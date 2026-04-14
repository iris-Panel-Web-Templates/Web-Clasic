<?php

class Downloads_Class {
    public static function Downloads_Get() {
        if (apcu_exists(APCU_PREFIX.'Downloads_Time') && (apcu_fetch(APCU_PREFIX.'Downloads_Time') + Downloads_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'Downloads_List')){
                iFunctions::ConsoleLog("Downloads for CACHE.");
                return apcu_fetch(APCU_PREFIX.'Downloads_List');
            }
        }
        apcu_store(APCU_PREFIX.'Downloads_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/Downloads/List";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Downloads Get Error: ".$response->responseMessage."<br/>");
            apcu_store(APCU_PREFIX.'Downloads_List', []);
            apcu_store(APCU_PREFIX.'Downloads_Time', time()-(Downloads_CacheTime-5));
            return [];
        }

        apcu_store(APCU_PREFIX.'Downloads_List', $response->list, 3600);
        iFunctions::ConsoleLog("Downloads for API.");
        return $response->list;

        // Downloads List içeriği (JSON camelCase döner)
        //int                id
        //int                order           — Gösterim sırası (küçük = önce)
        //string             name            — İndirme öğesinin adı (ör. "Tam İstemci", "Yama")
        //string             size            — Boyut string olarak (ör. "4.2 GB")
        //string             url             — İndirme linki
        //string             special         — Özel etiket/rozet metni (ör. "ÖNERİLEN", boş olabilir)
        //DateTime           create          — Oluşturulma tarihi (UTC)
        //DateTime           update          — Son güncelleme tarihi (UTC)
        //List<DownloadDesc> descs           — Dile göre açıklama listesi
            //string lang                    — Dil kodu (bkz. iApi_Setting.php dil listesi)
            //string desc                    — O dildeki açıklama metni
    }

}