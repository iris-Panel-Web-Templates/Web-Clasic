<?php
// ============================================================
// RankList_Class — Oyuncu ve Lonca Sıralama Modülü
// ============================================================
// API'den sıralama listelerini çeker ve APCu'da önbelleğe alır.
// Her liste için iki versiyon saklanır:
//   - Mini (ilk 10): Sidebar / küçük widget için
//   - Full (ilk 100): Tam sıralama sayfası için
//
// Cache mantığı:
//   _Time anahtarı en son API çağrısının zamanını tutar.
//   _Time + CacheTime > time() koşulu sağlandığı sürece API'ye gidilmez.
//   Hata durumunda _Time, CacheTime-15 saniye geri alınır;
//   böylece bir sonraki istekte tekrar API'ye gidilir (hata döngüsü oluşmaz).
// ============================================================

class RankList_Class {

    // Oyuncu sıralama listesini döndürür.
    // $listFull: bool — true: top 100 (tam liste) | false: top 10 (mini widget)
    // Dönüş: array — RankingList nesneleri dizisi (yapı dosyanın altında belgelenmiştir)
    public static function PlayerList($listFull){
        if (apcu_exists(APCU_PREFIX.'RankList_Player_Time') && apcu_fetch(APCU_PREFIX.'RankList_Player_Time') + PlayerList_CacheTime  > time()) {
            iFunctions::ConsoleLog("Player rank list for CACHE. Type: ".$listFull);
            if (apcu_exists(APCU_PREFIX.'RankList_Player_Full') &&  $listFull) { return apcu_fetch(APCU_PREFIX.'RankList_Player_Full');}
            if (apcu_exists(APCU_PREFIX.'RankList_Player_Mini') && !$listFull) { return apcu_fetch(APCU_PREFIX.'RankList_Player_Mini');}
        }
        // Cache süresi dolmuş veya yok; API'ye git ve sonucu kaydet.
        apcu_store(APCU_PREFIX.'RankList_Player_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/Ranking/Player";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Player List Get Error: ".$response->responseMessage."<br/>");
            apcu_store(APCU_PREFIX.'RankList_Player_Full', []);
            apcu_store(APCU_PREFIX.'RankList_Player_Mini', []);
            apcu_store(APCU_PREFIX.'RankList_Player_Time', time()-(PlayerList_CacheTime-15));
            return [];
        }

        apcu_store(APCU_PREFIX.'RankList_Player_Full', array_slice($response->rankingList, 0,100), 3600);
        apcu_store(APCU_PREFIX.'RankList_Player_Mini', array_slice($response->rankingList, 0, 10), 3600);
        iFunctions::ConsoleLog("Player rank list for API. Type: ".$listFull);
        if ($listFull) { return array_slice($response->rankingList, 0,100); }
        return array_slice($response->rankingList, 0,10);

        // PlayerRankingList içeriği (JSON camelCase döner)
            //int       order
            //int       id
            //int       accID
            //string    name
            //string    guild       — Lonca adı (loncası yoksa boş string)
            //int       job         — 0-8 arası (PlayerProto ile aynı enum, bkz. iApi_Account.php)
            //int       empire      — 1:Shinsoo, 2:Chunjo, 3:Jinno  (img/{empire}.jpg ile görsel kullanılabilir)
            //int       level
            //int       exp
            //int       playTime    — Saniye cinsinden
            //bool      isOnline
            //DateTime  lastPlay
            //DateTime  syncTime
    }
    // Lonca sıralama listesini döndürür.
    // $listFull: bool — true: top 100 (tam liste) | false: top 10 (mini widget)
    // Dönüş: array — RankingList nesneleri dizisi (yapı dosyanın altında belgelenmiştir)
    public static function GuildList($listFull){
        if (apcu_exists(APCU_PREFIX.'RankList_Guild_Time') && apcu_fetch(APCU_PREFIX.'RankList_Guild_Time') + GuildList_CacheTime > time()) {
            iFunctions::ConsoleLog("Guild rank list for CACHE.");
            if (apcu_exists(APCU_PREFIX.'RankList_Guild_Full') &&  $listFull)  { return apcu_fetch(APCU_PREFIX.'RankList_Guild_Full');}
            if (apcu_exists(APCU_PREFIX.'RankList_Guild_Mini') && !$listFull)  { return apcu_fetch(APCU_PREFIX.'RankList_Guild_Mini');}
        }
        // Cache süresi dolmuş veya yok; API'ye git ve sonucu kaydet.
        apcu_store(APCU_PREFIX.'RankList_Guild_Time', time(), 3600);

        $post_url     = irisAuthUrl . "/Ranking/Guild";
        $response     = iFunctions::ApiPost($post_url, []);
        if ($response->responseCode != 0) {
            iFunctions::ConsoleLog("Guild List Get Error: ".$response->responseMessage."<br/>");
            apcu_store(APCU_PREFIX.'RankList_Guild_Full', []);
            apcu_store(APCU_PREFIX.'RankList_Guild_Mini', []);
            apcu_store(APCU_PREFIX.'RankList_Guild_Time', time()-(GuildList_CacheTime-15));
            return [];
        }

        apcu_store(APCU_PREFIX.'RankList_Guild_Full', array_slice($response->rankingList, 0,100), 3600);
        apcu_store(APCU_PREFIX.'RankList_Guild_Mini', array_slice($response->rankingList, 0, 10), 3600);
        iFunctions::ConsoleLog("Guild rank list for API.");
        if ($listFull) { return array_slice($response->rankingList, 0,100); }
        return array_slice($response->rankingList, 0,10);

        // GuildRankingList içeriği (JSON camelCase döner)
            //int      order
            //string   name
            //string   leader_Name
            //int      leader_Point — Lonca savaşı puan (ladder_point)
            //int      leader_Job   — bkz. PlayerProto job enum (iApi_Account.php)
            //int      empire       — 1:Shinsoo, 2:Chunjo, 3:Jinno
            //int      level
            //int      personel     — Şu an her zaman 0 gelir (API henüz doldurmamaktadır; member_Count kullanın)
            //int      skor_Win
            //int      skor_Draw
            //int      skor_Loss
            //int      member_Count — Toplam kayıtlı üye sayısı
            //DateTime syncTime
        //
        // NOT: PHP GuildList_CacheTime=300s, API RankGuild_Build 120s aralıkla çalışır.
        //      PHP önbelleği 300s boyunca eski veriyi tutabilir. Daha güncel görünüm için
        //      GuildList_CacheTime değerini 120'ye eşitleyebilirsiniz (iApi_Setting.php).
    }
}