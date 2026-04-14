<div class="boxes-top">&nbsp;</div>
<?php include("./user/login.inc.php"); ?>
<div class="boxes-middle">&nbsp;</div>
<div class="modul-box modul-box-2">
	<div class="modul-box-bg">
		<div class="modul-box-bg-bottom">
			<h3>Sıralama listesi</h3>
            <div class='form-score'>
                <div class='highscore-player'>
                    <ul>
                        <?php
                            $rankList = irisApi::$RankList->PlayerList(false);
                            foreach ($rankList as $line) {
                                echo $line->order % 2 == 0 ? "<li class='light'>" : "<li>";
                                echo "<div class='empire$line->empire'><strong class='offset'>$line->order</strong> - $line->name</div></li>";
                            }
                        ?>
                    </ul>
                </div>
            </div>
			<div style="text-align: center;"><a href="<?= iFunctions::IsLocal() ? "?s=rankings": "/rankplayer" ?>" class="btn" style="margin: auto;">Top 100</a></div>
			<br />
		</div>
	</div>
</div>
<div class="boxes-bottom">&nbsp;</div>



