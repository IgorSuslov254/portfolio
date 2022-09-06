<div class="modal fade" id="modal_customer_appeals" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-fluid" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h1 class="modal-title w-100" id="myModalLabel">Звернення клієнта</h1>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<?php 
					$template = array('table_open' => '<table id="table_customer_appeals" class="display" style="width:100%">');
					$this->table->set_template($template);
					$this->table->set_heading(HEDING_TABEL);
					// echo $this->table->generate();
				?>
				<script>
					const generate_table = `<?= $this->table->generate() ?>`;
				</script>
			</div>
		</div>
	</div>
</div>