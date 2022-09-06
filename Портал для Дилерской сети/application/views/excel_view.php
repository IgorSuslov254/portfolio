<div class="content-wrapper" style="min-height: 335px;">
	<div id="excel_main" class="col-xs-12" style="margin-top: 20px;">

		<div class="box box-warning">

			<!-- main title -->
			<div class="box-header f-r-c-s">

				<!-- title -->
				<h3 class="box-title">
					<i class="fa fa-file" style="color: red; font-size: 24px;"></i>
					<a href='https://reports.crm.servicedesk.in.ua/ReportForm.aspx?organization=citroen&reportname=FunnelOfLeadGeneration' target="_blank" class="box-title" style="color: inherit">Завантажте файл Excel з базою візитів на СТО</a>
				</h3><!-- ./ title -->

				<!-- btn info -->
				<button type="button" class="btn btn-box-tool f-r-c-c" data-toggle="tooltip" title="" data-original-title="" style="opacity: 0;">
					<i class="fa fa-question-circle"></i>
				</button><!-- ./ btn info -->

				<!-- btn plus -->
				<div class="f-g-1 btn-my-plus">
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
						</button>
					</div>
				</div><!-- ./ btn plus-->

			</div><!-- ./ main title -->

			<style>
				.f-exel_l{
					width: 220px;
					margin-right: 20px;
				}
				.f-exel_r{
					max-width: calc(100% - 220px - 20px);
					background-color: #ccc;
					padding: 10px;
				}
				.upload-file input{
					 display: none;
				}
				/* .f-exel_r_r,
				.f-exel_r_l{
					width: calc(50% - 20px)
				} */
				.f-exel_r_l{
					margin-right: 20px;
				}
				.f-exel-info{
					margin-bottom: 5px;
				}

				.f-exel-info-c{
					width: 22px;
					height: 22px;
					border-radius: 50%;
					background-color: #223464;
					margin-right: 10px;
				}
				.f-exel-info-txt{
					width: calc(100% - 22px - 10px);
				}
				.f-exel-info-txt h4{
					color: #223464;
					font-weight: 700;
					margin: 0;
					text-transform: uppercase;
				}
				.f-exel-info-txt ul{
					padding: 10px;
				}
				.f-r-s-sb{
					display: flex;
					flex-direction: row;
					align-items: flex-start;
					justify-content: flex-start;
				}
				.f-exel-info-c{
					color: #FFFFFF;
					font-size: 12px;
					line-height: 0.75;
				}
				.f-exel_r_r{
					padding: 10px;
					background-color: #223464;
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: flex-start;
				}
				.f-exel_r_r i{
					font-size: 38px;
					color: #FFFFFF;
					margin-bottom: 8px;
				}
				.f-exel_r_r h4{
					font-weight: 700;
					color: #FFFFFF;
					margin-bottom: 8px;
					text-transform: uppercase;
					text-align: center;
				}
				.f-exel_r_r a{
					text-decoration: none;
					padding: 10px;
					background-color: #FFFFFF;
					color: #223464;
					font-size: 16px;
					margin-bottom: 8px;
					width: 100%;
					text-align: center;
				}
				.f-exel_r_r p{
					width: 100%;
					text-align: left;
					color: #FFFFFF;
				}
				.f-exel_download_top{
					border: 1px solid #f1f1f1;
					border-bottom: none;
				}
				.mailbox-attachment-info{
					border: 1px solid #f1f1f1;
					border-top: none;
				}

				@media screen and (max-width: 768px){
					.form-group-excel,
					.f-exel_r{
						flex-direction: column;
						align-items: flex-start;
						justify-content: flex-start;
					}

					.f-exel_r > div,
					.form-group-excel > div{
						width: 100%;
					}
				}
			</style>

			<!-- <h1>Идут техничексие работы!</h1>
			<h2>Не загружайте файл до окончания технических работ</h2>-->
			
			<!-- box-body -->
			<div class="box-body">
                <div class="form-group form-group-excel f-r-s-sb">

					<!-- left -->
					<div class="f-exel_l f-c-s-s">

					<!-- download -->
					<div class="f-exel_download w-100">
						
						<!-- top  -->
						<div class="f-exel_download_top f-r-c-c w-100">
							<span class="mailbox-attachment-icon"><i class="fa fa-file-word-o"></i></span>
						</div>
						<!-- ./ top  -->

						<!-- bottom -->
						<div class="f-exel_download_bottom f-c-s-s w-100">

							<div class="mailbox-attachment-info w-100" style="margin-bottom: 15px;">
								<a id='this-file-size' href="/../../template/template.xlsx" class="mailbox-attachment-name"><i class="fa fa-paperclip"></i> Шаблон.xlsx</a>
								<span class="mailbox-attachment-size">
								11,2 KB
								<a id="template" href="/../../template/template.xlsx" class="btn btn-default btn-xs pull-right"><i class="fa fa-cloud-download"></i></a>
								</span>
							</div>

							<form class="w-100" style='display: flex; flex-direction: row; align-items: flex-start; justify-content: space-between;' action="<?= site_url('Excel/export_excel'); ?>" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
								<label class="upload-file f-c-s-s" style="padding: 5px 0;">
									<input id="this-file" type="file" name="exel" required>
									<p class="btn btn-default btn-xs pull-right">Виберіть файл</p>
									<span id='file-name' style='font-weight: 400; margin-top: 6px;'>Файл не вибрано</span>
								</label>
								<button type="submit" class="btn btn-primary pull-right" id="excel_button">Надіслати</button>
							</form>
						</div>
						<!-- bottom -->

					</div>
					<!-- ./ download -->

					</div>
					<!-- ./ left -->

					<!-- right -->
					<div class="f-exel_r f-r-s-sb">
						<!-- left -->
						<div class="f-exel_r_l f-c-s-s">
							
							<!-- item -->
							<div class="f-exel-info w-100 f-r-s-sb">
								<div class="f-exel-info-c f-r-c-c">
									<i class="fa fa-info"></i>
								</div>
								<div class="f-exel-info-txt">
									<h4>Перед завантаженням *БД:</h4>
									<ul style="list-style-type: decimal;">
										<li>скачайте діючий шаблон;</li>
										<li>скопіюйте в нього базу візитів на СТО.</li>
									</ul>
								</div>
							</div>
							<!-- ./ item -->

							<!-- item -->
							<div class="f-exel-info w-100 f-r-s-sb">
								<div class="f-exel-info-c f-r-c-c">
									<i class="fa fa-edit"></i>
								</div>
								<div class="f-exel-info-txt">
									<h4>Шапка файлу</h4>
									<ul style="list-style-type: none; padding-left: 0;">
										<li>(перші 2 рядки) повинна збігатися з шаблоном</li>
									</ul>
								</div>

							</div>
							<!-- ./ item -->

							<!-- item -->
							<div class="f-exel-info w-100 f-r-s-sb">
								<div class="f-exel-info-c f-r-c-c">
									<i class="fa fa-edit"></i>
								</div>
								<div class="f-exel-info-txt">
									<h4>Назва файлу:</h4>
									<ul style="list-style-type: none; padding-left: 0;">
										<li>{RRDI}_{ГГГГ_ММ}_{SERVICE}</li>
										<li><span style="color: #223464">Приклад:</span> CUAXXXG_2020_09_SERVICE</li>
									</ul>
								</div>
							</div>
							<!-- ./ item -->
						</div>
						<!-- ./ left -->

						<!-- right -->
						<div class="f-exel_r_r f-c-s-c">
							<i class="fa fa-envelope"></i>
							<h4>Ел. адреса відповідального за якість</h4>
							<a href="maito: <?php print_r($PrimaryContact);?>"><?php print_r($PrimaryContact);?></a>
							<p>сюди буде відправлена база з аналізом даних</p>
						</div>
						<!-- ./ right -->
					</div>
					<!-- ./ right -->
                </div>
            </div>
		</div>
	</div>
</div>

<script>
	// this-file-size
	document.addEventListener('DOMContentLoaded', function() {
		let file = document.getElementById('this-file');
		file.addEventListener('input', function(){
			document.getElementById('file-name').innerHTML = this.files[0].name;
		})
	});
</script>
