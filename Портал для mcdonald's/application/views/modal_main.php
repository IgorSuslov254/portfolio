<div class="modal fade right" id="modal_main" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm modal-side modal-top-right" role="document">
		<div class="modal-content">
			<div class="header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="float-right white-text" aria-hidden="true">&times;</span>
				</button>
				<h2 class="font-weight-bold"><?= $this->session->User_FullName; ?></h2>
				<hr>
			</div>
			<div class="menu">
				<div id="appeals_menu_modal"><h2>Звернення <img src="img/modal_main/right.svg"></h2></div>
				<div class="appeals_modal">
					<h3 id="new"><a href="./Portal?view_appealse=new" style="color:white;">Нові</a></h3>
					<h3 id="inWork"><a href="./Portal?view_appealse=inWork" style="color:white;">В роботі</a></h3>
					<h3 id="expired"><a href="./Portal?view_appealse=expired" style="color:white;">Прострочені</a></h3>
					<h3 id="rework"><a href="./Portal?view_appealse=rework" style="color:white;">На доопрацювання</a></h3>
					<h3 id="closeOper"><a href="./Portal?view_appealse=closeOper" style="color:white;">Закриті</a></h3>
					<h3 id="helpPR"><a href="./Portal?view_appealse=helpPR" style="color:white;">Потрібна допомога PR</a></h3>
					<h3 id="helpLawyer"><a href="./Portal?view_appealse=helpLawyer" style="color:white;">Потрібна допомога юриста</a></h3>
					<h3 id="all"><a href="./Portal?view_appealse=all" style="color:white;">Всi</a></h3>
				</div>
				<?php if($this->session->Role_Name == 'Офіс' || $this->session->Role_Name == 'Ресторан'):?>
					<div id="payment_head"><h2>Компенсації <img src="img/modal_main/right.svg"></h2></div>
				<?php endif; ?>
				<?php if($this->session->Role_Name == 'Офіс' || $this->session->Role_Name == 'Модератор' || $this->session->Role_Name == 'Ресторан'):?>
					<div id="customers"><h2>Клієнти <img src="img/modal_main/right.svg"></h2></div>
				<?php endif; ?>
				<?php if($this->session->Role_Name == 'Офіс'):?>
					<div id="phone_calls"><h2>Дзвінки <img src="img/modal_main/right.svg"></h2></div>
				<?php endif; ?>
				<div id="knowledge_base"><h2>База знань <img src="img/modal_main/right.svg"></h2></div>
				<!-- <div><h2>Отчёты <img src="img/modal_main/right.svg"></h2></div> -->
				<div id="support_service"><h2>Служба підтримки <img src="img/modal_main/right.svg"></h2></div>
			</div>
			<div class="button">
				<h3><a href="Portal/exit">Вийти</a></h3>
			</div>
		</div>
	</div>
</div>
