<?php
// ============================================================
// rankings.php — Tam Oyuncu Sıralaması (Top 100)
// ============================================================
// irisApi::$RankList->PlayerList(true) çağrısı ile top 100 oyuncuyu çeker.
//   true  → 100 oyuncu (bu sayfa)
//   false → 10 oyuncu  (sidebar widget: user/ranksmall.php)
//
// Kullanılabilir RankingList alanları:
//   $line->order      — Sıra numarası (1-100)
//   $line->id         — Karakter ID
//   $line->accID      — Hesap ID
//   $line->name       — Karakter adı
//   $line->guild      — Lonca adı (yoksa boş string)
//   $line->job        — Sınıf (int: 0-8)
//   $line->empire     — İmparatorluk (int: 1=Shinsoo, 2=Chunjo, 3=Jinno)
//   $line->level      — Seviye
//   $line->exp        — Deneyim puanı
//   $line->playTime   — Oyun süresi
//   $line->isOnline   — Şu an çevrimiçi mi? (bool)
//   $line->lastPlay   — Son oynama tarihi (DateTime, UTC)
//   $line->syncTime   — Son güncelleme tarihi (DateTime, UTC)
//
// İmparatorluk görseli için: img/{empire}.jpg (1.jpg, 2.jpg, 3.jpg)
// ============================================================
?>

<div class="content">
	<div class="content-bg">
		<div class="content-bg-bottom">
			<h2>Metin2 - Oyuncu Sıralaması (İlk 100)</h2>
			<div id="ranking">
				<br />
				<table border="1">
                    <tr>
                        <th width="150">Konum</th>
                        <th width="150">Ad</th>
                        <th width="150">Seviye</th>
                        <th width="150">Deneyim</th>
                        <th width="150">İmparatorluk  </th>
                    </tr>
				<?php
                    $rankList = irisApi::$RankList->PlayerList(true);
                    foreach ($rankList as $line) {
                        echo
                        "<tr>
                            <th width=\"150\"><font color=\"black\">$line->order</font></th>
                            <th width=\"150\"><font color=\"black\">$line->name</font></th>
                            <th width=\"150\"><font color=\"black\">$line->level</font></th>
                            <th width=\"150\"><font color=\"black\">" . number_format($line->exp)   . "</font></th>
                            <th width=\"150\" align=\"center\"><img alt='' src=\"img/$line->empire.jpg\"></th>
                        </tr>";
                    }
					echo "</table>";
				?>
				<br />
			</div>
			<center><strong><a class="btn" href="<?= iFunctions::IsLocal() ? "?s=guildrank": "/rankguild" ?>">Lonca liderlik tablosu</a></strong><br /></center>
			<br class="clearfloat" />
		</div>
	</div>
</div>
<div class="shadow">&nbsp;</div>
