<?php
require("iApi.php");

// :::::: (Check Local Access) ::::::
if (!iFunctions::IsLocal() && strpos($_SERVER['REQUEST_URI'], 'iSystem/iPage_System.php') !== false){
    $response = [
        "ResponseCode"    => 1001,
        "ResponseMessage" => "Sadece Local!",
        "ProcessTime"     => 0,
        "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
        "RequestQueryPage"=> $_GET['s'] ?? ""
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// :::::: (Check Post Method) ::::::
if($_SERVER['REQUEST_METHOD'] !== 'POST')  {
    $response = [
        "ResponseCode"    => 1001,
        "ResponseMessage" => "Sadece POST istekler!",
        "ProcessTime"     => 0,
        "RequestURI"      => $_SERVER['REQUEST_URI'] ?? "",
        "RequestQueryPage"=> $_GET['s'] ?? ""
    ];

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

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

// :::::: (Api Servis'den gelen Bilgi ve Emirler) ::::::
// Admin Panel, bu endpoint'i kullanarak web sitesinin APCu cache'ini uzaktan temizler.
// İstek AuthKey ile doğrulanır; yanlış key → 1002, eksik key → 1001 hatası döner.
//
// ActionID Tablosu:
//    1  — Tüm APCu cache'i temizle
//    2  — Tüm aktif session'ları temizle
//   11  — Haberler cache'ini temizle (News_List yeniden çekilir)
//   12  — İndirmeler cache'ini temizle (Downloads_List yeniden çekilir)
//   13  — Oyuncu sıralama cache'ini temizle (Player Full + Mini)
//   14  — İstatistik cache'ini temizle (Statistics Reel + Fake)
//   15  — Etkinlikler cache'ini temizle (Events_List)
//
// Bu dosyaya doğrudan tarayıcıdan erişilemez (Check Local Access bloğu engeller).
// Yalnızca sunucu taraflı POST isteği ile kullanılabilir.
if($_SERVER['REQUEST_URI'] === '/localapi'  || (isset($_GET['s']) && $_GET['s'] === 'localapi')) {
    $authKey = $_SERVER["HTTP_AUTHKEY"];

    if(empty($authKey)) {
        $response = [
            "ResponseCode"    => 1001,
            "ResponseMessage" => "WebPhp, AuthKey NULL!",
            "ProcessTime"     => 0
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if($authKey !== irisAuthKey) {
        $response = [
            "ResponseCode"    => 1002,
            "ResponseMessage" => "WebPhp, AuthKey hatalı!",
            "ProcessTime"     => 0
        ];

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $postJson = json_decode($postData);
    $response = [
        "ResponseCode"    => 1003,
        "ResponseMessage" => "None",
        "ProcessTime"     => 0
    ];

    if ($postJson->ActionID ==  1){
        apcu_clear_cache();
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Apcu Cache Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID ==  2){
        // NOT: session_destroy() sadece mevcut PHP sürecinin oturumunu siler.
        // Tüm kullanıcıların oturumunu silmek için session dizini manuel temizlenmelidir.
        // Bu komut yalnızca API sunucusunun kendi isteği sırasındaki session'ı sıfırlar.
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            session_start();
        }
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Current Session Destroyed",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 11){
        apcu_delete(APCU_PREFIX.'News_CacheTime');
        apcu_delete(APCU_PREFIX.'News_List');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, News Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 12){
        apcu_delete(APCU_PREFIX.'Downloads_CacheTime');
        apcu_delete(APCU_PREFIX.'Downloads_List');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Downloads Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 13){
        apcu_delete(APCU_PREFIX.'RankList_Player_Time');
        apcu_delete(APCU_PREFIX.'RankList_Player_Full');
        apcu_delete(APCU_PREFIX.'RankList_Player_Mini');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Ranks Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 14){
        apcu_delete(APCU_PREFIX.'Statistics_Time');
        apcu_delete(APCU_PREFIX.'Statistics_Reel');
        apcu_delete(APCU_PREFIX.'Statistics_Fake');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Statistics Cleared",
            "ProcessTime"     => 0
        ];
    }
    if ($postJson->ActionID == 15){
        apcu_delete(APCU_PREFIX.'Events_Time');
        apcu_delete(APCU_PREFIX.'Events_List');
        $response = [
            "ResponseCode"    => 0,
            "ResponseMessage" => "WebPhp, Events Cleared",
            "ProcessTime"     => 0
        ];
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}




