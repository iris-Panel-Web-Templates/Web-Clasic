<?php

class News_Class {
    private static function News_Get(): array {
        if (apcu_exists(APCU_PREFIX.'News_CacheTime') && (apcu_fetch(APCU_PREFIX.'News_CacheTime') + News_CacheTime > time())) {
            if (apcu_exists(APCU_PREFIX.'News_List')){
                iFunctions::ConsoleLog("News for CACHE.");
                return apcu_fetch(APCU_PREFIX.'News_List');
            }
        }
        apcu_store(APCU_PREFIX.'News_CacheTime', time(), 3600);

        $response     = iFunctions::ApiPost(irisAuthUrl . "/News/List", []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("News Get Error: ".$response->responseMessage."<br/>");
            apcu_store(APCU_PREFIX.'News_List', []);
            apcu_store(APCU_PREFIX.'News_CacheTime', time()-(News_CacheTime-15));
            return [];
        }

        apcu_store(APCU_PREFIX.'News_List', $response->news, 3600);
        iFunctions::ConsoleLog("News for API.");
        return $response->news;

        // NewsList yapısı/içeriği (JSON camelCase döner)
            //int      id
            //int      type            — Haber kategorisi (admin panelden tanımlı)
            //int      order           — Gösterim sırası (küçük = önce)
            //int      status          — 0:Pasif, 1:Aktif
            //string   language        — "def" (herkese), veya dil kodu: tr,en,de,pt,cz,fr,es,hu,pl,ro,it,nl,gr,ae
            //string   adminName       — Haberi ekleyen admin adı
            //string   title
            //string   content_Preview — Liste görünümü için kısa özet
            //string   content_Full    — Haber detay sayfası için tam içerik
            //DateTime date_Create     — Oluşturulma tarihi (UTC)
            //DateTime date_Start      — Yayın başlangıç tarihi (UTC)
            //DateTime date_Finish     — Yayın bitiş tarihi (UTC)
            //int      autoStart       — 1: date_Start gelince otomatik aktif et
            //int      autoHide        — 1: date_Finish gelince otomatik gizle
            //int      clickCount      — Görüntülenme sayısı
    }
    public  static function News_List(): array {
        $getList = self::News_Get();
        $language = iFunctions::GetLanguage();

        $newsList = json_decode(json: "[]", associative: true);
        foreach ($getList as $line){
            if ($line->language !== "def" && $line->language !== $language){ continue; }
            $newsList[] = $line;
        }

        return $newsList;
    }

}