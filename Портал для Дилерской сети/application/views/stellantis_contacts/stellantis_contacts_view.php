<style>
	#stellantis_contacts > div > div{
		overflow-x: auto;
		-moz-transform: scaleY(-1);
		transform: scaleY(-1);
	}
	.tree{
		-moz-transform: scaleY(-1);
		transform: scaleY(-1);
	}

	.tree > ul, .tree > ul > li > ul{
		position: relative;
		display: table;
		padding: 6px 0 0 0 !important;
		line-height: normal;
		text-align: center;
		word-wrap: break-word;
		word-break: break-all;
	}
	.tree > ul{
		/* margin: 7% auto 0px 150px; */
	}
	.tree > ul > li, .tree > ul > li > ul > li{
		position: relative;
		display: table-cell;
	}
	.tree > ul > li:not(:only-child), .tree > ul > li > ul > li:not(:only-child){
		padding: 0 .5em;
	}
	.tree > ul > li:last-child, .tree > ul > li > ul > li:last-child{
		padding-right: 0;
	}
	.tree > ul > li:first-child, .tree > ul > li > ul > li:first-child{
		padding-left: 0;
	}
	.tree > ul > li > ul:before,
	.tree > ul > li > ul > li:before,
	.tree > ul > li > ul > li:after{
		content: "";
		position: absolute;
		top: -5px;
		left: 0;
		width: 50%;
		height: 5px;
		border-right: 2px solid #223464;
	}
	.tree > ul > li > ul:before{
		top: -4px;
	}
	.tree > ul > li > ul > li:not(:only-child):before{
		border-top: 2px solid #223464;
	}
	.tree > ul > li > ul > li:not(:only-child):first-child:before{
		right: 0;
		left: auto;
		border-left: 2px solid #223464;
		border-right: none;
	}
	.tree > ul > li > ul > li:not(:only-child):first-child:before,
	.tree > ul > li > ul > li:not(:only-child):last-child:before{
		width: calc(50% + .5em/2);
	}
	.tree > ul > li > ul > li:after{
		border: none;
	}
	.tree > ul > li > ul > li:not(:last-child):not(:first-child):after{
		width: 100%;
		border-top: 2px solid #223464;
	}


	.tree > ul > li > ul > li > ul{
		margin-left: -40px;
	}
	.tree > ul > li > ul > li > ul ul,
	.tree > ul > li > ul > li > ul li {
		margin: 0;
		padding: 0;
		line-height: 1;
		list-style: none;
	}
	.tree > ul > li > ul > li > ul ul {
		margin: 0 0 0 .5em;
	}
	.tree > ul > li > ul > li > ul > li/*:not(:only-child)*/,
	.tree > ul > li > ul > li > ul li li {
		position: relative;
		padding: .2em 0 0 1.2em;
	}
	.tree > ul > li > ul > li > ul li:not(:last-child) {
		border-left: 2px solid #223464; 
	}
	.tree > ul > li > ul > li > ul li li:before,
	.tree > ul > li > ul > li > ul > li/*:not(:only-child)*/:before { 
		content: "";
		position: absolute;
		top: 0;
		left: 0;
		width: 1.1em; 
		height: .7em; 
		border-bottom: 2px solid #223464;
	}
	.tree > ul > li > ul > li > ul li:last-child:before {
		width: calc(1.1em - 1px); 
		border-left: 2px solid #223464;
	}


	.customer_tree{
		display: grid;
		grid-auto-columns: 1fr;
		grid-template-columns: 30% 70%;
		gap: 0px 10px;
		width: 150px;
		border: 2px solid #223464;
		/* background: white; */
	}
	.tree > ul > li > .customer_tree{
		margin: 0px auto 4px auto;
	}
	.customer_tree > div:last-child{
		align-self: center;
		text-align: left;
		width: 88%;
		overflow-wrap: normal;
		word-wrap: break-word;
		word-break: normal;
		line-break: auto;
		hyphens: manual;
	}
	.customer_tree img{
		width: 50px;
		height: 50px;
	}
	.customer_tree p{
		margin: 0px;
		font-size: 9px;
	}
	.customer_tree p:nth-child(1){
		font-weight: bold;
	}
	.customer_tree p:nth-child(2){
		color: #223464;
	}


	.main_color{
		color: #223464 !important;
	}
	.modal_body_title{
		background: #223464;
		color: white;
		padding: 5px 7px;
		margin-left: 5px;
		display: block;
	}
	.input_as_text{
		border: none;
		background: white;
		padding: 0;
		margin: 0;
		width: 100%;
	}
	.modal-body textarea{
		width: 100%;
		resize: none;
	}
	.put_file{
		margin: 115px 0px 0px 0px;
	}

	input[name='appointment'], input[name='work_phone'], input[name='mobilephone'], input[name='email'], input[name='birthday'], input[name='ext'], input[name='contact_email']{
		width: 100%;
	}

	.birthday{
		position: absolute;
		margin-left: 130px;
		color: dodgerblue;
		margin-top: 2px;
		-webkit-animation: 1.2s ease-in-out 0s normal none infinite running trambling-animation;
		-moz-animation: 1.2s ease-in-out 0s normal none infinite running trambling-animation;
		-o-animation: 1.2s ease-in-out 0s normal none infinite running trambling-animation;
		animation: 1.2s ease-in-out 0s normal none infinite running trambling-animation;
	}
	@keyframes trambling-animation {
		0%, 50%, 100% {
		transform: rotate(0deg);
			-webkit-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			-ms-transform: rotate(0deg);
		}
		10%, 30% {
		transform: rotate(-10deg);
			-webkit-transform: rotate(-10deg);
			-moz-transform: rotate(-10deg);
			-o-transform: rotate(-10deg);
			-ms-transform: rotate(-10deg);
		}
		20%, 40% {
		transform: rotate(10deg);
			-webkit-transform: rotate(10deg);
			-moz-transform: rotate(10deg);
			-o-transform: rotate(10deg);
			-ms-transform: rotate(10deg);
		}
	}

	#export_btn{
		position: absolute;
		margin-top: 15px;
		z-index: 1000;
	}
	.search, #coincidence{
		position: absolute;
		z-index: 1000;
		margin-top: 15px;
		margin-left: 60px;
		height: 31px;
	}
	#coincidence{
		margin-left: 230px;
		display: none;
	}

	.pulse {
		animation: pulse 1.25s infinite;
	}
	@-webkit-keyframes pulse {
		0% {
			-webkit-box-shadow: 0 0 0 0 rgba(34,52,100, 1);
		}
		70% {
			-webkit-box-shadow: 0 0 0 10px rgba(34,52,100, 0);
		}
		100% {
			-webkit-box-shadow: 0 0 0 0 rgba(34,52,100, 0);
		}
	}
	@keyframes pulse {
		0% {
			-moz-box-shadow: 0 0 0 0 rgba(34,52,100, 1);
			box-shadow: 0 0 0 0 rgba(34,52,100, 1);
		}
		70% {
			-moz-box-shadow: 0 0 0 10px rgba(34,52,100, 0);
			box-shadow: 0 0 0 10px rgba(34,52,100, 0);
		}
		100% {
			-moz-box-shadow: 0 0 0 0 rgba(34,52,100, 0);
			box-shadow: 0 0 0 0 rgba(34,52,100, 0);
		}
	}

	.null_date{
		cursor: pointer;
		margin-left: 3px;
	}


	input[name="file"]{
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		width: 100%;
		padding: 0;
		margin: 0;
		cursor: pointer;
		filter: alpha(opacity=0);
		opacity: 0;
	}

	.btn_file{
		margin-top: 20px;
	}

	.validate {
		background: none;
		border: none;
	}
