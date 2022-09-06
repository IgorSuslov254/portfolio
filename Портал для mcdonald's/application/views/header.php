<style>
	#appeals_menu_header .<?=$this->session->view_appealse?>{
		font-weight: bold;
		font-size: 20px !important;
	}
</style>

<header>
	<nav>
		<div><a href="Portal"><img src="img/logo/logo.svg"></a></div>
		<div id="appeals_menu_header">
			<h2 class="new"><a href="<?= base_url('Portal?view_appealse=new') ?>" style="color: #727070;">Нові</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['new'])){echo $appeals_count['new'];}else{echo "0";} ?></span>
			<h2 class="inWork"><a href="<?= base_url('Portal?view_appealse=inWork') ?>" style="color: #727070;">В роботі</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['inWork'])){echo $appeals_count['inWork'];}else{echo "0";}?></span>
			<h2 class="expired"><a href="<?= base_url('Portal?view_appealse=expired') ?>" style="color: #727070;">Прострочені</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['expired'])){echo $appeals_count['expired'];}else{echo "0";}?></span>
			<h2 class="closeOper"><a href="<?= base_url('Portal?view_appealse=closeOper') ?>" style="color: #727070;">Закриті</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['closeOper'])){echo $appeals_count['closeOper'];}else{echo "0";}?></span>
			<h2 class="rework"><a href="<?= base_url('Portal?view_appealse=rework') ?>" style="color: #727070;">На доопрацювання</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['rework'])){echo $appeals_count['rework'];}else{echo "0";}?></span>
			<h2 class="all"><a href="<?= base_url('Portal?view_appealse=all') ?>" style="color: #727070;">Всі</a></h2><span class="badge rounded-pill badge-notification bg-warning"><?php if(isset($appeals_count['all'])){echo $appeals_count['all'];}else{echo "0";}?></span>
			<?php if($this->session->Role_Name == 'Офіс' || $this->session->Role_Name == 'Ресторан'):?>
				<img src="<?= base_url(); ?>/img/sandwich.png" class="payment_img"><h2 class="payment_head">Компенсації</h2>
			<?php endif; ?>
		</div>
		<div>
			<h2><?= $this->session->User_FullName; ?></h2>
			<h2 class="activ_modal_menu"><i class="fa fa-bars"></i></h2>
		</div>
	</nav>
</header>
