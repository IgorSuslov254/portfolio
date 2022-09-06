<section id="support_service">
    <div class="container">
        <div class="row">
            <div class="mt-5 col-12"><h3><span>Графік роботи служби підтримки:</span> з 9:00 до 18:00, понеділок-п’ятниця (у разі термінових запитань, звертайтесь у будь-який час)</h3></div>
            <div class="mt-3 col-12"><h3><a href="tel:380442901932"><span>Телефон:</span> +38 044 290-19-32</a></h4></div>
            <div class="mt-3 col-12"><h3><a href="mailto:mcd_feedback@800.com.ua"><span>Email:</span> mcd_feedback@800.com.ua</a></h4></div>
            <div class="mt-4 col-12"><h3>Переглянути інструкцію:</h4></div>
            <div class="text-center mt-3 col-12"><a href="https://sites.google.com/800.com.ua/instruction" target = "_blank" class="btn button_yellow" style="padding: .84rem 2.14rem;">Ресторан</a></div>
            <?php if($this->session->Role_Name == 'Офіс' || $this->session->Role_Name == 'Модератор'):?>
                <div class="text-center mt-3 col-12"><a href="https://sites.google.com/800.com.ua/moderator" class="btn button_yellow" target = "_blank" style="padding: .84rem 2.14rem;">Модератор</a></div>
            <?php endif; ?>
        </div>

        <?php if ($_SESSION['Role_Name'] == 'Ресторан' ): ?>
        <div class="row mt-5 support_service_change">
            <p>
                Для того, щоб сповістити про тимчасову або постійну зміну графіку роботи ресторану або про зміну директора закладу:
                <button type="button" class="btn btn-link"> НАТИСНІТЬ ТУТ </button>
            </p>
            <p>
                Для того, щоб сповістити про відновлення роботи ресторану:
                <button type="button" class="btn btn-link"> НАТИСНІТЬ ТУТ </button>
            </p>
        </div>
        <?php endif ?>
    </div>
</section>

<?php if ($_SESSION['Role_Name'] == 'Ресторан' ): ?>

