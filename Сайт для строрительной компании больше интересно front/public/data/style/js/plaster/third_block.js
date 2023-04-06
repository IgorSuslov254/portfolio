/*************************************/
/************third block**************/
/*************************************/
$(document).ready(function() {
	let params = {
		'from_elem' 	: '#third_block > div > div > div:last-child',
		'set_hight' 	: '#third_block > div > div > div:first-child > img:last-child',
		'show_photo' 	: '#third_block > div > div > div:first-child > img:last-child',
		'offset' 		: -90
	}

	set_hight_photo( params );

	// calc
	$('#third_block form').submit(function(event) {
		$.ajax({
			url: './plaster/calc',
			method: 'post',
			data: $(this).serialize(),
			success: function(data){
				if (data){
                    $("#successCallbackModal .success > .js-content").html(
                        "<p class=\"d-none d-sm-block\">Расчёт стоимости</p>\n" +
                        "<p class=\"d-none d-sm-block\">Стоимость работ составит: "+data+" руб.</p>\n" +
                        "<p class=\"d-sm-none d-block\">Расчёт <br> стоимости</p>\n" +
                        "<p class=\"d-sm-none d-block\">Стоимость работ <br> составит: "+data+" руб.</p>"
                    );

                    $("#successCallbackModal .success").addClass("d-block").removeClass("d-none");
                    $("#successCallbackModal .callback").addClass("d-none").removeClass("d-block");

                    const myModalEl = document.getElementById('successCallbackModal')
                    const modal = new mdb.Modal(myModalEl)
                    modal.show()

					window.dataLayer = window.dataLayer || [];
					window.dataLayer.push ({'event': 'formSuccess'});

                    integrationAmoSrm({"data":data })
				}
			},
			error: function (jqXHR, exception) {
				error_ajax(jqXHR, exception);
			}
		});

		return false;
	});

    $("#third_block #materialTrue").change(() => {
        $("#third_block #materialFalse").prop('checked', false);
    })
    $("#third_block #materialFalse").change(() => {
        $("#third_block #materialTrue").prop('checked', false);
    })
});
