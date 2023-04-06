/*************************************/
/************calc block***************/
/*************************************/
$(document).ready(function() {
    $("#calc #calcFloor").change(() => {
        $("#calc #calcWall").prop('checked', false);

        changeCost({
            "workWithMaterial": 0,
            "workWithoutMaterial": 0
        })
        $("#calc #calcSquare").val("");
    });
    $("#calc #calcWall").change(() => {
        $("#calc #calcFloor").prop('checked', false);

        changeCost({
            "workWithMaterial": 0,
            "workWithoutMaterial": 0
        })
        $("#calc #calcSquare").val("");
    });

    $("#calc #calcSquare").keyup(function () {
        if ($(this).val().length > 1){
            $("#calc .calc-body-calc-error").addClass("d-none");

            if ($(this).val() > 9){
                let cof = 1;
                let key = 0;
                let work = 0;
                let min = 0;
                let message = false;
                let color = "";

                if ($('#calc #calcFloor').is(':checked')){
                    cof = 2.92;
                    min = 25;

                    if ($(this).val() >= 25 && $(this).val() <= 28){
                        key = 723;
                        work = 371;
                        message= "*Мы работаем от 100 м2 по стенам";
                        color = "#FF0000";
                    }
                    if ($(this).val() >= 28 && $(this).val() <= 50){
                        key = 548;
                        work = 258;
                    }
                    if ($(this).val() >= 51 && $(this).val() <= 100){
                        key = 503;
                        work = 238;
                    }
                    if ($(this).val() >= 101 && $(this).val() <= 300){
                        key = 492;
                        work = 222;
                        message = "*Вам будет предоставлена скидка";
                        color = "#FFF";
                    }
                    if ($(this).val() >= 301 && $(this).val() <= 99999){
                        key = 472;
                        work = 214;
                        message = "*Вам будет предоставлена скидка";
                        color = "#FFF";
                    }
                    if ($(this).val() > 99999){
                        key = 472;
                        work = 214;
                        message = "*Превышено максимально допутимое число";
                        color = "#FF0000";
                        $("#calc #calcSquare").val("99999");
                    }
                } else{
                    min = 70;

                    if ($(this).val() >= 70 && $(this).val() <= 99){
                        key = 723;
                        work = 398;
                        message = "*Мы работаем от 100 м2 по стенам";
                        color = "#FF0000";
                    }
                    if ($(this).val() >= 100 && $(this).val() <= 150){
                        key = 548;
                        work = 258;
                    }
                    if ($(this).val() >= 151 && $(this).val() <= 300){
                        key = 503;
                        work = 238;
                    }
                    if ($(this).val() >= 301 && $(this).val() <= 900){
                        key = 492;
                        work = 222;
                        message = "*Вам будет предоставлена скидка";
                        color = "#FFF";
                    }
                    if ($(this).val() >= 901 && $(this).val() <= 999999){
                        key = 472;
                        work = 214;
                        message = "*Вам будет предоставлена скидка";
                        color = "#FFF";
                    }
                    if ($(this).val() > 999999){
                        key = 472;
                        work = 214;
                        message = "*Превышено максимально допутимое число";
                        color = "#FF0000";
                        $("#calc #calcSquare").val("999999");
                    }
                }

                if (key == 0) message = "*Значение поле площадь не должно быть меньше "+ min;

                let workWithMaterial = Math.round($(this).val() * cof * key).toLocaleString();
                let workWithoutMaterial = Math.round($(this).val() * cof * work).toLocaleString();

                changeCost({
                    "workWithMaterial": workWithMaterial,
                    "workWithoutMaterial": workWithoutMaterial,
                    "message": message,
                    "color": color
                })
            } else{
                if ($(this).val().length > 1){
                    $("#calc .calc-body-calc-error").text("*Поле площадь должно быть числом").removeClass("d-none").css("color", "#FF0000");
                } else {
                    changeCost({
                        "workWithMaterial": 0,
                        "workWithoutMaterial": 0
                    })
                }
            }
        } else {
            changeCost({
                "workWithMaterial": 0,
                "workWithoutMaterial": 0
            })
        }
    });

    $("#calc #calcSquare").click(function() {
        ym(86817668,'reachGoal','area');
    });

    $("#calc #calcFloor").click(function() {
        ym(86817668,'reachGoal','floor');
    });

    $("#calc #calcWall").click(function() {
        ym(86817668,'reachGoal','wall');
    });
});

const changeCost = (params) => {
    $("#calc .calc-body-calc-error").addClass("d-none");
    $("#calc .calc-body-price-key span").text(params.workWithMaterial);
    $("#calc .calc-body-price-work span").text(params.workWithoutMaterial);

    if (params.message){
        $("#calc .calc-body-calc-error").text(params.message).removeClass("d-none").css("color", params.color);
    }
}
