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
?>

<style>
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		-webkit-appearance: none;
	}
	input[type='number'] {
		-moz-appearance: textfield;
	}


	.title_sar{
		background: #223464;
		color: white;
		padding-left: 20px;
		margin: 0px;
	}
	.title_sar:not(.title_sar:first-child){
		margin-top: 30px;
	}


	#SAR_{
		padding: 0px 15px;
	}

		#SAR_ table.SAR_testdrive td, #SAR_ table.SAR_offer td{
			cursor: pointer;
		}

	.help_icon_btn{
		width: 35px !important;
		height: 35px !important;
		margin-left: 25px !important;
	}

	#SAR_ table{
		border-collapse: inherit !important;
	}
	#SAR_ table tbody > tr:nth-of-type(2n + 1) {
		background-color: #ffffff !important;
	}
	#SAR_ table tbody tr:nth-of-type(2n) {
		background: #ffffff !important;
	}
	#SAR_ table.sar_traffic{
		width: 100%;
		overflow-x: auto;
		font-size: 14px;
	}
	#SAR_ table.sar_traffic input{
		width: 100%;
		height: 100%;
		border: none;
		text-align: center;
		min-width: 20px;
	}
	#SAR_ table.sar_traffic th, #SAR_ table.sar_traffic td{
		background: white;
		border: 1px solid #223464;
		/* padding: 2px 5px; */
		text-align: center;
		min-width: 30px;
	}
	#SAR_ table.sar_traffic th:first-child, #SAR_ table.sar_traffic td:first-child{
		text-align: left;
	}
	#SAR_ table.sar_traffic th:last-child, #SAR_ table.sar_traffic td:last-child{
		font-weight: bold;
		background: #d5d5d5;
	}
	#SAR_ table.sar_traffic th{
		color: #223464;
	}
	#SAR_ table.sar_traffic tr:last-child td{
		font-weight: bold;
		background: #d5d5d5;
	}


	.content{
		min-height: 0px;
	}


	.sar_total{
		margin: 30px 0px;
		font-size: 14px;
	}
	.sar_total td{
		padding: 2px 5px;
		border: 1px solid black;
		color: white;
	}
	.sar_total td:nth-child(2){
		color: black;
		font-weight: bold;
	}
	.sar_total tr:first-child td:first-child{
		background: #b2b2b2;
	}
	.sar_total tr:nth-child(2) td:first-child{
		background: #f00;
	}
	.sar_total tr td:not(.sar_total tr:nth-child(2) td:first-child, .sar_total tr:nth-child(3) td:first-child, .sar_total tr:nth-child(4) td:first-child, .sar_total tr:nth-child(1) td:nth-child(2), .sar_total tr:nth-child(2) td:nth-child(2), .sar_total tr:nth-child(3) td:nth-child(2), .sar_total tr:nth-child(4) td:nth-child(2), .sar_total tr:last-child td:nth-child(2)){
		background: #223464;
	}
	.sar_total tr:nth-child(3) td:first-child{
		background: rgb(26 35 126);
	}
	.sar_total tr:nth-child(4) td:first-child{
		background: rgb(0 112 161);
	}


	.sar_analysis{
		font-size: 14px;
		margin-top: 20px;
		text-align: center;
	}
	.sar_analysis th{
		padding: 5px 10px;
		background: #223464;
		color: white;
		border: 1px solid black;
	}
	.sar_analysis td{
		padding: 2px 5px;
		background: white;
		border: 1px solid black;
	}
	#SAR_ > div{
		width: 45%;
		display: inline-block;
		margin-top: 15px;
		margin-bottom: 15px;
		margin-right: 5%;
		
	}
	#SAR_ > div > canvas{
		max-width: 500px;
		margin: auto;
	}

	@keyframes glowing {
		0% { background-color: #f21212; box-shadow: 0 0 5px #f21212; }
		50% { background-color: #f21212; box-shadow: 0 0 20px #f21212; }
		100% { background-color: #f21212; box-shadow: 0 0 5px #f21212; }
	}
	.btn_blink {
		animation: glowing 1300ms infinite;
	}

	.loading_SAR{
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 1000;
		background: white;
	}
	.loading_SAR img{
		height: 250px;
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;
		margin: auto;
	}


	@media screen and (max-width: 992px){
		#SAR_ > div{
			width: 100%;
			display: block;
		}
	}
</style>

<!-- loading -->
<div class="loading_SAR"><img src="/assets/img/loading.gif" alt=""></div>

<script src="/assets/jquery.top_scrollbar.js"></script>

<form action="<?=base_url('sar/excel_models_report')?>" method="POST" id="excel_models_report"></form>

<div class="content-wrapper">
	<!-- <h1>Технічні роботи</h1> -->
	<?php if (!empty($error) && $error == true):?>
		<p><?=$error_description?></p>
	<?php else: ?>
		<section class="content">
			<form method="post" action="<?=base_url('sar')?>" >
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


					<button type="submit" class="btn btn-primary" style="margin-right: 27px;" form="excel_models_report">Models Report</button>
					<button type="submit" form="sar_excel" id="export_btn" class="btn btn-primary" data-toggle="tooltip" title="" data-original-title=" Експорт в Excel"><i class="fa fa-download"></i></button>

					<?php if(empty($SAR_dealers)): ?>
						<a class="help_icon_btn class-for-hover-info" href="https://sites.google.com/800.com.ua/stellantis-portal/sar" target="_blank"><i class="fa fa-question"></i><p>Інструкція</p></a>
					<?php endif; ?>
				</div>
			</form>
		</section>
		<section id="SAR_">
			<?php if(!empty($SAR_dealers)): ?>
				<div><canvas id="myChart2"></canvas></div>
				<div><canvas id="myChart3"></canvas></div>
			<?php endif; ?>
			<form action="<?=base_url('sar/update')?>" method="POST" id="sar_form">
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
												<td style="<?=$style?>"><?=$showroom_traffic[$i]['value']?></td>
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
								<tr>
									<?php foreach (${$index['value'].'_total'} as $total):?>
										<td><?=$total?></td>
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

<form action="<?=base_url('sar/excel')?>" method="POST" id="sar_excel">
	<textarea name="sar_excel" style="display: none;"><?=json_encode($sar_total_date)?></textarea>
</form>

<div class="modal fade" tabindex="-1" role="dialog" id="myModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
        <h1>hello</h1>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.модал-контент -->
  </div><!-- /.модальное окно -->
</div><!-- /.модальные -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>

<script>
	const labels = [];
	const showroom_traffic_data = <?php unset($SAR_showroom_traffic_total[0], $SAR_showroom_traffic_total[32]); echo json_encode(array_values($SAR_showroom_traffic_total)); ?>;
	const leads_data = <?php unset($SAR_leads_total[0], $SAR_leads_total[32]); echo json_encode(array_values($SAR_leads_total)); ?>;
	const offer = <?php unset($SAR_offer_total[0], $SAR_offer_total[32]); echo json_encode(array_values($SAR_offer_total)); ?>;
	const test_drive = <?php unset($SAR_testdrive_total[0], $SAR_testdrive_total[32]); echo json_encode(array_values($SAR_testdrive_total)); ?>;
	const sale_total = <?php unset($SAR_sale_total[0], $SAR_sale_total[32]); echo json_encode(array_values($SAR_sale_total)); ?>;

	for (var i = 0; i < 31; i++) {
		labels[i] = i + 1;
	}

	const data = {
		labels: labels,
		datasets: [{
			label: 'Lead',
			backgroundColor: 'rgb(255 0 0)',
			borderColor: 'rgb(255 0 0)',
			data: leads_data,
			fill: false
		},{
			label: 'Sale',
			backgroundColor: 'rgb(50 120 80)',
			borderColor: 'rgb(50 120 80)',
			data: sale_total,
			fill: false
		},{
			label: 'Test Drive',
			backgroundColor: 'rgb(26 35 126)',
			borderColor: 'rgb(26 35 126)',
			data: test_drive,
			fill: false
		},{
			label: 'Offer',
			backgroundColor: 'rgb(0 112 161)',
			borderColor: 'rgb(0 112 161)',
			data: offer,
			fill: true
		},{
			label: 'Showroom traffic',
			backgroundColor: 'rgb(178 178 178)',
			borderColor: 'rgb(178 178 178)',
			data: showroom_traffic_data,
			fill: true
		}]
	};
	const config = {
		type: 'line',
		data,
		options: {
			plugins: {
              datalabels: {
                display: false
              }
            }
		}
	};

	var myChart = new Chart(
		document.getElementById('myChart'),
		config
	);
</script>

<script>
	var ctx = document.getElementById('myChart2').getContext('2d');
	var myChart = new Chart(ctx, {
	    type: 'bar',
	    data: {
	        labels: <?php echo json_encode($label_sar_mr); ?>,
	        datasets: [{
	            label: 'CC',
	            data: <?php echo json_encode($SAR_MR_total_CC); ?>,
	            datalabels: {
                   align: function(context) {
			          let value = [context.dataset.data[0], context.dataset.data[1], context.dataset.data[2]];
			          let max = value.sort((a,b)=>b-a)[0];

			          if( context.dataset.data[context.dataIndex]*100/max < '20' ){
			          	return 'end';
			          } else {
			          	return 'start';
			          }
			        },
                   anchor: 'end'
                },
	            backgroundColor: [
	                'rgba(0, 112, 161, 1)',
	                'rgba(0, 112, 161, 1)',
	                'rgba(0, 112, 161, 1)'
	            ],
	            borderColor: [
	                'rgba(0, 112, 161, 1)',
	                'rgba(0, 112, 161, 1)',
	                'rgba(0, 112, 161, 1)'
	            ],
	            borderWidth: 1
	        }]
	    },
	    options: {
	    	title: {
                display: true,
                padding: '15',
                text: 'TTL CC movement'
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero: true
	                }
	            }],
	            xAxes: [{
		            barPercentage: 0.4
		        }]
	        },
            plugins: {
              datalabels: {
                color: function(context) {
		          let value = [context.dataset.data[0], context.dataset.data[1], context.dataset.data[2]];
		          let max = value.sort((a,b)=>b-a)[0];

		          if( context.dataset.data[context.dataIndex]*100/max < '20' ){
		          	return 'black';
		          } else {
		          	return 'white';
		          }
		        },
                font: {
                  weight: 'bold'
                },
                display: true
              }
            }
        }
	});

	var ctx = document.getElementById('myChart3').getContext('2d');
	var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($label_sar_mr); ?>,
            datasets: [{
                type: 'line',
                label: 'Closing ratio',
                yAxisID: 'B',
                data: <?php echo json_encode($SAR_MR_total_CR); ?>,
                datalabels: {
                   align: 'end',
                   anchor: 'end'
                },
                borderColor: 'rgb(216 67 21)',
                backgroundColor: 'rgb(216 67 21)',
                fill: false
            }, {
                type: 'bar',
                label: 'Shr traffic',
                yAxisID: 'A',
                data: <?php echo json_encode($SAR_MR_total_SHR); ?>,
                datalabels: {
                   align: function(context) {
			          let value = [context.dataset.data[0], context.dataset.data[1], context.dataset.data[2]];
			          let max = value.sort((a,b)=>b-a)[0];

			          if( context.dataset.data[context.dataIndex]*100/max < '20' ){
			          	return 'end';
			          } else {
			          	return 'start';
			          }
			        },
                   anchor: 'end'
                },
                backgroundColor: [
                    'rgba(34, 52, 100, 1)',
                    'rgba(34, 52, 100, 1)',
                    'rgba(34, 52, 100, 1)'
                ],
                borderColor: [
                    'rgba(34, 52, 100, 1)',
                    'rgba(34, 52, 100, 1)',
                    'rgba(34, 52, 100, 1)'
                ],
                borderWidth: 1
            }],
        },
        options: {
            title: {
                display: true,
                padding: '15',
                text: 'TTL SHR traffic & Closing ratio movement'
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.datasets[tooltipItem.datasetIndex].label || '';

                        if (label == 'Closing ratio') {
                            label += ':' + tooltipItem.yLabel + '%';
                        } else {
                            label += ':' + tooltipItem.yLabel;
                        }

                        return label;
                    }
                }
            },
            plugins: {
                datalabels: {
                    color: 'white',
                    font: {
                      weight: 'bold'
                    },
                    display: true,
                    color: function(context) {
                        if(context.dataset.backgroundColor == 'rgb(216 67 21)'){
                            return context.dataset.backgroundColor;
                        } else {
                        	let value = [context.dataset.data[0], context.dataset.data[1], context.dataset.data[2]];
					        let max = value.sort((a,b)=>b-a)[0]

					        if( context.dataset.data[context.dataIndex]*100/max < '20' ){
					        	return 'black';
					        } else {
					        	return 'rgb(255, 255, 255)';
					        }
                        }
                    },
                    formatter: function(value, context) {
                    	if(context.dataset.label == 'Closing ratio'){
                    		return context.dataset.data[context.dataIndex] + '%';
                    	} else {
                    		return context.dataset.data[context.dataIndex];
                    	}
			        }
                }
            },
            scales: {
              yAxes: [{
                id: 'A',
                type: 'linear',
                position: 'left',
                ticks: {
                  beginAtZero: true
                }
              }, {
                id: 'B',
                type: 'linear',
                position: 'right',
                ticks: {
                  beginAtZero: true,
                  callback: function(value, index, values) {
                    return value + ' %';
                  }
                }
              }],
              xAxes: [{
		         barPercentage: 0.4
		      }]
            }
        }
    });
