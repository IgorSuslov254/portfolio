/*************************************/
/********bestPriceGuarantee***********/
/*************************************/

$(document).ready(function () {
    document.querySelector('#bestPriceGuarantee #bestPriceGuaranteeFile').addEventListener('change', function() {
        let fileNames = this.value.split('\\');
        let fileName = fileNames[fileNames.length - 1];

        $("#bestPriceGuarantee label[for=\"bestPriceGuaranteeFile\"]").html(fileName);
    });

    $("#bestPriceGuarantee form").submit(function () {
        let formData = new FormData();
        formData.append('file', $("#bestPriceGuarantee #bestPriceGuaranteeFile")[0].files[0]);
        formData.append('_token', $("#bestPriceGuarantee input[name=\"_token\"]").val());
        formData.append('phone', $("#bestPriceGuarantee #bestPriceGuaranteePhone").val());

        $.ajax({
            type: "POST",
            url: './plaster/send-document-telegram',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function(data){
                $("#successCallbackModal .success > .js-content").html(
                    "<p class=\"d-none d-sm-block\">Заявка отправлена!</p>\n" +
                    "<p class=\"d-none d-sm-block\">Перезвонить вам в ближайшее время</p>\n" +
                    "<p class=\"d-sm-none d-block\">Заявка <br> отправлена!</p>\n" +
                    "<p class=\"d-sm-none d-block\">Перезвонить вам <br> в ближайшее время</p>"
                );

                $("#successCallbackModal .success").addClass("d-block").removeClass("d-none");
                $("#successCallbackModal .callback").addClass("d-none").removeClass("d-block");

                const myModalEl = document.getElementById('successCallbackModal')
                const modal = new mdb.Modal(myModalEl)
                modal.show()

                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push ({'event': 'formSuccess'});

                integrationAmoSrm({"data": data})
            },
            error: function (jqXHR, exception) {
                error_ajax(jqXHR, exception);
            }
        });

        return false;
    });
});