<style>
    #support_service .support_service_change > p{
        font-size: 16px !important;
        color: var(--dark);
    }
    #support_service .support_service_change > p > button{
        padding: 0;
        color: var(--warning);
    }
    .modal-open > :not(.modal, #load) {
        filter: none !important;
    }
    body {
        padding-bottom: 0;
    }

    #support_service_form,
    #support_service_form_new_changetype{
        background-image: linear-gradient(180deg,#f6d365 0,#fda085 100%);
        color: white;
        text-shadow: 0px 1px #0000002e;
    }
    #support_service_form .form-check-input[type="radio"]:not(:checked) + label::before,
    #support_service_form .form-check-input[type="radio"]:not(:checked) + label::after,
    #support_service_form label.btn input[type="radio"]:not(:checked) + label::before,
    #support_service_form label.btn input[type="radio"]:not(:checked) + label::after {
        border: 2px solid #fff;
    }
    #support_service_form .form-check-input[type="radio"]:checked + label::after,
    #support_service_form .form-check-input[type="radio"].with-gap:checked + label::after,
    #support_service_form label.btn input[type="radio"]:checked + label::after,
    #support_service_form label.btn input[type="radio"].with-gap:checked + label::after {
        background-color: #fff;
    }
    #support_service_form .form-check-input[type="radio"]:checked + label::after,
    #support_service_form .form-check-input[type="radio"].with-gap:checked + label::before,
    #support_service_form .form-check-input[type="radio"].with-gap:checked + label::after,
    #support_service_form label.btn input[type="radio"]:checked + label::after,
    #support_service_form label.btn input[type="radio"].with-gap:checked + label::before,
    #support_service_form label.btn input[type="radio"].with-gap:checked + label::after {
        border: 2px solid #fff;
        box-shadow: 0px 0px 5px 0px #0006;
    }
    #support_service_form p{
        font-size: 14px !important;
    }
    #support_service_form .md-form{
        margin: 0;
    }
    #support_service_form .md-form .form-control{
        margin: 0;
        padding: 0;
    }
    #support_service_form .md-form label{
        top: -10px;
        color: white;
    }
    #support_service_form .md-form input:not([type]),
    #support_service_form .md-form input[type="text"]:not(.browser-default),
    #support_service_form .md-form input[type="password"]:not(.browser-default),
    #support_service_form .md-form input[type="email"]:not(.browser-default),
    #support_service_form .md-form input[type="url"]:not(.browser-default),
    #support_service_form .md-form input[type="time"]:not(.browser-default),
    #support_service_form .md-form input[type="date"]:not(.browser-default),
    #support_service_form .md-form input[type="datetime"]:not(.browser-default),
    #support_service_form .md-form input[type="datetime-local"]:not(.browser-default),
    #support_service_form .md-form input[type="tel"]:not(.browser-default),
    #support_service_form .md-form input[type="number"]:not(.browser-default),
    #support_service_form .md-form input[type="search"]:not(.browser-default),
    #support_service_form .md-form input[type="phone"]:not(.browser-default),
    #support_service_form .md-form input[type="search-md"],
    #support_service_form .md-form textarea.md-textarea,
    #support_service_form_new_changetype .md-form input:not([type]),
    #support_service_form_new_changetype .md-form input[type="text"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="password"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="email"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="url"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="time"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="date"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="datetime"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="datetime-local"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="tel"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="number"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="search"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="phone"]:not(.browser-default),
    #support_service_form_new_changetype .md-form input[type="search-md"],
    #support_service_form_new_changetype .md-form textarea.md-textarea{
        border-bottom: 1px solid #fff;
        color: white;
    }
    #support_service_form .md-form input:not([type]):focus:not([readonly]),
    #support_service_form .md-form input[type="text"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="password"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="email"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="url"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="time"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="date"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="datetime"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="datetime-local"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="tel"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="number"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="search"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="phone"]:not(.browser-default):focus:not([readonly]),
    #support_service_form .md-form input[type="search-md"]:focus:not([readonly]),
    #support_service_form .md-form textarea.md-textarea:focus:not([readonly]),
    #support_service_form_new_changetype .md-form input:not([type]):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="text"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="password"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="email"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="url"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="time"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="date"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="datetime"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="datetime-local"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="tel"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="number"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="search"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="phone"]:not(.browser-default):focus:not([readonly]),
    #support_service_form_new_changetype .md-form input[type="search-md"]:focus:not([readonly]),
    #support_service_form_new_changetype .md-form textarea.md-textarea:focus:not([readonly]){
        box-shadow: 0 1px 0 0 #fff;
    }

    #support_service_form .md-form label,
    #support_service_form_new_changetype .md-form label{
        color: white;
    }

    .picker__date-display{
        background-color: var(--warning);
    }
    .picker .picker__box{
        border-radius: 15px !important;
        box-shadow: 0px 0px 10px 0px #07070769;
        border: none;
    }
    .picker__date-display .clockpicker-display .clockpicker-display-column{
        color: white;
    }
    .clockpicker-plate .clockpicker-dial .clockpicker-tick.active,
    .clockpicker-plate .clockpicker-dial .clockpicker-tick:hover {
        background-color: var(--warning);
    }

    /* form two */
    #support_service_form_two .form-check-input[type="radio"]:not(:checked) + label,
    #support_service_form_two .form-check-input[type="radio"]:checked + label,
    #support_service_form_two label.btn input[type="radio"]:not(:checked) + label,
    #support_service_form_two label.btn input[type="radio"]:checked + label{
        font-size: 12px;
    }
    #support_service_form_two .form-check-input[type="radio"] + label::before,
    #support_service_form_two .form-check-input[type="radio"] + label::after,
    #support_service_form_two label.btn input[type="radio"] + label::before,
    #support_service_form_two label.btn input[type="radio"] + label::after {
        margin: 4px 0;
    }
</style>

