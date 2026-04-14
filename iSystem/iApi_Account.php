<?php
// ============================================================
// Account_Class — Hesap İşlemleri API Modülü
// ============================================================
// Kullanıcı hesabıyla ilgili tüm API çağrılarını yönetir:
//   - Giriş / Kayıt
//   - Şifre ve PIN değiştirme
//   - Şifremi unuttum (token ile)
//   - Aktivasyon e-postası gönderme/doğrulama
//   - PinCode / Safebox / DeleteCode SMS/mail gönderme
//   - Token sorgulama
//   - Session yönetimi (SessionSet)
//
// Tüm metodlar API'ye JSON POST yapar ve ResultCheck() ile
// yanıtı doğrular. Başarılı işlemlerde responseCode == 0 döner.
//
// Tüm metodların ortak dönüş alanları:
//   responseCode    : int    — 0: başarılı, diğer: hata
//   responseMessage : string — Hata açıklaması veya bilgi mesajı (kullanıcıya gösterilebilir)
//   processTime     : long   — API tarafındaki işlem süresi (milisaniye). Debug/performans takibi için.
//                              PHP'de int olarak gelir. Normal değer: 1-50ms, yüksekse API yükü var.
//
// AccountProto ve PlayerProto yapıları dosyanın sonunda belgelenmiştir.
// ============================================================

class Account_Class {

    // Kullanıcı girişi yapar. Başarılıysa hesap bilgilerini session'a kaydeder.
    // $loginName : string — Giriş adı
    // $loginPass : string — Şifre
    // $pinCode   : string — 6 haneli güvenlik PIN kodu (boş bırakılabilir)
    //
    // API'de bulunup bu PHP metodunda henüz KULLANILMAYAN parametreler:
    //   TFACode    : string — İki faktörlü doğrulama kodu (2FA aktifse gerekli, yoksa "" gönderin)
    //   GoogleCode : string — Google Authenticator kodu (Google 2FA aktifse gerekli, yoksa "" gönderin)
    //   TFACode ve GoogleCode admin panelden hesap bazında aktifleştirilebilir.
    //   Siteye 2FA desteği eklemek istiyorsanız bu parametreleri post_data'ya ekleyin.
    //
    // Dönüş: object — responseCode==0 başarılı, accountInfo içinde hesap bilgileri
    public static function Login($loginName, $loginPass, $pinCode): mixed {
        $post_url     = irisAuthUrl . "/Account/Login";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'LoginName' => $loginName,
            'LoginPass' => $loginPass,
            'PinCode'   => $pinCode,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);

