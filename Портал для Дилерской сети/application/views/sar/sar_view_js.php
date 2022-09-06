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
	$(document).ready( function() {
		
		drawCharts([],[],[],[]);
		$('.chart_preloader').show();
		
		$.ajax({
			type: "POST",
			url: "<?=base_url(SAR_URL.'/get_models_report_data')?>",
			data: {},
			success: function(str) {
				console.log(str, 'str');

				$('.chart_preloader').hide();
				let data = JSON.parse(str);
				if (data['SAR_MR_total'] && data['SAR_MR_total']['CC'] && data['SAR_MR_total']['CR'] && data['SAR_MR_index']) {
					let a = data['SAR_MR_total']['CC'];
					const data_CC = [a.prev_prev, a.prev, a.current];
					a = data['SAR_MR_total']['CR'];
					const data_CR = [Math.round(a.prev_prev*100), Math.round(a.prev*100), Math.round(a.current*100)];
					a = data['SAR_MR_total']['SHR'];
					const data_SHR = [a.prev_prev, a.prev, a.current];
					a = data['SAR_MR_index'];
					const labels = [a.prev_prev, a.prev, a.current];
					drawCharts(labels, data_CC, data_CR, data_SHR);
				}
				console.log(data, 'get_models_report_data');
			} // success
		}); // ajax

	});	

	function drawCharts(labels, data_CC, data_CR, data_SHR) {
		if (!document.getElementById('myChart2')) return;
		// ну что за гавнокод
		var ctx = document.getElementById('myChart2').getContext('2d');
		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				// labels: <?php echo json_encode($label_sar_mr); ?>,
				labels: labels,
				datasets: [{
					label: 'CC',
					//data: <?php echo json_encode($SAR_MR_total_CC); ?>,
					data: data_CC,
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
				labels: labels,
				datasets: [{
					type: 'line',
					label: 'Closing ratio',
					yAxisID: 'B',
					// data: <?php echo json_encode($SAR_MR_total_CR); ?>,
					data: data_CR,
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
					// data: <?php echo json_encode($SAR_MR_total_SHR); ?>,
					data: data_SHR,
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
	} // drawCharts
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
		const bkg = elem.css('background');
		elem.css('background', '#9ad0ff');

		setTimeout(function(){
			if (confirm("Заполнить карточку?")) {
				$.ajax({
					type: "POST",
					url: "<?=base_url(SAR_URL.'/requests_add')?>",
					data: "SAR_testdrive_car_id="+elem.parent('tr').find('input:first-child').val()+"&SAR_request_type=1",
					success: function(data) {
							window.open('<?=base_url('requests/add')?>');
							//$('#SAR_ table.SAR_testdrive td').css('background', '');
							if (bkg) {
								elem.css('background', bkg);
							} else {
								elem.css('background', '');
							}
					} // success
				}); // ajax
			} else {
					
					if (bkg) {
							elem.css('background', bkg);
					} else {
							elem.css('background', '');
					}

				//elem.css('background', '#ecf0f5');
				//elem.css('background', '');
			}
		}, 0);
	});
	$('#SAR_ table.SAR_offer td').click(function(event) {
		
		const elem = $(this);
		const bkg = elem.css('background');
		elem.css('background', '#9ad0ff');
	
		if ( $(this).text() != '' ) {
		
			showContracts( $(this), bkg );

		} else {
						
			setTimeout(function(){
				if (confirm("Заполнить карточку?")) {
					$.ajax({
						type: "POST",
						url: "<?=base_url(SAR_URL.'/requests_add')?>",
						data: "SAR_testdrive_car_id="+elem.parent('tr').find('input:first-child').val()+"&SAR_request_type=6",
						success: function(data){
							
							window.open('<?=base_url('requests/add')?>');
							
							if (bkg) {
									elem.css('background', bkg);
							} else {
								  elem.css('background', '');
							}

							// elem.css('background', '');
						}
					});
				} else {
					//elem.css('background', '#ecf0f5');
						if (bkg) {
								elem.css('background', bkg);
						} else {
							  elem.css('background', '');
						}
					//elem.css('background', '');
				}
			}, 0);

		}
	});


/**
 * @author aws
 * @version 2021-08-03
 * 
 * modal dialog showing contracts
 * 
 */

 function getLastDayOfMonth(year, month) {
  let date = new Date(year, month + 1, 0);
  return date.getDate();
}

function formatDay( day ) {
	if ( day < 10 ) {
			return '0'+day
	} else {
			return day;
	} 
} // formatDay

var contracts_table;
function showContracts( elem, bkg ) {

	// filter logic
	// *************************************
	const period_start = $("#period_start").datetimepicker().val();
	const period_start_arr = period_start.split('-');
	const date_start_y = period_start_arr[0];
	const date_start_m = period_start_arr[1];
	const date_start_d = period_start_arr[2];
	// console.log(date_start_y, date_start_m);

	let date_end = '';
	let date_start = '';
	let car_name = '';

	if ( elem.hasClass('total_all') ) {

			date_start = '01.' + date_start_m +'.' + date_start_y;
			//date_end = getLastDayOfMonth(date_start_y, date_start_m) + '.'+date_start_m + '.' + date_start_y;
			date_end = date_start_d + '.' + date_start_m + '.' + date_start_y;

	} else if ( elem.hasClass('total_col') ) {

			date_start = '01.' + date_start_m +'.' + date_start_y;
			//date_end = getLastDayOfMonth(date_start_y, date_start_m) + '.'+date_start_m + '.' + date_start_y;
			date_end = date_start_d + '.' + date_start_m + '.' + date_start_y;
			car_name = elem.closest('tr').find('td:first').text();

	}	else if ( elem.closest('tr').hasClass('total_row') ) {
			const day = elem.closest('tr').find('td').index(elem);
			date_start = formatDay( day ) + '.' + date_start_m +'.' + date_start_y;
			date_end = date_start;			
	} else {

			const day = elem.closest('tr').find('td').index(elem);
			date_start = formatDay( day ) + '.' + date_start_m +'.' + date_start_y;
			date_end = date_start;			
			car_name = elem.closest('tr').find('td:first').text();

	}
		
	console.log( 'date_start', date_start);
	console.log( 'date_end', date_end);
	console.log( 'car_name', car_name);
			/*{
			format: "yyyy-mm-dd",
			minView: "month",
			language: 'ru'
		});*/


	// dataTable ajax building
	// *************************************

	contracts_table = $('#contracts_table').DataTable({
      /*"processing": true,
       "serverSide": true,*/
      "ajax": {
          url: "/contract/get_contracts/full",
          type: 'GET',
          "data": function (d) {
				d.contract_date_start = date_start+' 00:00:00';
				d.contract_date_end = date_end+' 23:59:59';
				d.delivered_date_start = '';
				d.delivered_date_end = '';
				d.car_name = car_name;
          }
      },
      'paging': true,
      'pageLength': 10,
      'lengthChange': false,
      'searching': true,
      'ordering': true,
      'info': true,
      'autoWidth': true,
      collapsed: true,
      responsive: true,
      "processing": true,
      "order": [[2, 'desc']],
      "columnDefs": [
          {
              "targets": [0],
              "visible": false,
              'className': "contract_id"
          },
          //{
          //    "targets": [9],
          //    "visible": false
          //},
          {
              "targets": [1],
              className: "dealer",
              <?php if (!$this->userdata['access_employees']) : ?>
              "visible": false
              <?php endif; ?>
          },
          {
              "targets": [2, 12,14,15,18],
              render: $.fn.dataTable.render.moment('DD.MM.YYYY', 'DD.MM.YYYY'),
              className: "nowrap create_date_light_text"
          },
          {
              "targets": [24],
              responsivePriority: 1,
               "visible": false, // no delete 
              className: "nowrap delete_btn_column"
          },
          {"targets": [1,2,3,4,6,7,8,9,10,11,12,13], responsivePriority: 2},
          {"targets": [4], responsivePriority: 3},
          {"targets": [14,15], responsivePriority: 10},
          { "targets": [9], "width": "155px!important", className: "contracts_column_model" },
          { "targets": [3], "width": "155px!important", className: "contracts_column_fio" }
      ],
			
      /*
	dom: 'Bfrtip',
	buttons: [
            {
                text: 'My button',
                action: function ( e, dt, node, config ) {
                    alert( 'Button activated' );
                }
            }
        ],*/


		initComplete  : function () {
      	  $('#myModal table').show();

					if (bkg) {
							elem.css('background', bkg);
					} else {
						  elem.css('background', '');
					}

					// add Excel export button
					// ************************************



			this.api().columns().every( function () {
              var column = this;
              if (
                  column[0][0] == 8 ||
                  column[0][0] == 11
              ) {
                  //if(column[0][0] == 1) { var select = $('<select style="width: 100px;" id="filter_by_dealer"><option value="">Дилер</option></select>'); }
                  //if(column[0][0] == 9) { var select = $('<select style="width: 100px;" id="filter_by_model"><option value="">Модель SAR</option></select>'); }
                  //if(column[0][0] == 12) { var select = $('<select style="width: 100px;" id="filter_by_status"><option value="">Статус Контракту</option></select>'); }

                  if(column[0][0] == 8) {
                      var select = $('<select style="width: 100px;" id="filter_by_model"><option value="">Модель SAR</option></select>');

                      //Add Full options in column filterss
                      var cars_list_options = JSON.parse('<?php echo json_encode($cars_list); ?>');

                      $.each(cars_list_options, function (car_key, car_value) {
                          select.append('<option value="' + car_value + '">' + car_value + '</option>');
                      });
                  }
                  if(column[0][0] == 11) {
                      var select = $('<select style="width: 100px;" id="filter_by_status"><option value="">Статус Контракту</option></select>');

                      //Add Full options in column filterss
                      var status_options = JSON.parse('<?php echo json_encode($ContractStatus_picklist); ?>');

                      $.each(status_options, function (status_key, status_value) {
                          select.append('<option value="' + status_value + '">' + status_value + '</option>');
                      });
                  }

                  select.appendTo($(column.header()).empty())
                      .on('change', function () {
                          var val = $.fn.dataTable.util.escapeRegex(
                              $(this).val()
                          );
                          column
                              .search(val ? '^' + val + '$' : '', true, false)
                              .draw();
                      });

                  if (
                      column[0][0] != 8 &&
                      column[0][0] != 11
                  ) {
                      column.data().unique().sort().each(function (d, j) {
                          select.append('<option value="' + d + '">' + d + '</option>')
                      });
                  }
              }

          });
      }
  	});
  	// const export_param= `contract_date_start=${date_start}%26contract_date_end=${date_end}%26delivered_date_start=%26delivered_date_end=%26car_name=${car_name}`;  
 		 // all     
		const uri = `/contract/export/full?contract_date_start=${date_start}&contract_date_end=${date_end}&delivered_date_start=&delivered_date_end=&car_name=${car_name}`;
		const encoded = encodeURI(uri);     

		const html = `<a href="${encoded}" target="_blank" id="export_btn" class="btn btn-primary pull-left"><i class="fa fa-download"></i>&nbsp;Експорт</a>`;

		$(html).appendTo( $('.col-sm-6:eq(0)', contracts_table.table().container() ) );

		$('#myModal table').hide();
		$('#myModal').modal('toggle');

		if ( $('#myModal').data('init') != '1' ) {
				$('#myModal').data('init', '1');
				$('#myModal').on('hide.bs.modal', function() {
						if (contracts_table) contracts_table.destroy();
						//console.log('hide modal');
				});
		} // if

	} // showContracts
		

	console.log('SAR_URL', '<?=SAR_URL?>');

</script>

<!-- loading -->
<script>
	$('#sar_form').submit(function(event) {
		$('.loading_SAR').show();
	});
</script>
