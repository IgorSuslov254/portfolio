<main>
	<div id="first_div_main">
		<?php if(isset($breadcrumb)){echo $breadcrumb;} ?>
		<?=$this->table->generate();?>
	</div>
</main>

<!-- Constant for count bell -->
<script>
	var set_admin_count = <?= $set_admin_count ?>;
</script>