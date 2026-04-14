<div class="two-boxes">
    <div class="two-boxes-top">
        <div class="two-boxes-bottom">
            <div class="box">
                <h2>Metin2</h2>
                <div class="body"><p>Metin2'ye hoş geldiniz!</p>
                    <p>Pitoresk köyleri ve etkileyici manzaralarıyla Uzak Doğu dünyası önünüzde açılıyor.</p>
                    <p>Tehlikeli savaşlar sizi bekliyor!</p>
                    <p>Dövüş sanatlarında usta olmak ve ülkenizi Metin taşlarının karanlık etkisinden korumak için yolunuzu bulun.</p>
                </div>
            </div>
            <div class="box box-right">
                <h2>Fragman</h2>
                <div class="video"><object width="221" height="131" wmode="opaque"><iframe width="221" height="131" src="https://www.youtube.com/embed/LR0p460lbNs?rel=0" allowfullscreen></iframe></object></div>
            </div>
        </div>
    </div>
</div>
<div class="content">
	<div class="content-bg">
		<div class="content-bg-bottom">
			<h2>Ekran Görüntüleri</h2>
				<ul class="screenshots">
					<li class="first"><a href="/img/screenshots/mmorpg-fantasy-metin2-screenshot1.jpg"><img alt="Screenshot 1" src="/img/screenshots/mmorpg-fantasy-metin2-thumb1.jpg" width="100" height="75" /></a></li>
					<li><a href="/img/screenshots/mmorpg-fantasy-metin2-screenshot2.jpg"><img alt="Screenshot 2" src="/img/screenshots/mmorpg-fantasy-metin2-thumb2.jpg" width="100" height="75" /></a></li>
					<li><a href="/img/screenshots/mmorpg-fantasy-metin2-screenshot3.jpg"><img alt="Screenshot 3" src="/img/screenshots/mmorpg-fantasy-metin2-thumb3.jpg" width="100" height="75" /></a></li>
					<li><a href="/img/screenshots/mmorpg-fantasy-metin2-screenshot4.jpg"><!--suppress CheckImageSize --><img alt="Screenshot 4" src="/img/screenshots/mmorpg-fantasy-metin2-thumb4.jpg" width="100" height="75" /></a></li>
				</ul>
		</div>
	</div>
</div>
<div class="shadow">&nbsp;</div>
<div class="content">
    <div class="content-bg">
        <?php
        $newsList = irisApi::$News->News_List();
        foreach ($newsList as $line){
        echo "
            <div class='content-bg-bottom'>
                <h2>$line->title</h2>
                <div class='inner-content'>
                    <p style='padding: 10px 0 0 0;'>$line->content_Preview</p>
                </div>
            </div>
            <br/>";
        }?>
    </div>
</div>
<div class="shadow">&nbsp;</div>
<div class="content content-last">
    <div class="content-bg">
        <div class="content-bg-bottom">
            <h2>Metin2 - Doğu Aksiyon MMORPG'si</h2>
            <div class="inner-content">
                <p>Ejderha Tanrısı'nın nefesi uzun zamandır Shinsoo, Chunjo ve Jinno diyarlarına hükmediyor. Ancak bu <strong>büyüleyici büyü dünyası</strong> büyük bir tehdit ile karşı karşıya: <strong>Metin Taşları</strong>'nın etkisi sadece kıtada derin yaralar bırakmakla kalmadı, aynı zamanda ülkeye ve sakinlerine kaos ve yıkım getirdi. Diyarlar arasında savaş çıktı, vahşi hayvanlar vahşi canavarlara dönüştü ve ölüler kana susamış yaratıklar olarak dirildi. <strong>Metin Taşları</strong>'nın karanlık etkisine karşı <strong>Ejderha Tanrısı</strong>'nın müttefiki olarak ayağa kalkın. <strong>Gücünü topla ve silahlarını yükselt</strong> imparatorluğunu korku, acı ve yıkımla dolu bir gelecekten koru!</p>
                <h3>Özellikler</h3>
                <ul style="padding-bottom: 0">
                    <li>Cesur savaşçıların sayısız macerada cesaretlerini kanıtlamaları gereken uçsuz bucaksız bir kıta.</li>
                    <li>Cesaretini ve gücünü sunabileceğin üç rakip imparatorluk.</li>
                    <li>Eğitim sırasında düşmanlarını yenmek için çeşitli savaş sanatlarını öğren ve olağanüstü yetenekler edin.</li>
                    <li>Güçlü ve etkili bir loncaya liderlik et ve burada bir kale lordu olarak bir kale inşa etmek için birleşik gücü kullan.</li>
                    <li>Kendi ihtiyaçlarına göre şekillendirebileceğin sayısız silahla kendini donat.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="shadow">&nbsp;</div>


<!--suppress JSUnresolvedReference -->
<script type="text/javascript">
$(document).ready(function(){
	$("#page > div.container-wrapper > div > div.col-2 > div:nth-child(2) > div > div > ul > li > a").overlay({ 
		target: '#gallery',
		expose: '#000'  
	}).gallery({ 
		next:	'.forward',
		prev: 	'.back',
		speed: 800 
	});
});
</script>
