<div class="content content-last">
	<div class="content-bg">
		<div class="content-bg-bottom">
			<h2>Metin2 - Profil</h2>
			<div class="administration-inner-content">
				<div class="input-data-box">
					<h4>Detaylar</h4>
					<?php
                        echo "
                            <ul>
                            <li>Kullanıcı adı: <span class='offset'>"    . $_SESSION['login_name']         ."</span></li>
                            <li>Durum: <span class='offset'>"            . $_SESSION['status']             ."</span></li>
                            <li>Ejderha Parası <span class='offset'>"    . number_format($_SESSION['cash'])."</span><br/></li>
                            <li>Gerçek adı: <span class='offset'>"       . $_SESSION['account_name']       ."</span></li>
                            <li>E-posta adresiniz: <span class='offset'>". $_SESSION['email']              ."</span></li>
                            <li>Karakter silme kodu: <strong> "          . $_SESSION['delete_code']        ."</strong></li>
                            </ul>
                        ";
					?>
                    <div class="administration-box">
                        <a href="<?= iFunctions::IsLocal() ? "?s=characters": "/characters" ?>" class="btn">Karakterler</a>
                    </div>
					<div class="administration-box">
						<a href="<?= iFunctions::IsLocal() ? "?s=pwchange": "/pwchange" ?>" class="btn">Bilgileri Değiştir</a>
						<p>Hesap şifrenizi veya karakter silme kodunuzu değiştirin.</p>
					</div>
                    <div class="administration-box">
                        <a href="<?= iFunctions::IsLocal() ? "?s=accrecover": "/accrecover" ?>" class="btn">Karakter Kurtar</a>
                        <p>Karakterleri bug'dan kurtar.</p>
                    </div>
				</div>
				<div class="box-foot"></div>
			</div>
		</div>
	</div>
</div>
<div class="shadow">&nbsp;</div>
