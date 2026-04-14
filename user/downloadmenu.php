<div class="header-box">
	<div id="regBtn"> 
		<a id="toReg" href="<?= iFunctions::IsLocal() ? "?s=register": "/register" ?>" title="Hemen kayıt ol ve Metin2'yi indir!">Hemen kayıt ol ve Metin2'yi indir!</a>
		<div id="regSteps">
			<a href="<?= iFunctions::IsLocal() ? "?s=register": "/register" ?>" title="1. Kayıt   »   2. İndir ve kur   »   3. Metin2 oyna!">
                <span>1. Kayıt   »   2. İndir ve kur   »   3. Metin2 oyna!</span>
            </a>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#regBtn').hover(
	  function () { $(this).addClass("reg-hover"); },
	  function () { $(this).removeClass("reg-hover"); }
	);
</script>
