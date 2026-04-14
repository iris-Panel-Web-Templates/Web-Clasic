<div id="pwLost">
	<div class="content content-last">
		<div class="content-bg">
			<div class="content-bg-bottom">
				<h2>Şifrenizi mi unuttunuz??</h2>
				<div class="inner-form-border">
					<div class="inner-form-box">
						<h3><a id="toLogin" href="<?= iFunctions::IsLocal() ? "?s=login": "/login" ?>" title="Giriş yap">Giriş yap</a>Şifreyi E-posta ile gönder:</h3>
						<div class="trenner"></div>
						<form name="pwlostForm" id="pwlostForm" method="post" action="<?= iFunctions::IsLocal() ? "?s=passwordlost": "/passwordlost" ?>">

							<div class="form-item">
								<label for="username">Kullanıcı adı: *</label>
								<input type="text" class="validate[required,custom[noSpecialCharacters],length[5,16]]" id="username" name="username" title="" value="" maxlength="16" required />
							</div>
							<div class="form-item">
								<label for="email">Email: *</label>
								<input type="text" class="validate[required,custom[email]]" id="email" name="email" title="" value="" maxlength="64" required />
							</div>
							<input id="submitBtn" type="submit" name="SubmitPasswordLostForm" value="Yeni bir şifre isteyin" class="btn-big" />
						</form>
						<p id="regLegend">* gerekli</p>

                        <?php
                        if (isset($_GET['fail'])) {
                            echo "<div class='center'><span style='color:darkred; font-size:16px'><strong>".$_SESSION['last_error']."</strong></span></div>";
                            $_SESSION['last_error'] = "";
                        }
                        if (isset($_GET['success'])) {
                            echo "<div class='center'><span style='color:darkgreen; font-size:16px'><strong>".$_SESSION['last_error']."</strong></span></div>";
                            echo "<script type=\"text/javascript\" language=\"javascript\">document.getElementById(\"submitBtn\").style.display = \"none\";</script>";
                            $_SESSION['last_error'] = "";
                        }
                        ?>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="shadow">&nbsp;</div>
</div>
<?php
	if(isset($_POST['SubmitPasswordLostForm']) && $_POST['SubmitPasswordLostForm'] == 'Yeni bir şifre isteyin') {
		$loginName = $_POST['username'];
		$email = $_POST['email'];
		$phone = "";

        $goFailUrl = iFunctions::IsLocal() ? "?s=passwordlost&fail=true": "/passwordlost?fail=true";
        if(strlen($loginName) == 0) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = "Kullanıcı adı boş bırakılamaz!";
            exit;
        }
        if(strlen($loginName) <  3) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = "Kullanıcı adı geçersiz!";
            exit;
        }

        if(strlen($email) == 0) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = "Email boş bırakılamaz!";
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = "Geçersiz e-posta adresi!";
            exit;
        }

        $eDomain = substr(strrchr($email, "@"), 1);
        if (!checkdnsrr($eDomain, "MX")) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = "Domain geçersiz! (posta sunucusu yok)";
        }

        $tResult = irisApi::$Account->Forgot_Password_Send($loginName, $email, $phone);
        if($tResult->responseCode != 0) {
            echo "<meta http-equiv='refresh' content='0; URL=$goFailUrl'>";
            $_SESSION['last_error'] = $tResult->responseMessage;
            exit;
        }

        $goSuccessUrl = iFunctions::IsLocal() ? "?s=passwordlost&success=true": "/passwordlost?success=true";
        echo "<meta http-equiv='refresh' content='0; URL=$goSuccessUrl'>";
        $_SESSION['last_error'] = $tResult->responseMessage;
	}
?>