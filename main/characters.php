<!--suppress CssUnusedSymbol -->
<style>
    .char-list-content {color: #86280f;font-weight: bold;margin-bottom: 10px;width: 480px;}
    .char-list-content .charList {height: 100px;padding: 2px;width: 476px;font-size: 12px;}
    .char-list-content .charList .charimg {float: left;height: 72px;margin-right: 5px;width: 56px;}
    .char-list-content .charList .charimg img {border: 1px solid #000;padding: 1px;margin: 6px;}
    .char-list-content .charList .charuser .charname {font-size: 16px;float: left;width: 276px;}
    .char-list-content .charList .charuser .charrank {text-align: right;}
    .char-list-content .charList .charlabel {color: #86280f;display: block;float: left;font-weight: bold;width: 75px;}
    .char-list-content .charList .chardata {color: #000;font-weight: bold;}
    .char-list-content .charList .charlevel, .char-list-content .charList .charclass {width: 160px;}
    .char-list-content .charList .charlevel, .char-list-content .charList .charclass, .char-list-content .charList .charkingdom, .char-list-content .charList .chartime, .char-list-content .charList .charserver, .char-list-content .charList .charposition {float: left;height: 20px;line-height: 20px;width: 250px;}
    .char-list-content .charList .charposition {width: 410px;}
    .char-list-content .charList .charlevel, .char-list-content .charList .charclass {width: 160px;}

</style>
<div class="content content-last">
    <div class="content-bg">
        <div class="content-bg-bottom">
            <h2>
                Profil - Karakterler
                <a href="<?= iFunctions::IsLocal() ? "?s=profil": "/profil" ?>" class="btn" style="float: right; margin-top: -3px; margin-right: 20px;">Profil'e Dön</a>
            </h2>
            <div class="administration-inner-content">
                <div class="char-list-content">
                    <?php
                        $pOrder = 0;
                        $aPlayers = $_SESSION['players'];
                        foreach ($aPlayers as $line){
                            $pOrder++;
                            $jobName = "";
                            if ($line->job == 0) { $jobName = "Savaşçı (e)"; }
                            if ($line->job == 1) { $jobName = "Sura (e)"; }
                            if ($line->job == 2) { $jobName = "Ninja (e)"; }
                            if ($line->job == 3) { $jobName = "Şaman (e)"; }
                            if ($line->job == 4) { $jobName = "Savaşçı (k)"; }
                            if ($line->job == 5) { $jobName = "Ninja (k)"; }
                            if ($line->job == 6) { $jobName = "Sura (k)"; }
                            if ($line->job == 7) { $jobName = "Şaman (k)"; }
                            if ($line->job == 8) { $jobName = "Lycan"; }
                            $playTime = $line->playTime / 60 / 60;

                            echo "
                                <div class='charList'>
                                    <div class='charimg'><img alt='' src='/img/face_$line->job.png' width='44' height='40'></div>
                                    <div class='charuser'>
                                        <div class='charname'>$line->nick</div>
                                        <div class='charrank'><span class='charlabel'>Sıra</span> <span class='chardata'>$pOrder&nbsp;&nbsp;</span></div>
                                    </div>
                                    <div class='charrow'>
                                        <div class='charclass'><span class='charlabel'>Sınıf</span> <span class='chardata'>$jobName</span></div>
                                        <div class='chartime'><span class='charlabel'>Oyun Süresi</span> <span class='chardata'>&nbsp;".number_format($playTime)." saat</span></div>
                                    </div>
                                    <div class='charrow'>
                                        <div class='charlevel'><span class='charlabel'>Level</span><span class='chardata'>$line->level</span></div>
                                        <div class='charserver'><span class='charlabel'>Sunucu</span> <span class='chardata'>&nbsp;...</span></div>
                                    </div>
                                    <div class='charrow charend'>
                                        <div class='charposition'><span class='charlabel'>Pozisyon</span> <span class='chardata'>".$line->lastMapID." (".($line->lastPosX/100)."x".($line->lastPosX/100).")</span></div>
                                    </div>
                                </div>
                            ";
                        }
                    ?>
                </div>
                <div class="box-foot"></div>
            </div>
        </div>
    </div>
</div>
<div class="shadow">&nbsp;</div>
