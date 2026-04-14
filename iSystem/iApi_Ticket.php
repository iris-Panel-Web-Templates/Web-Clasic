<?php /** @noinspection PhpUnused */
/** @noinspection PhpMissingReturnTypeInspection */
// ============================================================
// Ticket_Class — Destek Talebi (Support Ticket) Modülü
// ============================================================
// Kullanıcı ve Admin tarafı destek talebi işlemlerini yönetir.
//
// KULLANICI İŞLEMLERİ:
//   User_List($accountID)                              — Kullanıcının ticket listesi
//   User_Get($accountID, $ticketID)                    — Ticket detayı + mesajlar
//   User_Create($accountID, $subject, $firstMessage)   — Yeni ticket oluştur
//   User_Reply($accountID, $ticketID, $message)        — Ticket'a yanıt yaz
//   User_Status_Close($accountID, $ticketID)           — Ticket'ı kapat
//   User_Rate_Ticket($accountID, $ticketID, $rate, $msg)   — Ticket'ı puanla
//   User_Rate_Message($accountID, $ticketID, $msgID, $rate, $msg) — Mesajı puanla
//
// ADMİN İŞLEMLERİ:
//   Admin_List()                                       — Tüm ticketlar (max 250)
//   Admin_Reply($adminID, $adminName, $ticketID, $msg) — Admin yanıtı
//   Admin_Change($adminID, $adminName, $msgID, $msg)   — Mesaj düzenle
//   Admin_Email($adminID, $messageID)                  — Bildirim maili gönder
//   Admin_Delete_Ticket($adminID, $ticketID)           — Ticket sil
//   Admin_Delete_Message($adminID, $messageID)         — Mesaj sil
//   Admin_Status_Wait/Process/Close(...)               — Durum değiştir
//
// TicketLine ve MessageLine yapıları dosyanın altında belgelenmiştir.
// ============================================================

class Ticket_Class {
    public static function User_List($accountID) {
        $post_url     = irisAuthUrl . "/Ticket/User/List";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //List<TicketLine>? List
    }
    public static function User_Get($accountID, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/User/Get";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'TicketID'  => $ticketID,
            'AccountID' => $accountID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function User_Create($accountID, $subject, $firstMessage) {
        $post_url     = irisAuthUrl . "/Ticket/User/Create";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID'    => $accountID,
            'Subject'      => $subject, // (max. 96 characters.)
            'FirstMessage' => $firstMessage,
            'Language'     => iFunctions::GetLanguage(),
            'IpAddress'    => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);
        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //TicketLine?       Ticket
        //List<MessageLine>? Messages
    }
    public static function User_Reply($accountID, $ticketID, $message) {
        $post_url     = irisAuthUrl . "/Ticket/User/Reply";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'TicketID'  => $ticketID,
            'Message'   => $message,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);
        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //TicketLine?       Ticket
        //List<MessageLine>? Messages
    }
    public static function User_Status_Close($accountID, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/User/Close";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'TicketID'  => $ticketID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);
        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //TicketLine?       Ticket
        //List<MessageLine>? Messages
    }
    public static function User_Rate_Ticket($accountID, $ticketID, $rate, $rateMessage) {
        $post_url     = irisAuthUrl . "/Ticket/User/RateTicket";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'TicketID'  => $ticketID,
            'Rate'      => $rate, // double field. Separate fractional halves with ".", exaple: 5.5
            'Message'   => $rateMessage, // optional, can be left blank. (max. 512 characters.)
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //TicketLine?       Ticket
        //List<MessageLine>? Messages
    }
    public static function User_Rate_Message($accountID, $ticketID, $messageID, $rate, $rateMessage) {
        $post_url     = irisAuthUrl . "/Ticket/User/RateMessage";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AccountID' => $accountID,
            'TicketID'  => $ticketID,
            'MessageID' => $messageID,
            'Rate'      => $rate, // double field. Separate fractional halves with ".", exaple: 5.5
            'Message'   => $rateMessage, // optional, can be left blank. (max. 512 characters.)
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //TicketLine?       Ticket
        //List<MessageLine>? Messages
    }



    public static function Admin_List() {
        $post_url     = irisAuthUrl . "/Ticket/Admin/List";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
        //List<TicketLine>? List    (Maximum 250 records are returned)
    }
    public static function Admin_Reply($adminID, $adminName, $ticketID, $message) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Reply";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'AdminName' => $adminName,
            'TicketID'  => $ticketID,
            'Message'   => $message,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function Admin_Change($adminID, $adminName, $messageID, $message) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Change";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'AdminName' => $adminName,
            'MessageID' => $messageID,
            'Message'   => $message,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function Admin_Email($adminID, $messageID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Email";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'MessageID' => $messageID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
    }
    public static function Admin_Delete_Ticket($adminID, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/DeleteTicket";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'TicketID'  => $ticketID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int               ResponseCode
        //int               ResponseCodeSub
        //string            ResponseMessage
        //long              ProcessTime
    }
    public static function Admin_Delete_Message($adminID, $messageID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/DeleteMessage";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'MessageID' => $messageID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function Admin_Status_Wait($adminID, $adminName, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Wait";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'AdminName' => $adminName,
            'TicketID'  => $ticketID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function Admin_Status_Process($adminID, $adminName, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Process";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'AdminName' => $adminName,
            'TicketID'  => $ticketID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }
    public static function Admin_Status_Close($adminID, $adminName, $ticketID) {
        $post_url     = irisAuthUrl . "/Ticket/Admin/Close";
        /** @noinspection DuplicatedCode */
        $post_data    = array(
            'AdminID'   => $adminID,
            'AdminName' => $adminName,
            'TicketID'  => $ticketID,
            'Language'  => iFunctions::GetLanguage(),
            'IpAddress' => iFunctions::GetRequestIP()
        );
        return iFunctions::ApiPost($post_url, $post_data);

        //int                ResponseCode
        //int                ResponseCodeSub
        //string             ResponseMessage
        //long               ProcessTime
        //TicketLine?        Ticket
        //List<MessageLine>? Messages
    }

}

