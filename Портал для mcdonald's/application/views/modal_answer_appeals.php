<div class="modal fade" id="modal_answer_appeals" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify modal-lg modal-info" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Відповіді на звернення</h2>
			<hr>
			<form id="answer_appeals_form">
				<div class="container">
					<div class="row">
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="answer_appeals_form_ownerid">Відповідальний</label>
							<input type="text" id="answer_appeals_form_ownerid" class="form-control" name="answer_appeals_form_ownerid" value="<?= $this->session->User_FullName; ?>" disabled>
						</div>
						<div class="col-sm-6 mt-3">
							<label class="float-left" for="answer_appeals_form_createdon">Дата/час створення</label>
							<input type="text" id="answer_appeals_form_createdon" class="form-control" name="answer_appeals_form_createdon" value="<?php date_default_timezone_set('Europe/Kiev'); echo date('Y-m-d H:i:s'); ?>" disabled>
						</div>
						<div class="col-12 mt-3">
							<label class="float-left" for="answer_appeals_form_incident">Звернення</label>
							<input type="text" id="answer_appeals_form_incident" class="form-control" name="answer_appeals_form_incident" value="." disabled>
							<input type="hidden" id="id_app" name="id_app">
						</div>
						<div class="col-12 mt-3">
							<label class="float-left" for="answer_appeals_form_feedbacktext">Текст відповіді</label>
							<textarea id="answer_appeals_form_feedbacktext" class="md-textarea form-control" name="answer_appeals_form_feedbacktext" required></textarea>
						</div>
						<div class="col-12 mt-3">
							<label class="float-left" for="answer_appeals_form_new_result"><h3>Результат перевірки</h3></label>
							<select id="answer_appeals_form_new_result" name="answer_appeals_form_new_result" class="browser-default custom-select" required>
								<option value="100000000">Скарга підтверджена</option>
								<option value="100000001">Скарга НЕ підтверджена</option>
							</select>
						</div>
						<div class="col-12 mt-3">
							<label class="float-left" for="answer_appeals_form_feedbacktextcorrect">Текст відповіді з доповненням/корекцією</label>
							<textarea id="answer_appeals_form_feedbacktextcorrect" class="md-textarea form-control" name="answer_appeals_form_feedbacktextcorrect"></textarea>
						</div>
					</div>
				</div>
			</form>
			<div class="container-fluid mt-3">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="answer_appeals_form" class="btn color_button_black btn-rounded"><H3>Додати</H3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
