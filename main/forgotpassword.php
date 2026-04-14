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

                            <?php echo "<form action='".(iFunctions::IsLocal() ? ("?s=forgotpassword&token=".$_GET['token']) : ("/forgotpassword?token=".$_GET['token']))."' method='post'>" ?>
                                <div class="form-item">
                                    <label for="password1">Yeni Parola:</label>
                                    <input type="password" id="password1" name="password1" size="16" required><br />
                                </div>
                                <div class="form-item">
                                    <label for="password2">Yeni Parola (tekrar):</label>
                                    <input type="password" id="password2" name="password2" size="16" required><br />
                                </div>
                                <br/>
                                <input id="submitBtn" class="btn-big" type="submit" name="change" value="Değiştir" />
                            </form>

                            <?php
                            if(isset($_POST['change']) && $_POST['change'] == 'Değiştir') {
                                $password1 = $_POST['password1'];
                                $password2 = $_POST['password2'];
                                $iToken    = $_GET['token'];

                                if (strlen($password1) < 6){
                                    echo "<span class='errorText'><strong>Şifre en az 6 karakter olmalı!</strong></span>";
                                    goto EndPhpCode;
                                }
                                if ($password1 != $password2){
                                    echo "<span class='errorText'><strong>Şifreler uyuşmuyor!</strong></font>";
                                    goto EndPhpCode;
                                }

                                $cResult = irisApi::$Account->Forgot_Password_Save($password1, $iToken);
                                if ($cResult->responseCode != 0){
                                    echo "<span class='errorText'><strong>". $cResult->responseMessage ."</strong></span>";
                                    goto EndPhpCode;
                                }

                                echo "<span style='color: green; font-size:16px'><strong>". $cResult->responseMessage ."</strong></font>";
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
