<?php
	//test pull
	$sar_total_date = array(
		'SAR_period_start' => $SAR_period_start,
		'SAR_titles' => $SAR_titles,
		'SAR_showroom_traffic' => $SAR_showroom_traffic,
		'SAR_showroom_traffic_total' => $SAR_showroom_traffic_total,
		'SAR_leads' => $SAR_leads,
		'SAR_leads_total' => $SAR_leads_total,
		'SAR_offer' => $SAR_offer,
		'SAR_offer_total' => $SAR_offer_total,
		'SAR_testdrive' => $SAR_testdrive,
		'SAR_testdrive_total' => $SAR_testdrive_total,
		'SAR_total' => $SAR_total,
		'SAR_analysis' => $SAR_analysis,
		'SAR_sale' => $SAR_sale,
		'SAR_sale_total' => $SAR_sale_total
	);

	$SAR_period_ = explode('-', $SAR_period_start);
	$m = date("m", strtotime("+1 month") ) - $SAR_period_[1];
	if( ( $SAR_period_[0] == date('Y') && $SAR_period_[1] == date('m') ) || ( $SAR_period_[0] == date('Y') && $m == 1 && date('d') < 8 ) ){
		$change_data = true;
	} else {
		$change_data = false;
	}


	$mr_indexs = ['prev_prev', 'prev', 'current'];
	foreach ($mr_indexs as $mr_index) {
		$label_sar_mr[] = $SAR_MR_index[$mr_index];
		$SAR_MR_total_SHR[] = $SAR_MR_total['SHR'][$mr_index];
		$SAR_MR_total_CR[] = round($SAR_MR_total['CR'][$mr_index]*100);
		$SAR_MR_total_CC[] = $SAR_MR_total['CC'][$mr_index];
	}

	$array_date = explode("-", $SAR_period_start);


	include "sar_view_css.php";
	
?>

<div id="version" style="display:none;">SAR_V3</div>
<!-- loading -->
<div class="loading_SAR"><img src="/assets/img/loading.gif" alt=""></div>

<script src="/assets/jquery.top_scrollbar.js"></script>

<form action="<?=base_url(SAR_URL.'/excel_models_report')?>" method="POST" id="excel_models_report"></form>