</style>

<div class="content-wrapper">
	<section id="stellantis_contacts">
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-12 tree">
					<?php if( $type_info == 'error' ):?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<?=$info?>
						</div>
					<?php endif; ?>

					<?php if( $type_info == 'success' ): ?>
						<div class="alert alert-success alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<?=$info?>
						</div>
					<?php endif; ?>

					<form id="excel_report" action="<?= base_url('stellantis_contacts/excel_report') ?>" method="POST"></form>

					<button type="submit" form="excel_report" id="export_btn" class="btn btn-primary" data-toggle="tooltip" title="" data-original-title=" Експорт в Excel"><i class="fa fa-download"></i></button>

					<input type="text" class="search" placeholder="пошук">

					<h3 id="coincidence"></h3>

					<?= $tree ?>
				</div>
			</div>
		</div>
	</section>
</div>

<div id="customer_card" class="modal fade" style="z-index: 1030;">
	<div class="modal-dialog" role="document">
		<div class="modal-content diler">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"> <span class="name_diler"> </span> </h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-xs-4"> <img class="img_diler" src="" alt="" style="width: 100%;"> </div>
					<div class="col-xs-8">
						<span class="modal_body_title dept_diler"> </span><span class="modal_body_title appointment_diler"> </span>
						<hr>
						<h4><?= $lang->line('sc_contact_information') ?></h4>
						<div class="col-xs-1 main_color"> <i class="fa fa-phone" aria-hidden="true"></i> </div><div class="col-xs-11 work_phone_diler"> </div>
						<div class="col-xs-1 main_color"> <i class="fa fa-volume-control-phone" aria-hidden="true"></i> </div><div class="col-xs-11 mobile_phone_diler"> </div>
						<div class="col-xs-1 main_color"> <i class="fa fa-envelope-o" aria-hidden="true"></i> </div><div class="col-xs-11 email_diler"> </div>
						<div class="col-xs-1 main_color"> <i class="fa fa-birthday-cake" aria-hidden="true"></i> </div><div class="col-xs-11 birthday_diler"> </div>
						<div class="col-xs-1 main_color"> EXT </div><div class="col-xs-11 ext_diler"> </div>
					</div>
					<div class="col-xs-12">
						<h4> <?= $lang->line('sc_cart_footer') ?> </h4>
						<p class="text_diler"></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"> <?= $lang->line('sc_close') ?> </button>
				<?php if( $_SESSION['access_employees'] ): ?>
					<button class="btn btn-info toogle_write"><i class="fa fa-pencil" aria-hidden="true"></i></button>
				<?php endif; ?>
			</div>
		</div>
		<div class="modal-content admin">
			<form action="<?= base_url('stellantis_contacts/update_employee') ?>" method="POST" enctype="multipart/form-data">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="gridSystemModalLabel">
						<input type="text" name="name" value=""><input type="text" name="last_name" value="">
						<input type="hidden" name="id" value="">
					</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-4" style="text-align: center">
							<input type="hidden" name="old_img" value=""> <img src="" alt="" style="width: 100%;">
							<div class="btn btn-rounded btn-primary btn_file">
								<span> <?= $lang->line('sc_photo') ?> </span>
								<input type="file" name="file">
							</div>
							<div class="file-path-wrapper">
								<input class="file-path validate valid" type="text">
							</div>
						</div>
						<div class="col-xs-8">
							<select name="dept">
								<option value="1">UKR/ADFI</option>
								<option value="2">UKR/ADFI/COMP</option>
								<option value="3">UKR/ADFI/GES</option>
								<option value="4">UKR/BTOB</option>
								<option value="5">UKR/LOG</option>
								<option value="6">UKR/MKT</option>
								<option value="7">UKR/MKT/PPD</option>
								<option value="8">UKR/OPUA</option>
								<option value="9">UKR/PSE</option>
								<option value="10">UKR/PSE/ARES</option>
								<option value="11">UKR/PSE/GFHT</option>
								<option value="12">UKR/PSE/LDPR</option>
								<option value="13">UKR/QDEV</option>
								<option value="14">UKR/VNTE</option>
							</select>
							<input type="text" name="appointment" value="">

							<hr>
							<h4> <?= $lang->line('sc_contact_information') ?> </h4>
							<div class="col-xs-1 main_color"> <i class="fa fa-phone" aria-hidden="true"></i> </div><div class="col-xs-11"> <input type="text" name="work_phone"></div>
							<div class="col-xs-1 main_color"> <i class="fa fa-volume-control-phone" aria-hidden="true"></i> </div><div class="col-xs-11"> <input type="text" name="mobilephone" style="width: 94%;" disabled> <input type="checkbox" name="close_cellphone" data-toggle="tooltip" title="" data-original-title=" Приховати відображення"> </div>
							<div class="col-xs-1 main_color"> <i class="fa fa-envelope-o" aria-hidden="true"></i> </div><div class="col-xs-11"> <input type="text" name="email"></div>
							<div class="col-xs-1 main_color"> <i class="fa fa-envelope" aria-hidden="true"></i> </div><div class="col-xs-11"> <input type="text" name="contact_email" data-toggle="tooltip" title="" data-original-title=" Приховати ел. адресу, і показати саму цю"></div>
							<div class="col-xs-1 main_color"> <i class="fa fa-birthday-cake" aria-hidden="true"></i> </div><div class="col-xs-11"> <input type="date" name="birthday" style="width: 94%;"> <i class="fa fa-times null_date" aria-hidden="true"></i> </div>
							<input type="hidden" name="ext">
						</div>
						<div class="col-xs-12">
							<h4> <?= $lang->line('sc_cart_footer') ?> </h4>
							<textarea name="text"> </textarea>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"> <?= $lang->line('sc_close') ?> </button>
					<button type="button" class="btn btn-info toogle_read"><i class="fa fa-eye" aria-hidden="true"></i></button>
					<button type="submit" class="btn btn-primary"> <?= $lang->line('sc_save') ?> </button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- <button type="submit" form="excel_report" id="export_btn" class="btn btn-primary" data-toggle="tooltip" title="" data-original-title=" Експорт в Excel"><i class="fa fa-download"></i></button> -->
