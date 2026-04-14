<div class="content">
    <div class="content-bg">
        <div class="content-bg-bottom">
            <h2>Metin2 - Lonca Sıralaması (İlk 100)</h2>
            <div id="ranking">
                <br />
                <table border="1">
                    <tr>
                        <th width="150">Yer</th>
                        <th width="150">İsim</th>
                        <th width="150">Galibiyet</th>
                        <th width="150">Beraberlik</th>
                        <th width="150">Kaybetme</th>
                    </tr>
                    <?php
                    $rankList = irisApi::$RankList->GuildList(true);
                    foreach ($rankList as $line) {
                        echo
                            "<tr>
                                <th width=\"110\"><font color=\"black\">". $line->order     ."</font></th>
                                <th width=\"150\"><font color=\"black\">". $line->name      ."</font></th>
                                <th width=\"150\"><font color=\"black\">". $line->skor_Win  ."</font></th>
                                <th width=\"150\"><font color=\"black\">". $line->skor_Draw ."</font></th>
                                <th width=\"150\"><font color=\"black\">". $line->skor_Loss ."</font></th>
                            ";
                    }
                    ?>
                </table>
                <br />
            </div>
            <center><strong><a class="btn" href="<?php echo iFunctions::IsLocal() ? "?s=rankings": "/rankplayer" ?>">Oyuncu liderlik tablosu</a></strong><br /></center>
            <br class="clearfloat" />
        </div>
    </div>
</div>
<div class="shadow">&nbsp;</div>
