<style>
    .downloadLink {
        display: inline-block;
        background:url("../img/download-client.jpg") no-repeat scroll 0 0 transparent;
        color:#FFF9C7;
        font-size:16px;
        height:53px;
        line-height:20px;
        margin:10px auto;
        padding:10px 8px 0 60px;
        text-decoration:none;
        width:110px;
        text-align: left;
    }
    .downloadLink:hover {
        background-position: 0 -63px;
        color:#FFF;
    }
</style>

<div id="download">
	<div class="content content-last">
		<div class="content-bg">
			<div class="content-bg-bottom">
				<h2>Metin2 - İndir</h2>
				<div class="download-inner-content">
					<h3>Metin2'yi hemen ücretsiz indirin!</h3>
                    <div style="padding: 15px; clear: both; display: flex;flex-wrap: wrap;">
                    <?php
                    $downList = irisApi::$Downloads->Downloads_Get();
                    foreach ($downList as $line) {
                        echo "<a 
                                href=\"$line->url\" 
                                class='downloadLink' 
                                onClick=\"DownloadTracker('$line->url');\"> 
                                ".$line->name."
                              </a>";
                    }
                    ?>
                    </div>
					<br class="clearfloat" />
					<a href="javascript:void(0)" id="requirements">» Sistem gereksinimleri</a>
					<div id="required">
						<table border="0">
							<caption>Minimum sistem gereksinimleri</caption>
							<tbody>
								<tr><td class="left_td">OS          </td><td>- Win XP, Win 2000, Win Vista, Win 7</td></tr>
								<tr><td class="left_td">CPU         </td><td>- Pentium 3 1GHz</td></tr>
								<tr><td class="left_td">hafıza      </td><td>- 512M</td></tr>
								<tr><td class="left_td">Sabit disk  </td><td>- 1 GB</td></tr>
								<tr><td class="left_td">Grafik kartı</td><td>- 32MB'den fazla RAM'e sahip grafik kartı</td></tr>
								<tr><td class="left_td">Ses kartı   </td><td>- DirectX 9.0 destekli</td></tr>
								<tr><td class="left_td">fare        </td><td>- Windows uyumlu fare</td>
                            </tbody>
						</table>
						<table border="0" >
							<caption>Önerilen sistem ön koşulu</caption>
							<tbody>
								<tr><td class="left_td">OS          </td><td>- Win XP, Win 2000, Win Vista, Win 7</td></tr>
								<tr><td class="left_td">CPU         </td><td>- Pentium 4 1.8GHz</td></tr>
								<tr><td class="left_td">hafıza      </td><td>- 1G</td></tr>
								<tr><td class="left_td">Sabit disk  </td><td>- 2 GB</td></tr>
								<tr><td class="left_td">Grafik kartı</td><td>- 64MB'den fazla RAM'e sahip grafik kartı</td></tr>
								<tr><td class="left_td">Ses kartı   </td><td>- DirectX 9.0 destekli</td></tr>
								<tr><td class="left_td">fare        </td><td>- Windows uyumlu fare</td>
							</tbody>
						</table>
					</div>
					<p id="downloadText">Yetersiz grafik kartı belleği FPS kaybına yol açabilir. Sorunu önlemek için oyun ayarlarını yapılandırın. Eğer birçok kullanıcı aynı anda İstemciyi indiriyorsa, indirme hızı yavaşlayabilir. Bu durumda sabrınızı rica ediyoruz.</p>

					<script type="text/javascript">
						$(document).ready(function() {
							$('#requirements').click(function(){
								$('#required').slideToggle();
							});
						});
					</script>
					<div class="download-box-foot"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="shadow">&nbsp;</div>
    <script>
        function DownloadTracker(downUrl){

        }
    </script>
</div>
