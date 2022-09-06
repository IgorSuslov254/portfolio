import React, {useContext} from 'react';
import {MDBCol, MDBInput, MDBRow} from "mdb-react-ui-kit";
import InputMask from 'react-input-mask';
import {MeasuringContext} from "../../context";

const CustomerData = () => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateCustomerData = (params) => {
        params.e.preventDefault();

        measuringContext.customerData[params.name] = params.e.target.value;
        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow>
            <MDBCol sm='4' className='mt-3'>
                <MDBInput value={measuringContext.customerData.name} onChange={e => updateCustomerData({"e": e, "name": "name"})} label='ФИО клиента' type='text' contrast required/>
            </MDBCol>
            <MDBCol sm='4' className='mt-3'>
                <MDBInput value={measuringContext.customerData.post} onChange={e => updateCustomerData({"e": e, "name": "post"})} label='Должность' type='text' contrast required/>
            </MDBCol>
            <MDBCol sm='4' className='mt-3'>
                <MDBInput value={measuringContext.customerData.measurer} onChange={e => updateCustomerData({"e": e, "name": "measurer"})} label='Замерщик' type='text' contrast required/>
            </MDBCol>
            <MDBCol sm='4' className='mt-3'>
                <MDBInput value={measuringContext.customerData.address} onChange={e => updateCustomerData({"e": e, "name": "address"})} label='Адрес' type='text' contrast required/>
            </MDBCol>
            <MDBCol sm='4' className='mt-3'>
                <div className="form-outline form-white">
                    <InputMask value={measuringContext.customerData.phone} onChange={e => updateCustomerData({"e": e, "name": "phone"})} id="form12" className={"form-control" + (measuringContext.customerData.phone ? ' active' : '')} mask="+7\(999)999-99-99" required></InputMask>
                    <label className="form-label" htmlFor="form12">Номер телефона</label>
                    <div className="form-notch">
                        <div className="form-notch-leading"></div>
                        <div className="form-notch-middle" style={{width: "110px"}}></div>
                        <div className="form-notch-trailing"></div>
                    </div>
                </div>
            </MDBCol>
            <MDBCol sm='4' className='mt-3'>
                <select defaultValue={measuringContext.customerData.typeSpace} onChange={e => updateCustomerData({"e": e, "name": "typeSpace"})} className="select-css" required>
                    <option value="" disabled>Тип жилого помещения</option>
                    <option value="1">Квартира</option>
                    <option value="2">Дом</option>
                </select>
            </MDBCol>
        </MDBRow>
    );
};

export default CustomerData;