<section id="support_service_form" class="mt-5 py-5">
    <div class="container">
        <div id="choose_support_service_form" class="text-center">
            <h2 class="mr-3"><?= $choose_support_service_form['choose'] ?></h2>

            <?php foreach ($choose_support_service_form['constantlies'] as $constantlie): ?>
                <div class="form-check form-check-inline">
                    <input type="radio" class="form-check-input" id="<?= $constantlie['id'] ?>" name="<?= $constantlie['name'] ?>">
                    <label class="form-check-label" for="<?= $constantlie['id'] ?>"><?= $constantlie['text'] ?></label>
                </div>
            <?php endforeach ?>
        </div>

        <hr class="white">

        <form id="support_service_form_one" class="row mt-2 align-items-end">
            <input type="hidden" name="new_changetype" value="100000000">
            <input type="hidden" name="new_schedulechangetype" value="100000000">
            <div class="col-md-6" id="constantly_form">
                <h3><?= $form_one['title'] ?></h3>
                <?php foreach ($form_one['constantly_forms']['elements'] as $element): ?>
                    <div class="row align-items-center mt-4">
                        <div class="col-6">
                            <p><?= $element['text'] ?></p>
                        </div>
                        <div class="col-3">
                            <div class="md-form">
                                <input 
                                    type="text" 
                                    id="<?= $element['name'] ?>_c" 
                                    name="<?= $element['name'] ?>_c" 
                                    value="<?= explode('-', $element['value'])[0] ?? '' ?>"
                                    class="form-control timepicker">
                                <label for="<?= $element['name'] ?>_c"><?= $form_one['constantly_forms']['in'] ?></label>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="md-form">
                                <input 
                                    type="text" 
                                    id="<?= $element['name'] ?>_k" 
                                    name="<?= $element['name'] ?>_k" 
                                    value="<?= explode('-', $element['value'])[1] ?? '' ?>"
                                    class="form-control timepicker">
                                <label for="<?= $element['name'] ?>_k"><?= $form_one['constantly_forms']['to'] ?></label>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="form-group shadow-textarea mt-2">
                    <textarea class="form-control z-depth-1" id="<?= $form_one['comments']['name'] ?>" name="<?= $form_one['comments']['name'] ?>" rows="3" placeholder="<?= $form_one['comments']['placeholder'] ?>" maxlength="<?= $form_one['comments']['maxlength'] ?>"><?= $form_one['comments']['value'] ?></textarea>
                </div>
                <div class="form-group shadow-textarea mt-4 mb-0">
                    <textarea class="form-control z-depth-1" id="<?= $form_one['name']['name'] ?>" name="<?= $form_one['name']['name'] ?>" rows="3" placeholder="<?= $form_one['name']['placeholder'] ?>" maxlength="<?= $form_one['name']['maxlength'] ?>"><?= $form_one['name']['value'] ?></textarea>
                </div>
            </div>
            <div class="col-12 mt-3 text-right">
                <button type="submit" class="btn btn-brown mx-0"><?= $form_one['button'] ?></button>
            </div>
        </form>

        <form id="support_service_form_two" class="row mt-2 align-items-end">
            <input type="hidden" name="new_changetype" value="100000000">
            <input type="hidden" name="new_schedulechangetype" value="100000001">
            <div class="col-lg-6">
                <h3><?= $form_two['title'] ?></h3>
                <div class="row mt-2 align-items-end">
                    <div class="col-6">
                        <?php foreach ($form_two['choose'] as $key => $choose): ?>
                            <?php if ($key > 7) break; ?>
                            <div class="form-check pl-0">
                                <input 
                                    type="radio" 
                                    class="form-check-input" 
                                    id="<?= $choose['id'] ?>" 
                                    name="<?= $choose['name'] ?>" 
                                    value="<?= $choose['value'] ?>"
                                    <?php if ($choose['value'] == $form_two['checked']): ?>
                                    checked
                                    <?php endif ?>
                                >
                                <label class="form-check-label" for="<?= $choose['id'] ?>"><?= $choose['text'] ?></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="col-6">
                        <?php foreach ($form_two['choose'] as $key => $choose): ?>
                            <?php if ($key < 8) continue; ?>
                            <div class="form-check pl-0">
                                <input 
                                    type="radio" 
                                    class="form-check-input" 
                                    id="<?= $choose['id'] ?>" 
                                    name="<?= $choose['name'] ?>" 
                                    value="<?= $choose['value'] ?>"
                                    <?php if ($choose['value'] == $form_two['checked']): ?>
                                    checked
                                    <?php endif ?>
                                >
                                <label class="form-check-label" for="<?= $choose['id'] ?>"><?= $choose['text'] ?></label>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-3">
                <div class="form-group shadow-textarea">
                    <textarea class="form-control z-depth-1" id="<?= $form_two['comments']['id'] ?>" name="<?= $form_two['comments']['name'] ?>" rows="3" placeholder="<?= $form_two['comments']['placeholder'] ?>" maxlength="<?= $form_two['comments']['maxlength'] ?>"><?= $form_two['comments']['value'] ?></textarea>
                </div>
                <div class="form-group shadow-textarea mt-4 mb-0">
                    <textarea class="form-control z-depth-1" id="<?= $form_two['name']['id'] ?>" name="<?= $form_two['name']['name'] ?>" rows="3" placeholder="<?= $form_two['name']['placeholder'] ?>" maxlength="<?= $form_two['name']['maxlength'] ?>"><?= $form_two['name']['value'] ?></textarea>
                </div>
            </div>
            <div class="col-12">
                <div class="row mt-3 align-items-end">
                    <div class="col-sm-8 col-md-10">
                        <div class="md-form">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="<?= $form_two['date_time']['id'] ?>" 
                                name="<?= $form_two['date_time']['id'] ?>" 
                                placeholder="<?= $form_two['date_time']['text'] ?>"
                                value="<?= $form_two['date_time']['value'] ?>"
                            >
                            <input type="text" class="form-control datepicker d-none" id="<?= $form_two['date_time']['id'] ?>_date" name="<?= $form_two['date_time']['id'] ?>_date">
                            <input type="text" class="form-control timepicker d-none" id="<?= $form_two['date_time']['id'] ?>_time" name="<?= $form_two['date_time']['id'] ?>_time">
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-2 text-right">
                        <button type="submit" class="btn btn-brown mx-0 mb-0"><?= $form_two['button'] ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>    