<div class="content-wrapper">
	<!-- <h1>Технічні роботи</h1> -->
	<?php if (!empty($error) && $error == true):?>
		<p><?=$error_description?></p>
	<?php else: ?>
		<section class="content">
			<form method="post" action="<?=base_url(SAR_URL)?>" >
				<label for="period_start" class="control-label">Фільтри</label>
				<div class="inputs">
					<div class="input-itm m-r-btn">
						<div class="input-group f-r-str-s">
							<div class="input-group-addon btn-box-tool f-r-c-c icon-bg" data-toggle="tooltip" title="" data-original-title="Вы можете посметреть данные за любой период:">
								<i class="fa fa-calendar"></i>
							</div>
							<?php echo form_input(array(
								'name'      => 'period_start',
								'id'        => 'period_start',
								'type'      => 'input',
								'autocomplete'=> 'off',
								'class'     => 'form-control w-input-date',
								'value'		=> $SAR_period_start,
								'required'	=> 'required'
							)); ?>
						</div>
					</div>
					<?php if(!empty($SAR_dealers)): ?>
						<div class="input-itm w-input-g-1 m-r-btn">
							<div class="input-group f-r-str-s">
								<div class="input-group-addon btn-box-tool f-r-c-c icon-bg" data-toggle="tooltip" title="" data-original-title="Вибір дилера">
									<i class="fa fa-user"></i>
								</div>
								<select class="mymsel_2 select2-hidden-accessible" name="selected_dealer[]" multiple="" tabindex="-1" aria-hidden="true">
									<?php foreach ($SAR_dealers as $dealers):?>
										<?php if($dealers['AccountId'] == 'a6ba10c3-bb1c-ea11-81b3-00155d1f050b'){continue;}?>
										<option value="<?=$dealers['AccountId']?>"><?=$dealers['Name']?></option>
									<?php endforeach; ?>
								</select>
								<button type="submit" class="btn btn-primary" style="margin-left: 25px;">Застосувати фільтри</button>
							</div>
						</div>
					<?php else: ?>
						<button type="submit" class="btn btn-primary" style="margin-left: 25px;">Застосувати фільтри</button>
						<button type="submit" class="btn btn-primary" style="margin-right: 27px;margin-left: 27px;" form="sar_form">Зберегти зміни</button>
					<?php endif; ?>


					<a class="btn btn-primary" href="<?=base_url(SAR_URL.'/excel').'?SAR_period_start='.$sar_total_date['SAR_period_start']?>" 
					style="margin-right:27px;padding-top:10px;" id="export_btn"  data-toggle="tooltip" title="" data-original-title=" Експорт в Excel" target="_blank"><i class="fa fa-download"></i></a>
					<!--
					<button type="submit" style="margin-right: 27px;" form="sar_excel" id="export_btn" class="btn btn-primary" data-toggle="tooltip" title="" data-original-title=" Експорт в Excel"><i class="fa fa-download"></i></button>
					-->
					
					<a class="btn btn-primary" href="<?=base_url(SAR_URL.'/excel_models_report')?>" data-toggle="tooltip" title="Models Report експорт в Excel" target="_blank">Models Report</a>
					<!--
					<button type="submit" class="btn btn-primary" form="excel_models_report" data-toggle="tooltip" title="Models Report експорт в Excel">Models Report</button>
					-->
					
					<?php if(empty($SAR_dealers)): ?>
						<a class="help_icon_btn class-for-hover-info" href="https://sites.google.com/800.com.ua/stellantis-portal/sar" target="_blank"><i class="fa fa-question"></i><p>Інструкція</p></a>
					<?php endif; ?>
				</div>
			</form>
		</section>
		<section id="SAR_">
			<?php if(!empty($SAR_dealers)): ?>
				<div><div class="chart_preloader"></div><canvas id="myChart2"></canvas></div>
				<div><div class="chart_preloader"></div><canvas id="myChart3"></canvas></div>
			<?php endif; ?>
			<form action="<?=base_url(SAR_URL.'/update')?>" method="POST" id="sar_form">
				<?php foreach ($SAR_index as $index):?>
					<?php if (!empty(${$index['value']})): ?>

						<?php if(($index['updatable'] === false) || count($SAR_selected_dealer) > 1 || $SAR_selected_dealer[0] == 'XXX' || !empty($SAR_dealers) /*|| $change_data === false */): ?>
							<p class="title_sar"><?=$index['name']?></p>
						<?php else: ?>
							<p class="title_sar" style="background: #26761f;"><?=$index['name']?></p>
						<?php endif; ?>


						<div style="overflow-x: auto;">
							<table class="sar_traffic <?=$index['value']?>">
								<tr>
									<?php foreach ($SAR_titles as $titles):?>
										<th><?=$titles?></th>
									<?php endforeach; ?>
								</tr>
								<?php $j=0; foreach (${$index['value']} as $showroom_traffic):?>
									<tr>
										<?php for ($i=0; $i<=32; $i++):?>
											<?php if (($i==0 || $i==32) || ($index['updatable'] === false) || count($SAR_selected_dealer) > 1 || $SAR_selected_dealer[0] == 'XXX' || !empty($SAR_dealers)/* || $change_data === false */ || $i > $array_date[2] ):?>
												<?php if ($i==0): ?>
													<?php $style = 'width: 150px;text-align: left;'; ?>
													<?php if ($showroom_traffic[$i]['fromCarsList'] === false):?>
														<?php $style .= 'background: #f88;'; ?>
													<?php endif; ?>
													<input type="hidden" name="<?=$index['value']?>_car_id_<?=$j?>" value="<?=$showroom_traffic[$i]['car_id']?>">
													<input type="hidden" name="<?=$index['value']?>_id_<?=$j?>" value="<?=$showroom_traffic[$i]['id']?>">
													<input type="hidden" name="<?=$index['value']?>_count" value="<?=count(${$index['value']})?>">
												<?php else: ?>
													<?php $style = 'background: #ecf0f5;'; ?>
												<?php endif; ?>
												<td<?php if ($i==0 || $i==32) echo ' class="total_col" ';?> style="<?=$style?>"><?=$showroom_traffic[$i]['value']?></td>
											<?php else: ?>
												<input type="hidden" name="<?=$index['value']?>_id_details_<?=$j.'_'.$i?>" value="<?=$showroom_traffic[$i]['id']?>">
												<input type="hidden" name="<?=$index['value']?>_old_value_details_<?=$j.'_'.$i?>" value="<?=$showroom_traffic[$i]['value']?>">
												<?php
													$index['value'] == 'SAR_leads' ? $sar_min = $showroom_traffic[$i]['value'] : $sar_min = 0;
												?>
												<td><input type="number" min="0" name="<?=$index['value']?>_value_details_<?=$j.'_'.$i?>" value="<?=$showroom_traffic[$i]['value']?>"></td>
											<?php endif; ?>
										<?php endfor; ?>
									</tr>
								<?php $j++; endforeach; ?>
								<tr class="total_row">
									<?php foreach (${$index['value'].'_total'} as $key => $total):?>
										<td <? if ($key==32) echo'class="total_all"'; ?> ><?=$total?></td>
									<?php endforeach; ?>
								</tr>
							</table>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</form>
			<table class="sar_total">
				<?php foreach ($SAR_total as $total):?>
					<?php $x = 1; ?>
					<tr>
						<?php foreach ($total as $total_value): ?>
							<?php $x == 2 || $x == 3 || $x == 5 ? $stely = 'text-align: center' : $stely = ''; ?>
							<td style="<?=$stely?>"><?=$total_value?></td>
						<?php $x++; endforeach; ?>
					</tr>
				<?php endforeach; ?>
			</table>
			<div><canvas id="myChart"></canvas></div>
			<div>
				<table class="sar_analysis">
					<?php foreach ($SAR_analysis as $key => $analysis):?>
						<tr>
							<?php foreach ($analysis as $value):?>
								<?php if($key == 0): ?>
									<th><?=$value?></th>
								<?php else: ?>
									<td><?=$value?></td>
								<?php endif; ?>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		</section>
	<?php endif; ?>
