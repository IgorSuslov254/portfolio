/*************************************/
/************fifth block**************/
/*************************************/

$(document).ready(function($) {
    $('#fifth_block .video').hide();

    showVideo();

    $('#fifth_block .btn-light').click(function() {
        if($(this).data('type') == "video"){
            showVideo();
        } else {
            showText();
        }
    });
});

const counter = () => {
    let count = Number($("#fifth_block #counter").data("count").replace(/\s/g,''));

    for (let i = 0; i <= count; i++){
        setTimeout(function (){
            $("#fifth_block #counter").text(i.toLocaleString());
        }, i);

    }
}

const showVideo = () => {
    let getSrc = false;
    $('#youtube_carousel .multi-carousel-item iframe').each(function() {
        if(!$(this).attr('src')){
            getSrc = true;
        }
    });

    if(getSrc) {
        $.ajax({
            url: './plaster/get-src-youtube',
            method: 'get',
            success: function (datas) {
                datas.map((data, key) => {
                    key++;
                    $('#youtube_carousel .multi-carousel-item:nth-child(' + key + ') iframe').attr('src', data.src);
                    $('#youtube_carousel .multi-carousel-item:nth-child(' + key + ') .spinner-border').hide();
                })
            },
            error: function (jqXHR, exception) {
                error_ajax(jqXHR, exception);
            }
        });
    }

    $('#fifth_block .video').show();
    $('#fifth_block .text').hide();
}

const showText = () => {
    $('#fifth_block .video').hide();
    $('#fifth_block .text').show();
}
