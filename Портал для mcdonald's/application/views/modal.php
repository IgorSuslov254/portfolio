<div class="modal fade" id="modal_recover_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-notify" role="document">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true" class="white-text float-right">&times;</span>
			</button>
			<h2 class="font-weight-bold">Відновлення пароля</h2>
			<h4>Для того, щоб відновити свій пароль - вкажіть Ваш логін</h4>
			<form id="modal_recover_password_form">
				<label for="recover_login" class="float-left"><h3>Введіть логін</h3></label>
				<input type="text" id="recover_login" name="recover_login" class="form-control" required>
			</form>
			<div class="container-fluid">
				<div class="row">
					<div class="col-sm-6"><button type="submit" form="modal_recover_password_form" class="btn color_button_black btn-rounded"><h3>Відновити</h3></button></div>
					<div class="col-sm-6"><button type="button" data-dismiss="modal" class="btn btn-rounded"><h3>Ні, дякую</h3></button></div>
				</div>
			</div>
		</div>
	</div>
</div>
