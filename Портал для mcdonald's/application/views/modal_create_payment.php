<div class="modal fade" id="modal_create_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify modal-fluid modal-info" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Створити компенсацію</h2>
			<hr>
			<form id="modal_create_payment_form" enctype="multipart/form-data">
				<div class="container">
					<div class="row">
						<div class="col-sm-4">
							<div>
								<label class="float-left mt-3" for="modal_create_payment_form_name"><h3>Код</h3></label>
								<input type="text" id="modal_create_payment_form_name" class="form-control" disabled>
								<input type="hidden" id="modal_create_payment_form_name_hidden" class="form-control" value="<?= $_SESSION['User_FullName']; ?>">
								<input type="hidden" id="modal_create_payment_form_name_value" name="modal_create_payment_form_name_value">
								<input type="hidden" id="modal_create_payment_form_id_appeal" name="modal_create_payment_form_id_appeal">
							</div>
							<div>
								<label class="float-left mt-3" for="modal_create_payment_form_enddate"><h3>Дата закінчення строку дії</h3></label>
								<input type="datetime-local" id="modal_create_payment_form_enddate" class="form-control" name="modal_create_payment_form_enddate" value="2000-07-01T12:00" required>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_create_payment_form_status"><h3>Статус</h3></label>
								<select id="modal_create_payment_form_status" name="modal_create_payment_form_status" class="browser-default custom-select" required>
									<option value="100000000">Активна</option>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_create_payment_form_createdby"><h3>Ресторан, який створив Компенсацію</h3></label>
								<input type="text" id="modal_create_payment_form_createdby" class="form-control" value="<?= $_SESSION['User_FullName'] ?>" disabled>
								<input type="hidden" id="modal_create_payment_form_createdby_value" name="modal_create_payment_form_createdby_value" value="<?= $_SESSION['SystemUserId'] ?>">
							</div>
							<div class="modal_create_payment_form_food_blok">
								<label class="float-left mt-3" for="modal_create_payment_form_food_1"><h3>Страва 1</h3></label>
								<input type="text" id="modal_create_payment_form_food_1" class="form-control" placeholder="Почнiть вводити" autocomplete="off" required>
								<input type="hidden" id="modal_create_payment_form_food_value_1" name="modal_create_payment_form_food_value_1">
								<div class="modal_create_payment_form_food_hide"></div>
							</div>
							<div class="modal_create_payment_form_food_blok">
								<label class="float-left mt-3" for="modal_create_payment_form_food_2"><h3>Страва 2</h3></label>
								<input type="text" id="modal_create_payment_form_food_2" class="form-control" placeholder="Почнiть вводити" autocomplete="off">
								<input type="hidden" id="modal_create_payment_form_food_value_2" name="modal_create_payment_form_food_value_2">
								<div class="modal_create_payment_form_food_hide"></div>
							</div>
							<div class="modal_create_payment_form_food_blok">
								<label class="float-left mt-3" for="modal_create_payment_form_food_3"><h3>Страва 3</h3></label>
								<input type="text" id="modal_create_payment_form_food_3" class="form-control" placeholder="Почнiть вводити" autocomplete="off">
								<input type="hidden" id="modal_create_payment_form_food_value_3" name="modal_create_payment_form_food_value_3">
								<div class="modal_create_payment_form_food_hide"></div>
							</div>
							<div>
								<h3 class="float-left mt-3 modal_create_payment_form_add_food">Додати страву +</h3>
							</div>
						</div>
						<div class="col-sm-4 second_block_modal_create_payment_form">
							
						</div>
						<div class="col-sm-4 third_block_modal_create_payment_form">
							
						</div>
					</div>
				</div>
			</form>
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="modal_create_payment_form" class="btn color_button_black btn-rounded"><H3>Створити</H3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