</section>

<section id="support_service_form_new_changetype" class="mt-5 py-5">
    <div class="container">
        <form class="row">
            <input type="hidden" name="new_changetype" value="100000001">
            <h2 class="text-center"><?= $support_service_form_new_changetype['title'] ?></h2>
            <?php foreach ($support_service_form_new_changetype['input'] as $new_changetype): ?>
                <div class="col-sm-4">
                    <div class="md-form">
                        <input 
                            type="text" 
                            id="<?= $new_changetype['id'] ?>" 
                            name="<?= $new_changetype['id'] ?>" 
                            class="form-control" 
                            maxlength="<?= $new_changetype['maxlength'] ?>" 
                            required
                            value="<?= $new_changetype['value'] ?>"
                        >
                        <label for="<?= $new_changetype['id'] ?>"><?= $new_changetype['text'] ?></label>
                    </div> 
                </div> 
            <?php endforeach ?>
            <div class="col-12 mt-3 text-right">
                <button type="submit" class="btn btn-brown mx-0"><?= $support_service_form_new_changetype['button'] ?></button>
            </div>
        </form>
    </div>
</section>

<script>
    $('body').on('click', '#support_service .support_service_change > p:nth-child(1) > button', ()=> {
        $('#modal_support_service_change').modal('toggle');
    });

    // support_service_form

    $('#constantly_form > div > div input, #support_service_form_new_changetype input').each(function(index, el) {
        if ($(this).val() != ""){
            $(this).find('+label').addClass('active');
        }        
    });

    $('#constantly_form input, #new_resumptionwork_time').pickatime({
        donetext : 'ОК',
        cleartext: 'ВІДМІНА'
    });

    $('#new_resumptionwork_date').pickadate({
        monthsFull: ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'],
        monthsShort: ['Січ', 'Лют', 'Бер', 'Квіт', 'Трав', 'Черв', 'Лип', 'Сер', 'Вер', 'Жовт', 'Лист', 'Груд'],
        weekdaysShort: ['НД', 'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ'],
        weekdaysFull: ['Неділя', 'Понеділок', 'Вівторок', 'Середа', 'Четвер', 'П’ятниця', 'Субота'],
        weekdaysShort: ['Нед', 'Пон', 'Вів', 'Сер', 'Чет', 'П’ят', 'Суб'],
        firstDay: 1,
        today: 'Сьогодні',
        clear: '',
        close: 'Закрити',
        formatSubmit: 'dd.mm.yyyy',
        format: 'dd.mm.yyyy'
    });

    $('#new_resumptionwork').click(()=>{
        $('#new_resumptionwork_date').trigger('click');
    });

    $('#new_resumptionwork_date').change(()=> {
        $('#new_resumptionwork_time').trigger('click');
    });

    $('#new_resumptionwork_time').change(function(){
        $('#new_resumptionwork').val($('#new_resumptionwork_date').val()+' '+$(this).val());
    });

    $('#support_service_form, #support_service_form_new_changetype').hide();

    $('body').on('click', '#modal_support_service_change button', function(event){
        if ($(this).data('acction')){
            $($(this).data('acction')).slideDown();
            $($(this).data('hide')).hide();

            scroolBottom($(this).data('scrool'));
        }
        $('#modal_support_service_change').modal('toggle');
    });

    $('#constantly').click(()=> {
        $('#support_service_form_one').slideDown();
        $('#support_service_form_two').hide();

        scroolBottom(500);
    });

    $('#temporarily').click(()=> {
        $('#support_service_form_two').slideDown();
        $('#support_service_form_one').hide();

        scroolBottom(500);
    });


    // support_service_form_new_changetype

    $('#new_directorephone').mask("389999999999");


    // send form
    $('#support_service_form_one').submit(function(){
        $('#load').show();
        var formData = $(this).serialize();
        $.post('Support_service/changeWorktime/',formData,processData);
        function processData(data) {
            $('#load').hide();
            if (data.status === true){
                $('#support_service_form_one').before( alert("success", data.message) );

                $('#support_service_form_one button[type="submit"]').attr('disabled', true);
                setTimeout(()=>{
                    $('#support_service_alert').remove();
                    $('#support_service_form').hide().find('input[type="radio"]').prop('checked', false);
                }, 3000);
            } else {
                $('#support_service_form_one').before( alert("danger", data.message) );
            }
        }
        return false;
    });

    $('#support_service_form_two').submit(function(){
        $('#load').show();
        var formData = $(this).serialize();
        $.post('Support_service/changeWorktimeTemporarily/',formData,processData);
        function processData(data) {
            $('#load').hide();
            if (data.status === true){
                $('#support_service_form_two').before( alert("success", data.message) );

                $('#support_service_form_two button[type="submit"]').attr('disabled', true);
                setTimeout(()=>{
                    $('#support_service_alert').remove();
                    $('#support_service_form').hide().find('input[type="radio"]').prop('checked', false);
                }, 3000);
            } else {
                $('#support_service_form_two').before( alert("danger", data.message) );
            }
        }
        return false;
    });

    $('#support_service_form_new_changetype form').submit(function(){
        $('#load').show();
        var formData = $(this).serialize();
        $.post('Support_service/changeDirector/',formData,processData);
        function processData(data) {
            $('#load').hide();
            if (data.status === true){
                $('#support_service_form_new_changetype form').before( alert("success", data.message) );

                $('#support_service_form_new_changetype form button[type="submit"]').attr('disabled', true);
                setTimeout(()=>{
                    $('#support_service_alert').remove();
                    $('#support_service_form_new_changetype').hide().find('input[type="radio"]').prop('checked', false);
                }, 3000);
            } else {
                $('#support_service_form_new_changetype form').before( alert("danger", data.message) );
            }
        }
        return false;
    });

    $('#support_service .support_service_change > p:nth-child(2) > button').click(async (e) => {
        e.preventDefault();

        if(await confirm('Підтвердіть, будь ласка, що роботу ресторану відновлено. Підтвердити/Скасувати')){
            $('#load').show();

            var formData = {'new_changetype':100000002}
            $.post('Support_service/changeRestrant/',formData,processData);
            function processData(data) {
                $('#load').hide();

                if (data.status === true){
                    $('#support_service .support_service_change').after( alert("success", data.message) );
                }  else {
                    $('#support_service .support_service_change').after( alert("danger", data.message) );
                }
            }
        }
    });


    // fuction
    function scroolBottom( offsett = 0 ){
        $('html, body').animate({scrollTop: $(document).height() - $(window).height() + offsett}, 600);
        return null;
    }

    function alert(style, message){
        return '<div id="support_service_alert" class="alert alert-'+style+' alert-dismissible fade show" role="alert">'+
                message+
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">'+
                    '<span aria-hidden="true">&times;</span>'+
                '</button>'+
            '</div>';
    }
</script>

<?php endif ?>