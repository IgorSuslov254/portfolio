/**
 * class for get data for amoCRM
 * @author Suslov Igor <IUSuslov@1cbit.ru>
 */
class Amo
{
    /**
     * get data call back and another forms and send amo
     * @param dataForm
     * @return void
     */
    sendDataAmoCallBack(dataForm)
    {
        let data = {
            "id_form": dataForm.id_form,
            "name": dataForm.names.split('|')[0],
            "city": dataForm.city.split('|')[0],
            "phone": dataForm.phone.split('|')[0],
            "email": dataForm.email.split('|')[0],
            "formName": dataForm.formName.split('|')[0],
            "url": window.location.href,
            "utm_source": this.getParameterByName("utm_source"),
            "utm_medium": this.getParameterByName("utm_medium"),
            "utm_campaign": this.getParameterByName("utm_campaign"),
            "utm_term": this.getParameterByName("utm_term"),
            "utm_content": this.getParameterByName("utm_content"),
            "utm_referrer": this.getParameterByName("utm_referrer")
        }
        this.sendData(data);
    }

    /**
     * get data gift card form and send amo
     * @param dataForm
     * @return void
     */
    sendDataAmoGiftCard(dataForm)
    {
        let data = {
            "id_form": dataForm.id_form,
            "name": dataForm.name,
            "city": dataForm.CITY,
            "phone": dataForm.phone,
            "email": "",
            "comment": dataForm.text,
            "price": dataForm.summ,
            "address": dataForm.adres,
            "formName": dataForm.formName,
            "url": window.location.href,
            "utm_source": this.getParameterByName("utm_source"),
            "utm_medium": this.getParameterByName("utm_medium"),
            "utm_campaign": this.getParameterByName("utm_campaign"),
            "utm_term": this.getParameterByName("utm_term"),
            "utm_content": this.getParameterByName("utm_content"),
            "utm_referrer": this.getParameterByName("utm_referrer")
        }
        this.sendData(data);
    }

    /**
     * get data certificate pay form and send amo
     * @param dataForm
     * @return void
     */
    sendDataCertificatePay(dataForm)
    {
        let data = {
            "id_form": "покупка сертификата",
            "name": dataForm.split('[~]')[2],
            "city": dataForm.split('[~]')[9],
            "phone": dataForm.split('[~]')[4],
            "email": dataForm.split('[~]')[3],
            "price": dataForm.split('[~]')[1],
            "formName": dataForm.split('[~]')[8],
            "url": window.location.href,
            "utm_source": this.getParameterByName("utm_source"),
            "utm_medium": this.getParameterByName("utm_medium"),
            "utm_campaign": this.getParameterByName("utm_campaign"),
            "utm_term": this.getParameterByName("utm_term"),
            "utm_content": this.getParameterByName("utm_content"),
            "utm_referrer": this.getParameterByName("utm_referrer")
        }
        this.sendData(data);
    }

    /**
     * get utm
     * @param name
     * @return string
     */
    getParameterByName(name)
    {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        let regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
        let results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    /**
     * send data amo
     * @param data
     * @return {Promise<void>}
     */
    async sendData(data)
    {
        data = new URLSearchParams(data);

        let xhr = new XMLHttpRequest();

        xhr.open('POST', '/AMO_CRM/amoCrm.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
        xhr.send(data.toString());
        xhr.onreadystatechange = function() {
            if (xhr.readyState != 4) return;
            if (xhr.status != 200) console.log(xhr.status + ': ' + xhr.statusText);
        }
    }
}

const amo = new Amo();