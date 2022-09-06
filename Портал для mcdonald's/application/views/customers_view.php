<style>
	#DataTables_Table_2,
	#DataTables_Table_2 tr > th:nth-child(5){
		width: 100% !important;
	}
	#DataTables_Table_2 tr > td:nth-child(7),
	#DataTables_Table_2 tr > td:nth-child(8){
		width: 150px;
	}
	#DataTables_Table_2 tr > td:nth-child(3){
		width: 70px;
	}
	#DataTables_Table_2 tr > th{
		white-space: nowrap;
	}
</style>

<div class="container mt-4">
	<div class="row">
		<div class="col-12">
			<form id="customers_form" class="form-inline d-flex justify-content-center form-sm active-cyan-2 mt-2">
				<input name="name" class="form-control form-control-sm mr-3 w-75" type="text" placeholder="Введіть телефон або Email" aria-label="Search" required>
				<button type="submit"><i class="fa fa-search cyan-text" aria-hidden="true"></i></button>
			</form>
		</div>
		<?php if($this->session->Role_Name == 'Модератор'):?>
			<div class="col-12 text-center mt-4">
				<button id="create_customers" type="button" class="btn color_button_black btn-rounded text-white">Створити клієнта</button>
			</div>
		<?php endif; ?>
		<div id="table_customers_clients" class="mt-4" style="overflow-x: hidden;"></div>
		<div id="more_information_customer" class="mt-4" style="overflow-x: hidden;"></div>
	</div>
</div>