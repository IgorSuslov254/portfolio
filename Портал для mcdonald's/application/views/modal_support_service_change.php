<style>
    #modal_support_service_change .btn-brown{
        text-transform: none;
        font-size: 12px;
        width: 100%;
    }
</style>

<div class="modal fade" id="modal_support_service_change" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-notify" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="white-text float-right">&times;</span>
            </button>
            <h2 class="font-weight-bold"><?= $lang->line('ss_modal_title') ?></h2>
            <div class="container">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="button" data-acction="#support_service_form" data-hide="#support_service_form_one, #support_service_form_two, #support_service_form_new_changetype" data-scrool="70" class="btn btn-brown btn-rounded waves-effect"><?= $lang->line('ss_modal_button_one') ?></button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" data-acction="#support_service_form_new_changetype" data-hide="#support_service_form_one, #support_service_form_two, #support_service_form" data-scrool="200" class="btn btn-brown btn-rounded waves-effect"><?= $lang->line('ss_modal_button_two') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
