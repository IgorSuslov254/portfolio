/*************************************/
/***********load library**************/
/*************************************/
// This code loads the IFrame Player API code asynchronously.
const tag = document.createElement('script');

tag.src = "https://www.youtube.com/iframe_api";
const firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);


/*************************************/
/************variables****************/
/*************************************/
const offset_fifth_block = $('#fifth_block').offset();
const height_fifth_block = $('#fifth_block').height();

const offset_second_block = $('#second_block').offset();
const height_second_block = $('#second_block').height();

const offset_sixth_block = $('#sixth_block').offset();
const height_sixth_block = $('#sixth_block').height();

const OFFSETCOUNTER = $('#counter').offset();
const HEIGHTCOUNTER = $('#counter').height();

let player = false;
let done = false;


/*************************************/
/**************scroll*****************/
/*************************************/
function scrollElem( id = null, offset = 0 ){
	if( id == null ) return false;

	$(document).scrollTop( $('#' + id).offset().top - 100 + offset);

	return false;
}

$('a[href="scroll"]').click(function(){
	let elem = "'#" + $(this).data('id_scroll') + "'";

	$(document).scrollTop($(elem).offset().top);

	return false;
});

let one_show = {
	'sixth_block' : true,
    'secondBlock': true,
    'counter': true
}

$(document).scroll(function () {
	if (show_map() === false) one_show.sixth_block = false;
    if (addMobileVideo() === false) one_show.secondBlock = false;
    if (showCounter() === false) one_show.counter = false;
	navbarFixed();
});

$(document).ready(function() {
	if (show_map() === false) one_show.sixth_block = false;
    if (addMobileVideo() === false) one_show.secondBlock = false;
    if (showCounter() === false) one_show.counter = false;
	navbarFixed();

    $("input[type='text']:not('#fb_phone')").click(() => {
        ym(86817668,'reachGoal','wwod-popitka');
    });
});


/*************************************/
/*************function****************/
/*************************************/
function error_ajax(jqXHR, exception){
	if (jqXHR.status === 0) {
		alert('Not connect. Verify Network.');
	} else if (jqXHR.status == 404) {
		alert('Requested page not found (404).');
	} else if (jqXHR.status == 500) {
		alert('Internal Server Error (500).');
	} else if (exception === 'parsererror') {
		alert('Requested JSON parse failed.');
	} else if (exception === 'timeout') {
		alert('Time out error.');
	} else if (exception === 'abort') {
		alert('Ajax request aborted.');
	} else if (jqXHR.status == 422) {
        $("#error .alert").text(jQuery.parseJSON(jqXHR.responseText).message).addClass("show").style("z-index", "1200");
        setTimeout(() => {
            $("#error .alert").removeClass("show").style("z-index", "0");
        }, 3000);
    } else {

		if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.errors) {

			const errors = Object.values(jqXHR.responseJSON.errors)
			let error = "";

			for (var i = 0; i < errors.length; i++) {
				error += errors[i][0] + "\r\n";
			}

			alert(error);
		}
	}
}

function set_hight_photo( params ){
	let height = $(params.from_elem).outerHeight();
	$(params.set_hight).height( height + params.offset );

	$(params.show_photo).show();

    if (params.dopElem) $(params.set_hight).find(params.dopElem).css('margin-top', height);
}

function show_map(){
	var scrollTop = $(window).scrollTop();
	var windowHeight = $(window).height();

	if(scrollTop <= offset_sixth_block.top && (height_sixth_block -1500 + offset_sixth_block.top) < (scrollTop + windowHeight)){
		if ( one_show.sixth_block === true ) {

			map();

			return false;
		} else{
			return null;
		}
	} else{
		return null;
	}
}

function showCounter(){
    var scrollTop = $(window).scrollTop();
    var windowHeight = $(window).height();

    if(scrollTop <= OFFSETCOUNTER.top && (HEIGHTCOUNTER -500 + OFFSETCOUNTER.top) < (scrollTop + windowHeight)){
        if ( one_show.counter === true ) {

            counter();

            return false;
        } else{
            return null;
        }
    } else{
        return null;
    }
}

const addMobileVideo = () => {
    if (window.innerWidth >= 992) return false;

    let scrollTop = $(window).scrollTop();
    let windowHeight = $(window).height();

    if ($('#second_block .right_second_block').is(":hidden")) {
        if (scrollTop <= offset_second_block.top && (height_second_block - 800 + offset_second_block.top) < (scrollTop + windowHeight)) {
            if (one_show.secondBlock === true) {
                if (player === false) player = onYouTubeIframeAPIReady("videoMobilePlanshetPlay");

                $('#second_block #videoMobilePlanshet .embed-responsive-item').remove();

                return false;
            } else {
                return null;
            }
        } else {
            return null;
        }
    } else{
        return null;
    }
}

const integrationAmoSrm = (params) => {
    $.ajax({
        url: './amo-srm',
        method: 'post',
        data: params.data,
        error: function (jqXHR, exception) {
            error_ajax(jqXHR, exception);
        }
    });
}

function navbarFixed(){
	let scrollTop = $(window).scrollTop();

	if (scrollTop == 0){
		$('#first_block nav').removeClass('navbar_fixed');
        $('#first_block #logo_mobile').css('top', '23px');
        $('#first_block .shining').slideDown();
	} else {
		if (!$('#first_block nav').hasClass('navbar_fixed')){
			$('#first_block nav').addClass('navbar_fixed');
            $('#first_block #logo_mobile').css('top', '13px');
            $('#first_block .shining').hide();
		}
	}
}

/**
 * This function creates an <iframe> (and YouTube player)
 * after the API code downloads.
 * @return {boolean}
 */
const onYouTubeIframeAPIReady = (blockId) => {
    return new YT.Player(blockId, {
        height: '100%',
        videoId: 'XraW-skuI4w',
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange
        }
    });
}

/**
 * The API will call this function when the video player is ready
 */
const onPlayerReady = event => {
    if (window.innerWidth >= 992) event.target.playVideo();
}

/**
 * The API call this function when modal hide
 */
const stopVideo = () => {
    player.stopVideo();
}

/**
 * event listen play video
 */
const onPlayerStateChange = event => {
    if (event.data == YT.PlayerState.PLAYING && !done) {
        ym(86817668,'reachGoal','sow_video');
        done = true;
    }
}
