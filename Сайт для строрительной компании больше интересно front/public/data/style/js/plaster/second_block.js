/*************************************/
/***********second block**************/
/*************************************/
$(document).ready(function() {
	let params = {
		'from_elem' 	: '#second_block .left_second_block',
		'set_hight' 	: '#second_block .right_second_block',
		'show_photo' 	: '#second_block .right_second_block img',
		'offset' 		: 120,
	}

	set_hight_photo( params );
});

$('#second_block .js-modal-video-show').click(() => {
    if (window.innerWidth < 992) return false;

    if (player === false) player = onYouTubeIframeAPIReady("player");

    const myModalEl = document.getElementById('aboutUsModal')
    const modal = new mdb.Modal(myModalEl)
    modal.show()

    myModalEl.addEventListener('hidden.mdb.modal', (e) => {
        stopVideo();
    });
});
