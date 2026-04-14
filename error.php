<?php
$statusCode = $_SERVER['REDIRECT_STATUS'] ?? http_response_code();
$statusCode = (int)$statusCode;

if ($statusCode === 403) {
    http_response_code(403);
    $title   = "403 — Erişim Reddedildi";
    $heading = "Erişim Yasak";
    $message = "Bu alana girme yetkiniz bulunmuyor.";
    $sub     = "Kaybolduysanız aşağıdaki butonu kullanabilirsiniz.";
} else {
    http_response_code(404);
    $statusCode = 404;
    $title   = "404 — Sayfa Bulunamadı";
    $heading = "Sayfa Bulunamadı";
    $message = "Aradığınız sayfa mevcut değil ya da taşınmış olabilir.";
    $sub     = "Belki de yanlış bir yoldasınız, kahraman.";
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #150f08;
            background-image:
                radial-gradient(ellipse at 50% 0%, rgba(100,50,10,0.35) 0%, transparent 70%);
            color: #c8a96e;
            font-family: Georgia, 'Times New Roman', serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }

        .ornament {
            font-size: 28px;
            color: #5a3010;
            letter-spacing: 12px;
            margin-bottom: 16px;
            user-select: none;
        }

        .code {
            font-size: 110px;
            font-weight: bold;
            color: #7a3b1e;
            text-shadow:
                0 2px 4px rgba(0,0,0,0.8),
                0 0 60px rgba(122,59,30,0.3);
            line-height: 1;
            letter-spacing: -2px;
        }

        .divider {
            width: 260px;
            height: 1px;
            margin: 22px auto;
            background: linear-gradient(to right, transparent, #6b3015, #c8a96e, #6b3015, transparent);
        }

        .heading {
            font-size: 26px;
            color: #e8d09a;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .message {
            font-size: 15px;
            color: #9a7d50;
            line-height: 1.8;
            max-width: 420px;
        }

        .message em {
            display: block;
            font-style: italic;
            color: #7a6040;
            font-size: 13px;
            margin-top: 4px;
        }

        .divider-sm {
            width: 80px;
            height: 1px;
            margin: 28px auto;
            background: linear-gradient(to right, transparent, #5a3010, transparent);
        }

        .btn {
            display: inline-block;
            padding: 11px 38px;
            background: linear-gradient(to bottom, #7a3b1e 0%, #4e2410 100%);
            color: #e8d09a;
            text-decoration: none;
            font-family: Georgia, serif;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 1px solid #7a3b1e;
            border-radius: 2px;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }

        .btn:hover {
            background: linear-gradient(to bottom, #9a4b2e 0%, #7a3b1e 100%);
            color: #fff8e8;
            border-color: #c8703a;
        }

        .footer-note {
            position: fixed;
            bottom: 18px;
            font-size: 11px;
            color: #3a2810;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="ornament">✦ ✦ ✦</div>
    <div class="code"><?= $statusCode ?></div>
    <div class="divider"></div>
    <div class="heading"><?= $heading ?></div>
    <div class="message">
        <?= $message ?>
        <em><?= $sub ?></em>
    </div>
    <div class="divider-sm"></div>
    <a href="/" class="btn">Ana Sayfaya Dön</a>
    <div class="footer-note">METIN2</div>
</body>
</html>
