<div class="modul-box">
	<div class="modul-box-bg">
		<div class="modul-box-bg-bottom">
			<h3>Giriş</h3>
			<form action="<?= iFunctions::IsLocal() ? "?s=login": "/login" ?>" method="post">
				<div class="form-login">
					<label for="loginName"> Kullanıcı adı:</label>
					<div class="input"><input type="text" id="loginName" name="loginName" required /><br /></div>
					<label for="loginPass"> Şifre:</label>
					<div class="input"><input type="password" id="loginPass" name="loginPass" required /><br /></div>
					<div>
						<input type="submit" class="button btn-login" name="login" value="Login">
						<p class="agbok">
                            Giriş yaparak
                            <a href="/imprint.html" target="_blank"><strong>kullanım şartları</strong></a>'nı kabul etmiş olursunuz .
						    <a href="<?= iFunctions::IsLocal() ? "?s=passwordlost": "/passwordlost" ?>" class="password">Parolanızı mı unuttunuz?</a>
						</p>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
