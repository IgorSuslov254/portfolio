$(document).ready(function($) {
    $('#new_fourth_block .body > div:nth-child(1) > div').hover(function () {
            $(this).find('div').addClass('mask');
        }, function () {
            $(this).find('div').removeClass('mask');
        }
    );

    // click type object
    $("#new_fourth_block .js-type-building-id").click(function () {
        $("#new_fourth_block .js-type-building-id").each(function () {
            $(this).removeClass('active').find('stop:nth-child(1), stop:nth-child(2)').attr('stop-color', '#FC8F49');
        });
        $(this).addClass('active').find('stop').attr('stop-color', '#FFF');

        $("#new_fourth_block .js-square-panel").each(function () {
            $(this).addClass("d-none");
        });
        $("#new_fourth_block .js-square-panel[data-type-building-id="+$(this).data("type-building-id")+"]").removeClass("d-none");

        changeBuildingObject({
            "typeBuildingId": $(this).data("type-building-id"),
            "squareBuildingId": 1
        });

        changeCarousel({
            "typeBuildingId": $(this).data("type-building-id"),
            "squareBuildingId": 1,
            "numberBuildingId": 1
        })
    });

    // click square object
    $("#new_fourth_block .js-click-square-building").click(function () {
        changeBuildingObject({
            "typeBuildingId": $(this).data("type-building-id"),
            "squareBuildingId": $(this).data("square-building-id")
        });

        changeCarousel({
            "typeBuildingId": $(this).data("type-building-id"),
            "squareBuildingId": $(this).data("square-building-id"),
            "numberBuildingId": 1
        })
    });

    //click object
    $("#new_fourth_block .js-building-object").click(function () {
        $("#new_fourth_block .js-building-object").each(function () {
           $(this).removeClass("active");
        });
        $(this).addClass("active");

        changeCarousel({
            "typeBuildingId": $(this).data("type-building-id"),
            "squareBuildingId": $(this).data("square-building-id"),
            "numberBuildingId": $(this).data("number_building_id")
        })
    });

    //click get-estimate
    $("#new_fourth_block .js-get-estimate").click(function () {
        $("#successCallbackModal #third_block_next h1").text("Чтобы получить расчет сметы");

        $("#successCallbackModal .callback").addClass("d-block").removeClass("d-none");
        $("#successCallbackModal .success").addClass("d-none").removeClass("d-block");

        const myModalEl = document.getElementById('successCallbackModal')
        const modal = new mdb.Modal(myModalEl)
        modal.show()
    });

    $('body').on('click', '#new_fourth_block .carousel img', function () {
        $("#spinner-load").removeClass("d-none").addClass("d-flex");
    });
    window.addEventListener('opened.mdb.lightbox', () => {
        setTimeout(function (){
            $("#spinner-load").removeClass("d-flex").addClass("d-none");
        }, 1500);
    });
});

const changeBuildingObject = (params) => {
    $("#new_fourth_block .js-click-square-building").each(function () {
        $(this).removeClass("active");
    });
    $("#new_fourth_block .js-click-square-building[data-type-building-id="+params.typeBuildingId+"][data-square-building-id="+params.squareBuildingId+"]").addClass("active");

    let countJsBuildingObject = 0;
    $("#new_fourth_block .js-building-object").removeClass("active").each(function () {
        if ($(this).data("type-building-id") == params.typeBuildingId && $(this).data("square-building-id") == params.squareBuildingId){
            if (countJsBuildingObject == 0){
                countJsBuildingObject++;
                $(this).addClass("active");
            }
            $(this).removeClass("d-none");
        } else {
            $(this).addClass("d-none");
        }
    });
}
const changeCarousel = (params) => {
    $("#new_fourth_block .js-carousel").each(function () {
        if ($(this).data("type-building-id") == params.typeBuildingId && $(this).data("square-building-id") == params.squareBuildingId && $(this).data("number_building_id") == params.numberBuildingId){
            $(this).removeClass("d-none");
        } else {
            $(this).addClass("d-none");
        }
    });

    $.ajax({
        url: './plaster/get-object-type-square',
        method: 'GET',
        data: {
            'center': [
                ['type_building_id', '=', params.typeBuildingId],
                ['square_building_id', '=', params.squareBuildingId],
                ['number_building_id', '=', params.numberBuildingId]
            ],
            'right': true
        },
        success: function( data ){
            $("#new_fourth_block .js-right-content > h3").text(data.right.name);
            $("#new_fourth_block .js-right-content > h4").text(data.right.address);
            $("#new_fourth_block .js-right-content > ul > li:nth-child(1) > p").text(data.right.count_day);
            $("#new_fourth_block .js-right-content > ul > li:nth-child(2) > p").text(data.right.cost);
            $("#new_fourth_block .js-right-content > ul > li:nth-child(3) > p").text(data.right.square_wall);
            $("#new_fourth_block .js-right-content > ul > li:nth-child(4) > p").text(data.right.square_floor);
            $("#new_fourth_block .js-right-content > ul > li:nth-child(5) > p").text(data.right.material);
        },
        error: function (jqXHR, exception) {
            error_ajax(jqXHR, exception);
        }
    });
}