</script>

<?php
	if(!empty($SAR_selected_dealer)){
		foreach ($SAR_selected_dealer as $selected_dealer) {
			foreach ($SAR_dealers as $SAR_dealer) {
				if($SAR_dealer['AccountId'] == $selected_dealer){
					$placeholderThis[] = $SAR_dealer['Name'];
				}
			}
		}
	} else {
		$placeholderThis = ['Всі дилери'];
	}
?>
<script>
	function on_page_ready() {
		//SELECT2 Dealer FILTER INIT
		var placeholderThis = <?php echo json_encode(implode(",", $placeholderThis)); ?>;
		$(".mymsel_2").select2({
			width: '0%',
			placeholder: placeholderThis,
			'width': '40%',
		});

		var period_start = $("#period_start").datetimepicker({
			format: "yyyy-mm-dd",
			minView: "month",
			language: 'ru'
		});
	}
</script>

<script>
	$('.SAR_showroom_traffic input[type="number"], .SAR_leads input[type="number"]').click(function(event) {
		// $('.SAR_showroom_traffic tr td, .SAR_leads tr td').not('.SAR_showroom_traffic tr td:last-child').not('.SAR_showroom_traffic tr:last-child td').not('.SAR_leads tr td:last-child').not('.SAR_leads tr:last-child td').css('background', 'none');

		$('.SAR_showroom_traffic input[type="number"], .SAR_leads input[type="number"]').css('background', 'none');
		// $(this).parent('td').parent('tr').find('td').not('td:last-child').css('background', '#9ad0ff');
		$(this).parent('td').parent('tr').find('input[type="number"]').css('background', '#9ad0ff');
	});

	$('.sar_traffic input[type="number"]').change(function(event) {
		$('button[form="sar_form"]').addClass('btn_blink');
	});
