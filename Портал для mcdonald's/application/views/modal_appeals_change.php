<div class="modal fade" id="modal_change_appeals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify modal-lg modal-info" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Змінити дані звернення</h2>
			<hr>
			<form id="cheng_appeals_form">
				<div class="container">
					<div class="row">
						<div class="col-sm-6">
							<div>
								<label class="float-left mt-3" for="modal_input_feedback">Чи задоволений клієнт зворотнім зв'язком</label>
								<select id="modal_input_feedback" name="modal_input_feedback" class="browser-default custom-select" required>
									<option value="1">Так</option>
									<option value="0">Ні</option>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_input_comments">Коментарі клієнта по зворотному зв'язку</label>
								<textarea class="form-control rounded-0" id="modal_input_comments" name="modal_input_comments"></textarea>
							</div>
						</div>
						<div class="col-sm-6">
							<div>
								<label class="float-left mt-3" for="modal_input_help_PR">Допомога PR-відділу</label>
								<select id="modal_input_help_PR" name="modal_input_help_PR" class="browser-default custom-select">
									<option value=""></option>
									<option value="100000001">PR-відділ</option>
									<?php if($_SESSION['Role_Name'] == 'Офіс'):?>
										<option value="100000002">Юрист</option>
									<?php endif; ?>
								</select>
							</div>
							<div>
								<label class="float-left mt-3" for="modal_input_notes">Коментарі ресторану</label>
								<textarea class="form-control rounded-0" id="modal_input_notes" name="modal_input_notes"></textarea>
							</div>
							<div>
								<input type="hidden" id="number_appeals" name="number_appeals">
							</div>
						</div>
						<div class="col-12">
							<div class="file-field mt-3 text-center">
								<div class="btn btn-rounded color_button_black btn-sm float-left">
									<span>Виберіть файл</span>
									<input type="file" id="modal_add_appeals_form_file_change" name="modal_add_appeals_form_file_change">
								</div>
								<div class="file-path-wrapper">
									<input class="file-path validate" type="text">
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="cheng_appeals_form" class="btn color_button_black btn-rounded"><H3>Змінити</H3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
