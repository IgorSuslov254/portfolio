<div class="modal fade" id="modal_add_appeals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify modal-lg modal-info" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Додати звернення</h2>
			<hr>
			<form id="modal_add_appeals_form" enctype="multipart/form-data">
				<div class="container">
					<div class="row">
						<div class="col-sm-6">
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_channel"><h3>Канал надходження</h3></label>
								<select id="modal_add_appeals_form_channel" name="modal_add_appeals_form_channel" class="browser-default custom-select" required>
									<option value="100000002">Facebook</option>
									<option value="100000003">Instagram</option>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_contact"><h3>Клієнт</h3></label>
								<input type="text" id="modal_add_appeals_form_contact" class="form-control" name="modal_add_appeals_form_contact"disabled>
								<input type="hidden" id="modal_add_appeals_form_contact_hide" name="modal_add_appeals_form_contact_hide">
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_new_requesttype"><h3>Тип звернення</h3></label>
								<select id="modal_add_appeals_form_new_requesttype" name="modal_add_appeals_form_new_requesttype" class="browser-default custom-select" required>
									<?php foreach($getInfoForComboboxAddAppeal['new_requesttype'] as $key => $new_requesttype):?>
										<option value="<?= $new_requesttype['new_theme1Id']; ?>"><?= $new_requesttype['new_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_new_requestpart"><h3>Розділ звернення</h3></label>
								<select id="modal_add_appeals_form_new_requestpart" name="modal_add_appeals_form_new_requestpart" class="browser-default custom-select">
									<?php foreach($getTheme2 as $key => $getTheme):?>
										<option value="<?= $getTheme['new_theme2Id']; ?>"><?= $getTheme['new_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_new_requesttheme"><h3>Тема звернення</h3></label>
								<select id="modal_add_appeals_form_new_requesttheme" name="modal_add_appeals_form_new_requesttheme" class="browser-default custom-select">
									<?php foreach($getTheme3 as $key => $getTheme):?>
										<option value="<?= $getTheme['new_theme3Id']; ?>"><?= $getTheme['new_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="mb-3">
								<!-- <label class="float-left mt-3" for="modal_add_appeals_form_new_restaurant"><h3>Ресторан</h3></label>
								<select id="modal_add_appeals_form_new_restaurant" name="modal_add_appeals_form_new_restaurant" class="browser-default custom-select" required>
									<?php foreach($getInfoForComboboxAddAppeal['new_restaurant'] as $key => $new_restaurant):?>
										<option value="<?= $new_restaurant['AccountId']; ?>"><?= $new_restaurant['name']; ?></option>
									<?php endforeach; ?>
								</select>
								<label id="date_start_label" for="modal_add_appeals_form_date" class="float-left mt-0 mt-md-3"><h3>Дата и час інциденту</h3></label> -->
								<div class="mb-3">
									<label class="float-left mt-3" for="modal_add_appeals_form_new_restaurant"><h3>Ресторан</h3></label>
									<input type="text" id="modal_add_appeals_form_new_restaurant" class="form-control" name="modal_add_appeals_form_new_restaurant" placeholder="Почнiть вводити" required autocomplete="off">
									<input type="hidden" id="modal_add_appeals_form_new_restaurant_value" name="modal_add_appeals_form_new_restaurant_value" value=".">
									<div id="modal_add_appeals_form_new_restaurant_hide"></div>
								</div>
							</div>
							<div>
								<input type="datetime-local" id="modal_add_appeals_form_date" class="form-control" name="modal_add_appeals_form_date" value="2000-07-01T12:00" required>
							</div>
							<div>
								<div class="file-field mt-3">
									<div class="btn btn-rounded color_button_black btn-sm float-left">
										<span>Виберіть файл</span>
										<input type="file" id="modal_add_appeals_form_file" name="modal_add_appeals_form_file">
									</div>
									<div class="file-path-wrapper">
										<input class="file-path validate" type="text">
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_chek"><h3>Наявність чека</h3></label>
								<select id="modal_add_appeals_form_chek" name="modal_add_appeals_form_chek" class="browser-default custom-select" required>
									<option value="1">Так</option>
									<option value="0">Ні</option>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_number"><h3>Номер чека / Номер замовлення</h3></label>
								<input type="number" id="modal_add_appeals_form_number" class="form-control" name="modal_add_appeals_form_number">
							</div>
							<div>
								<label class="float-left mt-3" for="answer_appeals_form_description"><h3>Суть звернення</h3></label>
								<textarea id="answer_appeals_form_description" class="md-textarea form-control" name="answer_appeals_form_description" required></textarea>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_new_food"><h3>Страва</h3></label>
								<select id="modal_add_appeals_form_new_food" name="modal_add_appeals_form_new_food" class="browser-default custom-select" required disabled>
									<option value=""></option>
									<?php foreach($getInfoForComboboxAddAppeal['new_food'] as $key => $new_food):?>
										<option value="<?= $new_food['new_foodId']; ?>"><?= $new_food['new_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_inclusion">Тип стороннього включення</label>
								<select id="modal_add_appeals_form_inclusion" name="modal_add_appeals_form_inclusion" class="browser-default custom-select" required disabled>
									<option value=""></option>
									<option value="100000000">Скло</option>
									<option value="100000001">Пластик</option>
									<option value="100000002">Метал</option>
									<option value="100000003">Папір, картон</option>
									<option value="100000004">Деревина</option>
									<option value="100000005">Тканина (ворс, нитки)</option>
									<option value="100000006">Волосся, нігті</option>
									<option value="100000007">Органічні включення (кістки, очистки, стебла)</option>
									<option value="100000008">Комахи</option>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_add_appeals_form_new_employee"><h3>ПІБ, посада</h3></label>
								<input type="text" id="modal_add_appeals_form_new_employee" class="form-control" name="modal_add_appeals_form_new_employee" required disabled>
							</div>
							<div>
								<label class="float-left mt-3" for="answer_appeals_form_new_object"><h3>Що було не прибрано/брудне?</h3></label>
								<textarea id="answer_appeals_form_new_object" class="md-textarea form-control" name="answer_appeals_form_new_object" required disabled></textarea>
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="modal_add_appeals_form" class="btn color_button_black btn-rounded"><H3>Додати</H3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
