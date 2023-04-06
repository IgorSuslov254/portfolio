$(document).ready(function($) {
	// typing
	const params = {
		strings: fb_h2,
		typeSpeed: 70,
		backDelay: 1500,
		startDelay: 500,
		loop: true,
		// loopCount: 2,
		contentType: 'html'
	}
	new Typed('#typing > h2', params);

    $("#first_block .js-callback").click(function () {
        $("#successCallbackModal .callback").addClass("d-block").removeClass("d-none");
        $("#successCallbackModal .success").addClass("d-none").removeClass("d-block");

        const myModalEl = document.getElementById('successCallbackModal')
        const modal = new mdb.Modal(myModalEl)
        modal.show()
    });
});