<script>
	$('.customer_tree').click(function(event) {
		//temp
		$('input[type="file"]').change(function(event) {
			var splittedFakePath = this.value.split('\\');
			$('.file-path').val(splittedFakePath[splittedFakePath.length - 1]);
		});

		let data = $(this).data("item");

		// diler name_diler
		let dept;
		if( data.New_dept == 1 ){ dept = 'UKR/ADFI'; } else if( data.New_dept == 2 ){ dept = 'UKR/ADFI/COMP'; } else if( data.New_dept == 3 ){ dept = 'UKR/ADFI/GES'; } else if( data.New_dept == 4 ){ dept = 'UKR/BTOB'; } else if( data.New_dept == 5 ){ dept = 'UKR/LOG'; } else if( data.New_dept == 6 ){ dept = 'UKR/MKT'; } else if( data.New_dept == 7 ){ dept = 'UKR/MKT/PPD'; } else if( data.New_dept == 8 ){ dept = 'UKR/OPUA'; } else if( data.New_dept == 9 ){ dept = 'UKR/PSE'; } else if( data.New_dept == 10 ){ dept = 'UKR/PSE/ARES'; } else if( data.New_dept == 11 ){ dept = 'UKR/PSE/GFHT'; } else if( data.New_dept == 12 ){ dept = 'UKR/PSE/LDPR'; } else if( data.New_dept == 13 ){ dept = 'UKR/QDEV'; } else if( data.New_dept == 14 ){ dept == 'UKR/VNTE'; }

		$('.name_diler').text( data.New_name+' '+data.New_lastname );

		$('.img_diler').attr('src', $(this).find('img').attr('src') );

		$('.dept_diler').text( dept );
		$('.appointment_diler').text( data.New_appointment );

		$('.work_phone_diler').text( '| ' + data.New_work_phone );

		if( !data.New_close_cellphone ){
			$('.mobile_phone_diler').text( '| ' + data.New_mobilephone );
		} else{
			$('.mobile_phone_diler').text( '| ' );
		}


		let email = '';
		if( <?= $access_employees ?> != 1 ){
			if( data && data.New_contact_email ){
				email = data.New_contact_email;
			} else{
				email =data.New_email;
			}
		} else{
			email =data.New_email;
		}
		$('.email_diler').text( '| ' + email );

		let date_str = '';
		if (data && data.New_birthday) {
			let arr = data.New_birthday.split('-');
			if (arr.length == 3) date_str = arr[2] + '.'+arr[1] + '.' + arr[0];
		}
		
		$('.birthday_diler').text( '| ' + date_str );
		$('.ext_diler').text( '| ' + data.New_ext );

		$('.text_diler').text( data.New_text );
		// diler name_diler


		$('#customer_card .modal-header input[name="name"]').val( data.New_name );
		$('#customer_card .modal-header input[name="last_name"]').val( data.New_lastname );

		$('#customer_card .modal-header input[name="id"]').val( data.New_web_userId );

		$('select[name="dept"] option').each(function(index, el) {
			if( $(this).val() == data.New_dept ){
				$(this).attr('selected', 'selected');
			} else{
				$(this).removeAttr('selected');
			}
		});

		$('#customer_card .modal-body input[name="appointment"]').val( data.New_appointment );

		$('#customer_card .modal-body input[name="work_phone"]').val( data.New_work_phone );
		$('#customer_card .modal-body input[name="mobilephone"]').val( data.New_mobilephone );
		$('#customer_card .modal-body input[name="email"]').val( data.New_email );
		$('#customer_card .modal-body input[name="contact_email"]').val( data.New_contact_email );
		$('#customer_card .modal-body input[name="birthday"]').val( data.New_birthday );
		$('#customer_card .modal-body input[name="ext"]').val( data.New_ext );
		$('#customer_card textarea[name="text"]').text( data.New_text );

		if( data.New_close_cellphone ){
			$('#customer_card .modal-body input[name="close_cellphone"]').prop('checked', true);
		} else{
			$('#customer_card .modal-body input[name="close_cellphone"]').prop('checked', false);
		}

		$('#customer_card .modal-body input[name="old_img"]').val( $(this).find('img').attr('src') );
		$('#customer_card .modal-body input[name="old_img"] + img').attr( 'src', $(this).find('img').attr('src') );

		$('#customer_card').modal({
			backdrop: false,
			keyboard: true,
			show: false
		});
		$('#customer_card').modal('toggle');
	});

	$('.admin').hide();
	$('.toogle_write').click(function(event) {
		$('.admin').show();
		$('.diler').hide();
	});
	$('.toogle_read').click(function(event) {
		$('.admin').hide();
		$('.diler').show();
	});

	$('.customer_tree').hover(function() {
		$(this).css({
			'transition': '0.3s',
			'background': '#223464',
			'color': 'white',
			'cursor': 'pointer'
		});
		$(this).find('p:nth-child(2)').css({
			'transition': '0.3s',
			'color':'white'
		});
	}, function() {
		$(this).css({
			'transition': '0.3s',
			'background': '',
			'color': '',
			'cursor': ''
		});
		$(this).find('p:nth-child(2)').css('color','');
	});

	$('.null_date').click( function() {
		$('#customer_card .modal-body input[name="birthday"]').val('');
	})

	// search
	$('#stellantis_contacts .search').keyup(function(event) {
		let search = $(this).val().toLowerCase();
		if( search.length > 2 ){
			let count = 0;
			$('.customer_tree').each(function(index, el) {
				let item = $(this).data("item");
				let work_phone = '';
				let mobilephone = '';
				let email = '';
				let appointment = '';

				let name = item.New_name.toLowerCase()+' '+item.New_lastname.toLowerCase();

				if (item &&  item.New_work_phone) {
					work_phone = item.New_work_phone.toLowerCase();
				}
				if (item &&  item.New_mobilephone) {
					mobilephone = item.New_mobilephone.toLowerCase();
				}
				if (item &&  item.New_email) {
					email = item.New_email.toLowerCase();
				}
				if (item &&  item.New_appointment) {
					appointment = item.New_appointment.toLowerCase();
				}

				if( name.includes( search.toLowerCase() ) || work_phone.includes( search.toLowerCase() ) || mobilephone.includes( search.toLowerCase() ) || email.includes( search.toLowerCase() ) || appointment.includes( search.toLowerCase() ) ){
					$(this).addClass('pulse');
					count++;
				} else {
					$(this).removeClass('pulse');
				}
			});

			$('#coincidence').text( 'збіг:' + count ).show();
		} else{
			$('.customer_tree').each(function(index, el) {
				$(this).removeClass('pulse');
				$('#coincidence').hide();
			});
		}
	});
	// 
</script>