        if ($response->responseCode == 0) {
            session_regenerate_id(true); // Session fixation koruması: yeni ID üret, eskiyi sil
            self::SessionSet($response->accountInfo);
        }
        return $response;
        // Dönüş alanları:
        //int          responseCode     — 0: başarılı
        //string       responseMessage  — Hata veya bilgi mesajı
        //long         processTime      — API'nin isteği işleme süresi (milisaniye), debug için
        //AccountProto accountInfo      — Başarılı girişte dolu, hata durumunda null
    }
    // Yeni hesap oluşturur. Başarıyla kayıt olunursa hesap bilgileri döner (otomatik giriş YAPILMAZ).
    // Giriş için kayıt sonrası ayrıca Login() çağrılmalıdır.
    // $loginName    : string — Giriş adı (benzersiz olmalı)
    // $loginPass    : string — Şifre (min. 6 karakter)
    // $email        : string — E-posta adresi
    // $phone        : string — Telefon numarası (bilinmiyorsa "" gönderin)
    // $userName     : string — Oyun içi görünen ad
    // $pinCode      : string — 6 haneli güvenlik PIN kodu (istemiyorsanız "" gönderin)
    // $deleteCode   : string — 7 haneli karakter silme kodu (yalnızca harf+rakam)
    // $referanceName: string — Referans veren kullanıcının LOGIN ADI (kodu değil, boş bırakılabilir)
    //                          ReferanceCode ise otomatik cookie'den alınır (iApi_UserAgent)
    //
    // API'de Chapca yönetebilirsiniz (kendi yönetiminiz daha sağlıklı olur):
    //   GoogleCode : string — Google Authenticator kodu (kayıt sırasında 2FA bağlama için)
    //   TokenCode  : string — Dışarıdan token tabanlı kayıt akışı için (özel entegrasyonlar)
    //   Her ikisi de opsiyoneldir; kullanmıyorsanız "" gönderebilirsiniz.
    //
    // Dönüş: object — responseCode==0 başarılı, accountInfo dolu gelir (login için kullanılabilir)
    public static function SingUp($loginName, $loginPass, $email, $phone, $userName, $pinCode, $deleteCode, $referanceName): mixed {
        $post_url     = irisAuthUrl . "/Account/Create";
        $post_data    = array(
            'LoginName'     => $loginName,
            'LoginPass'     => $loginPass,
            'EMail'         => $email,
            'Phone'         => $phone,
            'Name'          => $userName,
            'PinCode'       => $pinCode,
            'DeleteCode'    => $deleteCode,
            'ReferanceName' => $referanceName,
            'ReferanceCode' => irisApi::$UserAgent->ReferanceCode_Get(),
            'Language'      => iFunctions::GetLanguage(),
            'IpAddress'     => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    // Session'daki hesap bilgilerini API'den tazeler ve session'ı günceller.
    // Sayfa her yüklendiğinde çağrılır (iPage_Index.php'den) fakat gereksiz API çağrısını önlemek
    // için son yenileme zamanından bu yana 30 saniye geçmediyse API'ye gidilmez.
    //
    // $accountID: int — 0 bırakılırsa aktif session'dan alınır (normal kullanım)
    //                   Belirtilirse o hesap zorla yenilenir (admin kullanımı için)
    // Dönüş: bool — true: başarıyla yenilendi veya henüz yenilemeye gerek yok | false: hata
    public static function Account_Refresh($accountID=0): bool {
        global $_SESSION;
        $timeCheck = false;
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); $timeCheck = true; }
        if ($accountID == 0) { return false; }
        if ($timeCheck) {
            // Son API yenileme zamanını kontrol et.
            // time_stamp, API'nin AccountProto içinde döndürdüğü Unix zaman damgasıdır.
            // Account_RefreshTime saniye geçmediyse API'ye tekrar gidilmez; session yeterince güncel kabul edilir.
            $timeStamp = $_SESSION['time_stamp'] * 1;
            if ($timeStamp + Account_RefreshTime > time()) { return true; }
        }

        /** @noinspection DuplicatedCode */
        $post_url     = irisAuthUrl . "/Account/Get";
        $post_data    = array(
            'AccountID' => $accountID,
            'Detail'    => true,   // true: players listesi dahil tam AccountProto döner (yavaş)
                                   // false: sadece temel hesap bilgileri döner, players listesi boş (hızlı)
            'Language'  => iFunctions::GetLanguage()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Account Refresh Error: ".$response->responseMessage);
            return false;
        }
        self::SessionSet($response->accountInfo);
        iFunctions::ConsoleLog("Account Refreshed.");
        return true;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    public static function Account_Recover($accountID=0) {
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); }

        $post_url     = irisAuthUrl . "/Account/Recover";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Account Recover Error: ".$response->responseMessage);
            return $response;
        }

        session_regenerate_id(true); // Session fixation koruması: yeni ID üret, eskiyi sil
        self::SessionSet($response->accountInfo);
        iFunctions::ConsoleLog("Account Recovered.");
        return $response;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }

    // API'den dönen AccountProto nesnesini PHP session'ına yazar.
    // Giriş, yenileme ve kurtarma işlemlerinden sonra çağrılır.
    //
    // NOT: login_pass (password) session'a yazılır. API key + IP koruması zaten güvenli bir
    // iletişim sağlar; ancak şifre değiştirme gibi akışlarda mevcut şifreyi doğrulamak için
    // bu değer okunabilir. Hassas ortamlarda session'a yazmak istemiyorsanız bu satırı kaldırın.
    //
    // ATILAN ALANLAR (AccountProto'da var ama session'a yazılmıyor):
    //   accountInfo->language    — Kullanıcının API'de kayıtlı dil tercihi.
    //                              Sitenin kendi GetLanguage() yöntemi kullanıldığı için atlandı.
    //   accountInfo->passwordMd5 — Şifrenin MD5 özeti. Güvenlik nedeniyle session'a yazılmıyor.
    /** @noinspection DuplicatedCode */
    public static function SessionSet($accountInfo): void {
        global /** @noinspection DuplicatedCode */
        $_SESSION;
        $_SESSION['account_id']   = $accountInfo->accountID;
        $_SESSION['account_name'] = $accountInfo->accountName;
        $_SESSION['login_name']   = $accountInfo->loginName;
        $_SESSION['login_pass']   = $accountInfo->password;
        $_SESSION['cash']         = $accountInfo->cash;
        $_SESSION['mileage']      = $accountInfo->mileage;
        $_SESSION['status']       = $accountInfo->status;
        $_SESSION['phone']        = $accountInfo->phone;
        $_SESSION['email']        = $accountInfo->email;
        $_SESSION['delete_code']  = $accountInfo->deleteCode;
        $_SESSION['pin_code']     = $accountInfo->pinCode;
        $_SESSION['players']      = $accountInfo->players;

        $_SESSION['shop_token']   = $accountInfo->shopToken;
        $_SESSION['time_stamp']   = $accountInfo->timeStamp;
        $_SESSION['cache_time']   = $accountInfo->cacheTime;
    }

    // Kullanıcının PIN kodunu değiştirir. İşlem başarılıysa session güncellenir.
    // $password   : string — Mevcut hesap şifresi (doğrulama için)
    // $pincodeOld : string — Eski 6 haneli PIN kodu
    // $pincodeNew : string — Yeni 6 haneli PIN kodu
    // API'de henüz KULLANILMAYAN parametre: TokenCode (2FA doğrulaması için, "" gönderin)
    // Dönüş: object — responseCode==0 başarılı, accountInfo güncellenmiş hesap bilgileri
    public static function Change_PinCode($password, $pincodeOld, $pincodeNew){
        $post_url     = irisAuthUrl . "/Account/PinCode/Change";
        $post_data    = array(
            'AccountID'     => iFunctions::GetAccountID(),
            'LoginPass'     => $password,
            'PinCodeOld'    => $pincodeOld, // (6 characters.)
            'PinCodeNew'    => $pincodeNew, // (6 characters.)
            'Language'      => iFunctions::GetLanguage(),
            'IpAddress'     => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Change PinCode Error: ".$response->responseMessage);
            return $response;
        }

        self::SessionSet($response->accountInfo);
        return $response;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }
    // Kullanıcının şifresini değiştirir. DeleteCode da aynı anda değiştirilir.
    // $passwordOld  : string — Eski şifre (min. 6 karakter)
    // $passwordNew  : string — Yeni şifre (min. 6 karakter)
    // $deleteCodeOld: string — Mevcut 7 haneli karakter silme kodu (doğrulama için)
    // $deleteCodeNew: string — Yeni 7 haneli karakter silme kodu
    // API'de henüz KULLANILMAYAN parametre: TokenCode (2FA doğrulaması için, "" gönderin)
    // Dönüş: object — responseCode==0 başarılı, accountInfo güncellenmiş hesap bilgileri
    public static function Change_Password($passwordOld, $passwordNew, $deleteCodeOld, $deleteCodeNew) {
        $post_url     = irisAuthUrl . "/Account/Password/Change";
        $post_data    = array(
            'AccountID'     => iFunctions::GetAccountID(),
            'PasswordOld'   => $passwordOld, // (min. 6 characters.)
            'PasswordNew'   => $passwordNew, // (min. 6 characters.)
            'DeleteCodeOld' => $deleteCodeOld, // (7 characters, yalnızca harf+rakam)
            'DeleteCodeNew' => $deleteCodeNew, // (7 characters, yalnızca harf+rakam)
            'Language'      => iFunctions::GetLanguage(),
            'IpAddress'     => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Change Password Error: ".$response->responseMessage);
            return $response;
        }

        self::SessionSet($response->accountInfo);
        return $response;
        //int            ResponseCode
        //string         ResponseMessage
        //long           ProcessTime
        //AccountProto   AccountInfo
    }

    // Şifremi unuttum — kullanıcıya e-posta/SMS ile şifre sıfırlama linki gönderir.
    // Linkte 16 karakterlik bir token bulunur; bu token Forgot_Password_Save'e iletilir.
    // $loginName: string — Hesap adı
    // $email    : string — Kayıtlı e-posta adresi (bilinmiyorsa "" gönderin)
    // $phone    : string — Kayıtlı telefon numarası (bilinmiyorsa "" gönderin)
    // NOT: EMail ve Phone'dan en az biri dolu olmalıdır. API doğrulaması bu alanlar
    //      üzerinden yapılır; ikisi de boşsa API hata döner.
    // Dönüş: object — responseCode==0 gönderim başarılı
    public static function Forgot_Password_Send($loginName, $email, $phone){
        $post_url     = irisAuthUrl . "/Account/Password/Forgot";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'LoginName' => $loginName,
            'EMail'     => $email,
            'Phone'     => $phone,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Forgot Password Send Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }
    // Şifremi unuttum — token doğrulandıktan sonra yeni şifreyi kaydeder.
    // Token, kullanıcıya gönderilen e-posta/SMS linkinden alınır (?token=XXXX).
    // $password : string — Yeni şifre (min. 6 karakter)
    // $tokenCode: string — 16 karakterlik şifre sıfırlama token'ı
    // Dönüş: object — responseCode==0 başarılı
    public static function Forgot_Password_Save($password, $tokenCode){
        $post_url     = irisAuthUrl . "/Account/Password/Forgot/Save";
        $post_data    = array(
            'LoginPass' => $password, // (min. 6 characters.)
            'Token'     => $tokenCode,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Forgot Password Save Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }

    // Hesap aktivasyon linkini e-posta/SMS ile tekrar gönderir.
    // Kayıt sonrası aktivasyon yapmamış kullanıcılar için kullanılır.
    // Dönüş: object — responseCode==0 başarılı
    public static function Activation_Send($loginName, $email, $phone) {
        $post_url     = irisAuthUrl . "/Account/Activation/Send";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'LoginName' => $loginName,
            'EMail'     => $email,
            'Phone'     => $phone,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Send PinCode Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }
    // Aktivasyon tokenını doğrular ve hesabı aktif hale getirir.
    // Token, aktivasyon e-postasındaki linkten alınır (?token=XXXX).
    // $tokenCode: string — 16 karakterlik aktivasyon token'ı
    // Dönüş: object — responseCode==0 başarılı
    public static function Activation_Save($tokenCode) {
        $post_url     = irisAuthUrl . "/Account/Activation/Save";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'Token'          => $tokenCode,
            'Language'       => iFunctions::GetLanguage(),
            'IpAddress'      => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Send PinCode Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }

    // Kullanıcının PIN kodunu e-posta/SMS ile gönderir (hatırlatma amaçlı).
    // $accountID: int — 0 bırakılırsa aktif session'dan alınır
    // Dönüş: object — responseCode==0 başarılı
    public static function Send_PinCode($accountID=0) {
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); }

        $post_url     = irisAuthUrl . "/Account/Send/PinCode";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Send PinCode Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }
    // Kullanıcının kasa şifresini e-posta/SMS ile gönderir.
    // $accountID: int — 0 bırakılırsa aktif session'dan alınır
    // Dönüş: object — responseCode==0 başarılı
    public static function Send_Safebox($accountID=0) {
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); }

        $post_url     = irisAuthUrl . "/Account/Send/Safebox";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Send Safebox Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }
    // Kullanıcının karakter silme kodunu e-posta/SMS ile gönderir.
    // $accountID: int — 0 bırakılırsa aktif session'dan alınır
    // Dönüş: object — responseCode==0 başarılı
    public static function Send_DeleteCode($accountID=0) {
        if ($accountID == 0) { $accountID = iFunctions::GetAccountID(); }

        $post_url     = irisAuthUrl . "/Account/Send/DeleteCode";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Send DeleteCode Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int      ResponseCode
        //string   ResponseMessage
        //long     ProcessTime
    }

    // Token bilgisini sorgular. Aktivasyon ve şifre sıfırlama akışlarında kullanılır.
    // URL'den gelen ?token=XXXX değerini API'ye göndererek token türünü ve geçerliliğini kontrol eder.
    //
    // Token türleri (tokenInfo.tokenType):
    //   0: None         — Geçersiz veya süresi dolmuş
    //   1: Activation   — Hesap aktivasyon token'ı
    //   2: ForgetPassword — Şifre sıfırlama token'ı
    //
    // $token: string — 16 karakterlik token kodu
    // Dönüş: object — responseCode==0 başarılı, tokenInfo içinde token detayları
    public static function TokenInfo($token){
        $post_url     = irisAuthUrl . "/Account/TokenInfo";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'Token'     => $token,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        $response     = iFunctions::ApiPost($post_url, $post_data);
        if ($response->responseCode != 0){
            iFunctions::ConsoleLog("Token Info Send Error: ".$response->responseMessage);
            return $response;
        }

        return $response;
        //int          ResponseCode
        //string       ResponseMessage
        //long         ProcessTime
        //TokenProto   TokenInfo
            //int        AccountID
            //string     TokenCode
            //TokenTypes TokenType
            //string     TokenName
            //DateTime   TimeCreate
            //DateTime   TimeFinish
            //string     IpAddress
    }
}