</div>

<form action="<?=base_url(SAR_URL.'/excel')?>" method="POST" id="sar_excel">
	<textarea name="sar_excel" style="display: none;"><?=json_encode($sar_total_date)?></textarea>
</form>

<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Контракти</h4>
      </div>
      <div class="modal-body">

          <table id="contracts_table" width="100%" class="table table-bordered table-striped">
              <thead>
              <tr>
                  <th>ID</th>
                  <th>Дилер</th>
                  <!--<th>Дата створення</th>--> <!-- 2 Дата створення -->
                  <th>Дата контракту</th> <!-- 3 -->
                  <th>Клієнт (ПІБ) </th>
                  <th>Мобільний телефон</th>
                  <th>Склад / виробництво</th>
                  <th>Номер кузова</th>
                  <th>Номер замовлення у виробництво</th>
                  <th>Модель SAR</th> <!-- 9 -->
                  <th>Модель</th>
                  <th>Тип оплати</th>
                  <th>Статус контракту</th>
                  <th>Дата продажи</th> <!-- 2 Дата створення -->
                  <th>Продавець</th>

                  <th>Дата гарантійного листа</th><!-- 14 -->
                  <th>Дата угоди в банку</th><!-- 15 -->

                  <th>Митне очищення</th>
                  <th> Місцезнаходження авто</th>
                  <th>Дата прибуття до дилера</th><!-- 18 -->
                  <th>100% буде продано в поточному місяці</th>
                  <th>Запланована дата продажу в наступних місяцях</th>
                  <!--<th>Продано в поточному місяці</th>-->
                  <th>Коментарі</th>
                  <th>Конкуренти, з якими клієнт порівнював</th>
                  <th>Що було визначальним у виборі</th>

                  <th>Видалити</th> <!-- 25 -->

              </tr>
              </thead>
              <tbody>

              </tbody>
          </table>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрити</button>
        <!--<button type="button" class="btn btn-primary">Save changes</button>-->
      </div>
    </div><!-- /.модал-контент -->
  </div><!-- /.модальное окно -->
</div><!-- /.модальные -->


<?php	
	include "sar_view_js.php";
?>