</script>

<script>
	$('#SAR_ table.SAR_testdrive td').click(function(event) {
		const elem = $(this);
		elem.css('background', '#9ad0ff');

		setTimeout(function(){
			if (confirm("Заполнить карточку?")) {
				$.ajax({
					type: "POST",
					url: "<?=base_url('sar/requests_add')?>",
					data: "SAR_testdrive_car_id="+elem.parent('tr').find('input:first-child').val()+"&SAR_request_type=1",
					success: function(data){
						window.open('<?=base_url('requests/add')?>');
						$('#SAR_ table.SAR_testdrive td').css('background', '');
					}
				});
			} else {
				//elem.css('background', '#ecf0f5');
				elem.css('background', '');
			}
		}, 0);
	});
	$('#SAR_ table.SAR_offer td').click(function(event) {
		const elem = $(this);
		elem.css('background', '#9ad0ff');

		if ( $(this).text() != '' ) {
			// alert('Hello world;');
			elem.css('background', '');
			$('#myModal').modal('toggle')
		} else {

			setTimeout(function(){
				if (confirm("Заполнить карточку?")) {
					$.ajax({
						type: "POST",
						url: "<?=base_url('sar/requests_add')?>",
						data: "SAR_testdrive_car_id="+elem.parent('tr').find('input:first-child').val()+"&SAR_request_type=6",
						success: function(data){
							window.open('<?=base_url('requests/add')?>');
							elem.css('background', '');
						}
					});
				} else {
					//elem.css('background', '#ecf0f5');
					elem.css('background', '');
				}
			}, 0);

		}
	});

</script>

<!-- loading -->
<script>
	$('#sar_form').submit(function(event) {
		$('.loading_SAR').show();
	});
</script>
