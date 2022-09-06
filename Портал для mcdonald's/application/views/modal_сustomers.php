<div class="modal fade" id="modal_сustomers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify modal-lg modal-info" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Додати клієнта</h2>
			<hr>
			<form id="modal_сustomers_form">
				<div class="container">
					<div class="row">
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_suname">Прізвище</label>
							<input type="text" id="modal_сustomers_form_suname" class="form-control" name="modal_сustomers_form_suname" required>
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_name">Ім'я</label>
							<input type="text" id="modal_сustomers_form_name" class="form-control" name="modal_сustomers_form_name" required>
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_father_name">По-батькові</label>
							<input type="text" id="modal_сustomers_form_father_name" class="form-control" name="modal_сustomers_form_father_name">
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_phone">Мобільний телефон</label>
							<input type="tel" id="modal_сustomers_form_phone" class="form-control" name="modal_сustomers_form_phone" required>
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_email">Email</label>
							<input type="email" id="modal_сustomers_form_email" class="form-control" name="modal_сustomers_form_email" required>
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="modal_сustomers_form_city">Населений пункт</label>
							<input type="text" id="modal_сustomers_form_city" class="form-control" name="modal_сustomers_form_city" placeholder="Почніть вводити" required autocomplete="off">
							<input type="hidden" id="modal_сustomers_form_city_value" name="modal_сustomers_form_city_value" value=".">
							<div id="modal_сustomers_form_city_hide"></div>
						</div>
						<!-- <div class="col-sm-6 mt-3">
							<select id="modal_сustomers_form_region" name="modal_сustomers_form_region" class="mdb-select md-form" searchable="Поиск">
								<option value="" disabled selected>Область</option>
								<?php foreach($ComboBox_model['new_region'] as $key => $new_region):?>
									<option value="<?= $new_region['new_regionId']; ?>"><?= $new_region['new_name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="col-sm-6 mt-3">
							<select id="modal_сustomers_form_district" name="modal_сustomers_form_district" class="mdb-select md-form" searchable="Поиск">
								<option value="" disabled selected>Район</option>
								<?php foreach($ComboBox_model['new_district'] as $key => $new_district):?>
									<option value="<?= $new_district['new_districtId']; ?>"><?= $new_district['new_name']; ?></option>
								<?php endforeach; ?>
							</select>
						</div> -->
						<div class="col-sm-6 mt-3">
							Стать:
							<div class="form-check form-check-inline">
								<input type="radio" class="form-check-input" id="modal_сustomers_form_email_gender_1" name="modal_сustomers_form_email_gender" value="1" checked>
								<label class="form-check-label" for="modal_сustomers_form_email_gender_1">Ж</label>
							</div>
							<div class="form-check form-check-inline">
								<input type="radio" class="form-check-input" id="modal_сustomers_form_email_gender_2" name="modal_сustomers_form_email_gender" value="2">
								<label class="form-check-label" for="modal_сustomers_form_email_gender_2">Ч</label>
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="modal_сustomers_form" class="btn color_button_black btn-rounded"><H3>Додати</H3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
