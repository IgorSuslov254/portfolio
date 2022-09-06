import React, {useContext} from 'react';
import {MDBCol, MDBInput, MDBRow, MDBIcon, MDBBtn} from "mdb-react-ui-kit";
import {MeasuringContext} from "../../context";

const ToWork = (props) => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateAddToWork = (params) => {
        params.e.preventDefault();

        if(params.roomIndex === false || params.index === false){
            if(params.roomIndex === false && params.index !== false){
                measuringContext.showedFloor[params.index].addWorks[params.indexAddWork][params.name] = params.e.target.value;
            } else {
                measuringContext.toWorkObject[params.indexAddWork][params.name] = params.e.target.value;
            }
        } else {
            measuringContext.showedFloor[params.index].rooms[params.roomIndex].addWorks[params.indexAddWork][params.name] = params.e.target.value;
        }

        setMeasuringContext({...measuringContext});
    }

    const deleteAddToWork = (params) => {
        params.e.preventDefault();

        if (!confirm("Доп. работа будет удалена.\r\nВы уверены?")) return false;
        if(params.roomIndex === false || params.index === false){
            if(params.roomIndex === false && params.index !== false){
                measuringContext.showedFloor[params.index].addWorks.splice(params.indexAddWork, 1);
            } else {
                measuringContext.toWorkObject.splice(params.indexAddWork, 1);
            }
        } else {
            measuringContext.showedFloor[params.index].rooms[params.roomIndex].addWorks.splice(params.indexAddWork, 1);
        }

        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow className="text-white mt-3">
            <MDBCol lg="4" size="12" className='mt-2'>
                <MDBInput className="to-work-disabled-button bg-gradient" onChange={e => updateAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork, "name": "name"})} label={props.label} contrast type='text' value={props.addWork.name} disabled />
                <MDBIcon className="times-room" size='lg' fas icon="times" onClick={e => deleteAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork})}/>
            </MDBCol>

            {props.addWork.param_one &&
                <MDBCol lg="2"  size="6" className='mt-2'>
                    <MDBInput onChange={e => updateAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork, "name": "paramOne"})} label={props.addWork.param_one} contrast type='text' value={props.addWork.paramOne} required/>
                </MDBCol>
            }

            {props.addWork.param_two &&
                <MDBCol lg="2" size="6" className='mt-2'>
                    <MDBInput onChange={e => updateAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork, "name": "paramTwo"})} label={props.addWork.param_two} contrast type='text' value={props.addWork.paramTwo} required />
                </MDBCol>
            }

            {props.addWork.param_three &&
                <MDBCol lg="2" size="6" className='mt-2'>
                    <MDBInput onChange={e => updateAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork, "name": "paramThree"})} label={props.addWork.param_three} contrast type='text' value={props.addWork.paramThree} required />
                </MDBCol>
            }

            {props.addWork.param_four &&
                <MDBCol lg="2" size="6" className='mt-2'>
                    <MDBInput onChange={e => updateAddToWork({"e": e, "index": props.index, "roomIndex": props.roomIndex, "indexAddWork": props.indexAddWork, "name": "paramFour"})} label={props.addWork.param_four} contrast type='text' value={props.addWork.paramFour} required />
                </MDBCol>
            }
        </MDBRow>
    );
};

export default ToWork;