// ============================================================
// TICKET RESPONSE CODE SİSTEMİ
// ============================================================
// ResponseCode    : Genel HTTP/API sonucu (0 = başarılı)
// ResponseCodeSub : Ticket sistemine özel alt hata kodu
//   Örnek kullanım: Ticket kapalıysa Reply metodu responseCode=1 + responseCodeSub=101 gibi
//   döndürebilir. Alt kod değerleri API versiyonuna göre farklılık gösterebilir.
//   Genel hata ayıklama için responseMessage yeterlidir; responseCodeSub daha ince
//   kontrol (ör. "bu ticket zaten kapalı" ile "ticket bulunamadı" ayrımı) içindir.
// ============================================================

// :::::: (TicketLine yapısı/içeriği — JSON camelCase döner) ::::::
    //int               id
    //int               type
    //int               status          — 0:Wait (bekliyor), 1:Process (işlemde), 2:Close (kapalı)
    //string            language
    //DateTime          date_Create     — UTC
    //DateTime?         date_Finish     — Kapanış tarihi, null ise hâlâ açık (UTC)
    //int               account_ID
    //string            account_Login
    //string            account_Email
    //string            account_Phone
    //string            account_Status  — "OK", "ATC", "BAN"
    //int               account_Notice  — 2:PanelAdmin, 4:GmAdmin
    //int               admin_Type      — 0:None, 1:NewMessage (yeni mesaj var), 2:NotSeen (admin görmedi)
    //int               admin_ID
    //string            admin_Name
    //string            admin_Note      — Adminin iç notu (kullanıcıya gösterilmez)
    //int               rate_Point      — Ticket puanı (0 = henüz puanlanmadı)
    //DateTime?         rate_Date       — UTC
    //string            rate_Message
    //int               message_Count   — Toplam mesaj sayısı
    //List<MessageLine> messages        — User_Get'te dolu gelir; List'te boş veya null olabilir

// :::::: (MessageLine yapısı/içeriği — JSON camelCase döner) ::::::
    //int       id
    //int       ticketID
    //int       owner_Type      — 0:None, 1:System (otomatik), 2:AdminPanel, 3:User, 4:AdminGm
    //int       owner_ID
    //string    owner_Name
    //string    owner_Ip
    //string    message_Text
    //DateTime  message_Date_Add   — UTC
    //DateTime? message_Date_Read  — UTC, null ise okunmadı
    //int       message_Read    — 0:Okunmadı, 1:Okundu
    //DateTime? change_Date     — UTC, null ise düzenlenmedi
    //string    change_Text     — Düzenleme sonrası yeni metin
    //int       change_AdminID  — Düzenleyen admin ID
    //int       rate_Point
    //DateTime? rate_Date       — UTC
    //string    rate_Message



