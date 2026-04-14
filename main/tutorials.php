<div class="content content-last">
    <div class="content-bg">
        <div class="content-bg-bottom">
            <h2>Metin2 - Danışma</h2>
            <div class="firststeps-inner-content">
                <ul class="tabs-nav tabs2">
                    <li id="tab1"><a href="<?= iFunctions::IsLocal() ? "?s=howto": "/howto" ?>">İlk Adımlar</a></li>
                    <li id="tab2" class="selected"><a href="<?= iFunctions::IsLocal() ? "?s=tutorials": "/tutorials" ?>">Danışma</a></li>
                </ul>
                <div class="tutorialsbox">
                    <p>Çevrimiçi MMORPG Metin2'ye hoş geldiniz! Giriş yaptıktan sonra ek bilgiler alacak ve çeşitli alemler ve çevrimiçi rol yapma olanakları hakkında bilgi edineceksiniz. Ayrıntılı bir eğitim, sizi MMORPG'nin gizemleriyle tanıştıracak ve akıcı ve heyecanlı bir oyun için gereken tuş kombinasyonlarını adlandıracaktır.</p>
                    <a href="/main/tutorial_createcharacter.php" rel="#overlay" class="tutorial-btn">Bir karakter yaratın</a>
                    <a href="/main/tutorial_introduction.php" rel="#overlay" class="tutorial-btn">Giriş</a>
                </div>
                <div class="box-foot"></div>
            </div>
        </div>
    </div>
</div>
<div class="shadow">&nbsp;</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".tutorialsbox a[rel]").overlay({ 
		target: '#overlay',
		expose: 'black',
		 onBeforeLoad: function() { 
            // grab wrapper element inside content 
            var wrap = this.getContent().find(".contentWrap"); 
 
            // load the page specified in the trigger 
            wrap.load(this.getTrigger().attr("href")); 
        }
	});
});
</script>
