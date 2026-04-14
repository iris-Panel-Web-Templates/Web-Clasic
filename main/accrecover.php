<div class="content content-last">
    <div class="content-bg">
        <div class="content-bg-bottom">
            <h2>Metin2 - Profil</h2>
            <div class="administration-inner-content">
                <div class="input-data-box">
                    <h4>Sonuç</h4>
                    <div style="padding: 20px; margin: 0 0 20px 0;">
                    <?php
                        $rResult = irisApi::$Account->Account_Recover();
                        if ($rResult->responseCode == 0){ echo "<div class=''><span style='color:darkgreen; font-size:16px'><strong>$rResult->responseMessage</strong></span></div>"; }
                        if ($rResult->responseCode != 0){ echo "<div class=''><span style='color:darkred; font-size:16px'><strong>$rResult->responseMessage</strong></span></div>"; }
                    ?>
                    </div>
                    <div class="administration-box"><a href="<?= iFunctions::IsLocal() ? "?s=profil": "/profil" ?>" class="btn">Profile geri dön</a></div>
                </div>
                <div class="box-foot"></div>
            </div>
        </div>
    </div>
</div>
<div class="shadow">&nbsp;</div>
