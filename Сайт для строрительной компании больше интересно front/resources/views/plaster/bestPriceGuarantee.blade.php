<section id="bestPriceGuarantee">
    <div class="container">
        <h1>{!!__('plaster/bestPriceGuarantee.h1')!!}</h1>
        <h2>{!!__('plaster/bestPriceGuarantee.h2')!!}</h2>
        <form class="row" enctype="multipart/form-data" method="post">
            @csrf

            <div class="col-xl-4 col-lg-6">
                <label for="bestPriceGuaranteePhone">{!!__('plaster/bestPriceGuarantee.phone')!!}</label>
                <input
                    type="text"
                    data-mdb-input-mask="+7(999)999-99-99"
                    id="bestPriceGuaranteePhone"
                    name="bestPriceGuaranteePhone"
                    data-mdb-mask-placeholder="true"
                    required
                >
            </div>
            <div class="col-xl-4 col-lg-6">
                <p class="bestPriceGuaranteeFileLabel">Ваша смета</p>
                <label for="bestPriceGuaranteeFile">
                    <svg width="31" height="28" viewBox="0 0 31 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.1273 26.5409C11.6184 26.6506 10.0591 26.0901 8.80517 25.1079C6.37543 23.2048 5.03232 19.6714 7.16642 16.6004C8.41682 14.801 13.4256 7.59384 15.9257 3.99516C16.8133 2.7179 18.1778 2.15053 19.6682 2.43816C21.1319 2.72047 22.5216 3.80853 23.2075 5.20986C23.9063 6.6378 23.7982 8.18889 22.9109 9.46613L14.5342 21.5207C14.0559 22.209 13.4391 22.6406 12.7512 22.7691C12.0701 22.8959 11.3519 22.7116 10.7785 22.2626C9.73823 21.448 9.25212 19.8077 10.3356 18.2495L16.2194 9.78196C16.4611 9.43415 16.9695 9.39719 17.3553 9.6994C17.7411 10.0016 17.8579 10.5285 17.6161 10.876L11.7325 19.3433C11.2238 20.0751 11.308 20.7324 11.6537 21.0032C11.8053 21.1219 12.0051 21.1658 12.2153 21.1262C12.538 21.066 12.8656 20.8173 13.1373 20.4262L21.5141 8.37207C22.0887 7.54515 22.1732 6.6152 21.7513 5.75394C21.3358 4.9055 20.4953 4.24716 19.6092 4.07603C18.7098 3.90223 17.8976 4.26229 17.3225 5.08947C14.8225 8.68742 9.8137 15.8955 8.56319 17.6944C6.93123 20.0429 7.99827 22.5309 9.6801 23.8483C11.3619 25.1656 13.9158 25.5144 15.5484 23.1656L24.3077 10.5604C24.5494 10.2126 25.0578 10.1757 25.4436 10.4779C25.8294 10.7801 25.9462 11.3069 25.7045 11.6545L16.9452 24.2599C15.9129 25.7461 14.542 26.4381 13.1273 26.5409Z" fill="#030104" fill-opacity="0.2"/>
                    </svg>

                    Прикрепить файл
                </label>
                <input
                    type="file"
                    id="bestPriceGuaranteeFile"
                    name="bestPriceGuaranteeFile"
                    class="text-center d-none"
                    required
                >
            </div>
            <div class="col-xl-4 col-lg-12">
                <button type="submit" class="btn btn-warning btn-rounded">{!!__('plaster/bestPriceGuarantee.button')!!}</button>
            </div>
        </form>
    </div>
</section>