// ============================================================
// ALAN ADI KASASI KURALI (TÜM API DOSYALARI İÇİN GEÇERLİ)
// ============================================================
// API belgelerindeki alan adları, sunucu tarafındaki C# sınıf
// adlarını yansıtır ve PascalCase formatındadır.
// Ancak JSON yanıtı camelCase olarak serialize edilir.
//
// Kural:
//   Tek kelime    : İlk harf küçük  → Job → $line->job
//   Çok kelime    : camelCase        → LoginName → $line->loginName
//   Alt çizgili   : İlk bölüm küçük → Content_Preview → $line->content_Preview
//
// Örnek:
//   Belge: AccountID    → PHP'de: $accountInfo->accountID
//   Belge: Content_Preview → PHP'de: $line->content_Preview
//   Belge: Online_Total → PHP'de: $stat->online_Total
// ============================================================

// :::::: (AccountProto yapısı — JSON camelCase döner) ::::::
    //int               accountID
    //string            accountName
    //string            loginName     — min 3, max 30 karakter (yalnızca harf+rakam)
    //string            phone
    //string            email
    //string            status        — "OK", "ATC" (aktivasyon bekliyor), "BAN"
    //string            language
    //string            passwordMd5
    //string            password
    //string            pinCode       — 6 haneli güvenlik kodu
    //string            deleteCode    — 7 haneli karakter silme kodu
    //string            ipAddress     — Son giriş IP adresi (SessionSet'e yazılmaz)
    //string            shopToken     — Dış item shop için token
    //int               cash          — Birincil oyun parası
    //int               mileage       — İkincil/puan para birimi
    //List<PlayerProto> players       — Detail=true ise dolu, Detail=false ise boş liste
    //long              timeStamp     — Unix zaman damgası (son cache zamanı)
    //DateTime          cacheTime

// :::::: (PlayerProto yapısı — JSON camelCase döner) ::::::
    //int      id
    //string   nick
    //int      job          — 0:Savaşçı(e), 1:Sura(e), 2:Ninja(e), 3:Şaman(e)
    //                        4:Savaşçı(k), 5:Ninja(k), 6:Sura(k), 7:Şaman(k), 8:Lycan
    //int      level
    //int      exp
    //int      stat_Hp
    //int      stat_Mp
    //int      stat_St
    //int      stat_Ht
    //int      stat_Dx
    //int      stat_Iq
    //int      money_Gold
    //int      money_Won
    //int      money_Gem
    //int      part_Main
    //int      part_Base
    //int      part_Hair
    //int      part_Sash
    //int      playTime     — Saniye cinsinden; saat için: $line->playTime / 3600
    //int      alignment
    //int      lastMapID
    //int      lastPosX     — Ham pozisyon; gerçek koordinat için: $line->lastPosX / 100
    //int      lastPosY     — Ham pozisyon; gerçek koordinat için: $line->lastPosY / 100
    //DateTime lastPlay