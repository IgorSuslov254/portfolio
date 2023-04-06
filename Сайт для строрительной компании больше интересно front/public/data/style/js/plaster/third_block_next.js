/*************************************/
/*********third block next************/
/*************************************/
$(document).ready(function() {
	// call back
	$('#third_block_next form, #calc form').submit(function(event) {
		$.ajax({
			url: './plaster/callBack',
			method: 'post',
			data: $(this).serialize(),
			success: function( data ){
				if (data){
                    $("#successCallbackModal .success > .js-content").html(
                        "<p class=\"d-none d-sm-block\">Заявка отправлена!</p>\n" +
                        "<p class=\"d-none d-sm-block\">Перезвонить вам в ближайшее время</p>\n" +
                        "<p class=\"d-sm-none d-block\">Заявка <br> отправлена!</p>\n" +
                        "<p class=\"d-sm-none d-block\">Перезвонить вам <br> в ближайшее время</p>"
                    );

                    if ($("#successCallbackModal").hasClass("show")) {
                        $("#successCallbackModal .success").addClass("d-block").removeClass("d-none");
                        $("#successCallbackModal .callback").addClass("d-none").removeClass("d-block");
                    } else {
                        $("#successCallbackModal .success").addClass("d-block").removeClass("d-none");
                        $("#successCallbackModal .callback").addClass("d-none").removeClass("d-block");

                        const myModalEl = document.getElementById('successCallbackModal')
                        const modal = new mdb.Modal(myModalEl)
                        modal.show()
                    }

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
});
