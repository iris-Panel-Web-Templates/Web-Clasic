<style>
    .errorText {
        color: #5a0c0c;
        font-size:16px;
        animation: color-change 1s infinite;
    }
    @keyframes color-change {
        0% { color: #b11515; }
        50% { color: #801111; }
        100% { color: #5a0c0c; }
    }
</style>

<div id="register">
	<div class="content content-last">
		<div class="content-bg">
			<div class="content-bg-bottom">
				<h2>Metin2 - Verileri değiştir</h2>
				<div class="inner-form-border">
					<div class="inner-form-box">
						<div class="trenner"></div>
                        <div class="center">
                            <form action="?s=pwchange" method="post">
                                <div class="form-item">
                                    <label for="oldpw">Eski parola:</label>
                                    <input type="password" id="oldpw" name="oldpw" size="16" required><br />
                                </div>
                                <div class="form-item">
                                    <label for="newpw">Yeni parola:</label>
                                    <input type="password" id="newpw" name="newpw" size="16" required><br />
                                </div>
                                <div class="form-item">
                                    <label for="newpw2">Yeni parola tekrar:</label>
                                    <input type="password" id="newpw2" name="newpw2" size="16" required><br />
                                </div>
                                <div class="form-item">
                                    <label for="lcold">Eski karakter silme kod:</label>
                                   <?php echo "<input type=\"text\" id=\"lcold\" name=\"lcold\" size=\"7\" value=\"".$_SESSION['delete_code']."\" required><br />" ?>
                                </div>
                                <div class="form-item">
                                    <label for="lcnew">Yeni karakter silme kodu:</label>
                                    <?php echo "<input type=\"text\" id=\"lcnew\" name=\"lcnew\" size=\"7\" value=\"".$_SESSION['delete_code']."\" required><br />" ?>
                                </div>
                                <br/>
                                <input id="submitBtn" class="btn-big" type="submit" name="change" value="Değiştir" />
                            </form>

                            <?php
                                if(isset($_POST['change']) && $_POST['change'] == 'Değiştir') {
                                    $passwordOld = $_POST['oldpw'];
                                    $passwordNew1 = $_POST['newpw'];
                                    $passwordNew2 = $_POST['newpw2'];
                                    $deleteCodeOld = $_POST['lcold'];
                                    $deleteCodeNew = $_POST['lcnew'];

                                    if (strlen($deleteCodeOld) != 0 && strlen($deleteCodeNew) != 0 && $deleteCodeOld != $deleteCodeNew) {
                                        if (strlen($deleteCodeNew) != 7){
                                            echo "<span class='errorText'><strong>Karakter silme kodu 7 karakter olmalı!</strong></span>";
                                            goto EndPhpCode;
                                        }
                                        if (!ctype_alnum($deleteCodeNew)){
                                            echo "<span class='errorText'><strong>karakter silme kodu sadece rakamlardan oluşmalı!</strong></span>";
                                            goto EndPhpCode;
                                        }
                                    }
                                    if (strlen($passwordNew1) < 6){
                                        echo "<span class='errorText'><strong>Şifre en az 6 karakter olmalı!</strong></font>";
                                        goto EndPhpCode;
                                    }
                                    if ($passwordNew1 != $passwordNew2){
                                        echo "<span class='errorText'><strong>Şifreler uyuşmuyor!</strong></font>";
                                        goto EndPhpCode;
                                    }

                                    $cResult = irisApi::$Account->Change_Password($passwordOld, $passwordNew1, $deleteCodeOld, $deleteCodeNew);
                                    if ($cResult->responseCode != 0){
                                        echo "<span class='errorText'><strong>$cResult->responseMessage</strong></span>";
                                        goto EndPhpCode;
                                    }

                                    echo "<span style='color: green; font-size:16px'><strong>Veriler başarıyla değiştirildi!</strong></font>";
                                    EndPhpCode:
                                }
                            ?>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="shadow">&nbsp;</div>
</div>
