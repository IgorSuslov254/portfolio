import React, {useContext} from 'react';
import {MDBBtn, MDBCol, MDBRow} from "mdb-react-ui-kit";
import MeasuringAPI from "../../API/MeasuringAPI";
import {MainContext, MeasuringContext} from "../../context";

const Submit = () => {
    const {mainContext, setMainContext} = useContext(MainContext); //глобальные данные
    const {measuringContext} = useContext(MeasuringContext); //глобальные данные

    async function saveEstimate(e){
        mainContext.showLoad = true;
        setMainContext({...mainContext});

        e.preventDefault();

        const response = await MeasuringAPI.saveEstimate(measuringContext);

        if(response.status == "error") {
            mainContext.info.info(response, "danger");
            return false;
        }

        mainContext.info.info(response, "success");
    } // сохранить смету

    const calculateEstimate = async (e) => {
        e.preventDefault();

        mainContext.showLoad = true;
        setMainContext({...mainContext});

        const response = await MeasuringAPI.calculateEstimate(measuringContext);

        if(response.status == "error") {
            mainContext.info.info(response, "danger");
            return false;
        }

        // mainContext.info.info(response, "success");

        console.log(response);

        mainContext.showLoad = false;
        setMainContext({...mainContext});

        /*mainContext.info.info({
            'status': 'success',
            'data': "В разрботке!",
        }, "success");
        return false;*/
    }

    return (
        <MDBRow>
            <MDBCol className='mt-5'>
                <MDBBtn onClick={e => saveEstimate(e)} className='text-dark' color='light' block>Сохранить замер</MDBBtn>
            </MDBCol>
            <MDBCol className='mt-5'>
                <MDBBtn onClick={e => calculateEstimate(e)} className='text-dark' color='light' block>Рассчитать смету</MDBBtn>
            </MDBCol>
        </MDBRow>
    );
};

export default Submit;