<?php
// ============================================================
// irisApi — Merkezi API Erişim Katmanı (Facade Pattern)
// ============================================================
// Bu dosya, tüm API sınıflarını tek bir noktadan erişilebilir
// hale getiren bir Facade (cephe) görevi görür.
//
// KULLANIM:
//   irisApi::$Account->Login(...)
//   irisApi::$RankList->PlayerList(true)
//   irisApi::$News->News_List()
//   ... vb.
//
// DOSYA YAPISI:
//   iApi_Setting.php  — Yapılandırma sabitleri (AuthKey, URL, cache süreleri)
//   iApi_Account.php  — Hesap işlemleri (giriş, kayıt, şifre, token)
//   iApi_Rank.php     — Oyuncu ve lonca sıralamaları
//   iApi_Statistics.php — Sunucu istatistikleri
//   iApi_News.php     — Haberler (dil filtrelemeli)
//   iApi_Events.php   — Etkinlikler (dil filtrelemeli)
//   iApi_Downloads.php — İndirme linkleri
//   iApi_Ticket.php   — Destek talep sistemi
//   iApi_UserAgent.php — Tarayıcı saat dilimi ve referans kodu yönetimi
//   iFunctions.php    — Yardımcı fonksiyonlar (IP, dil, session, cache)
// ============================================================

require("iApi_Setting.php");
require("iFunctions.php");
require("iApi_Rank.php");
require("iApi_Account.php");
require("iApi_Statistics.php");
require("iApi_News.php");
require("iApi_Events.php");
require("iApi_Downloads.php");
require("iApi_Ticket.php");
require("iApi_UserAgent.php");
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

class irisApi {
    // Her modül için statik sınıf örneği.
    // Static özellikler sayesinde new irisApi() gerekmez; doğrudan irisApi::$Account->... kullanılır.
    public static RankList_Class   $RankList;
    public static Account_Class    $Account;
    public static Statistics_Class $Statistics;
    public static UserAgent_Class  $UserAgent;
    public static News_Class       $News;
    public static Events_Class     $Events;
    public static Downloads_Class  $Downloads;
    public static Ticket_Class     $Ticket;

    // Tüm modülleri başlatır. Bu dosya include edildiğinde otomatik çağrılır.
    public static function init(): void {
        self::$RankList   = new RankList_Class();
        self::$Account    = new Account_Class();
        self::$Statistics = new Statistics_Class();
        self::$UserAgent  = new UserAgent_Class();
        self::$News       = new News_Class();
        self::$Events     = new Events_Class();
        self::$Downloads  = new Downloads_Class();
        self::$Ticket     = new Ticket_Class();
    }
}

// Static property'lerin varsayılan değer alabilmesi için sınıf tanımından hemen sonra init() çağrılır.
irisApi::init();


