import React, {useContext} from 'react';
import {MDBBtn, MDBCol, MDBRow} from "mdb-react-ui-kit";
import AddToWork from "../AddToWork";
import {MeasuringContext} from "../../../context";

const ButtonsFloor = (props) => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const addRoom = (params) => {
        params.e.preventDefault();
        let countRoom = measuringContext.showedFloor[params.index].rooms.length + 1;
        measuringContext.showedFloor[params.index].rooms.push({"name": "Помещение " + countRoom, "SQM": "", "MP": "", "corners": "", "profile": "", "typeMixture": "", "addWorks": [],});
        setMeasuringContext({...measuringContext});
    }
    const deleteFloor = (params) => {
        params.e.preventDefault();

        let elemPush;
        measuringContext.floors.map((floor, index) => {
            if((floor.id - measuringContext.showedFloor[params.index].id) == 1) elemPush = index;
        });
        measuringContext.floors.splice(elemPush, 0, {
            "id": measuringContext.showedFloor[params.index].id,
            "name": measuringContext.showedFloor[params.index].name,
            "created_at": measuringContext.showedFloor[params.index].created_at,
            "updated_at": measuringContext.showedFloor[params.index].updated_at,
        });

        measuringContext.showedFloor.splice(params.index, 1);
        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow className="text-white">
            {/*<MDBCol sm="3" className='mt-3'>*/}
            {/*    <MDBBtn onClick={e => addRoom({"e": e, "index": props.index})} className='text-dark' color='light' block>Добавить помещение</MDBBtn>*/}
            {/*</MDBCol>*/}
            {/*<MDBCol sm="3" className='mt-3'>*/}
            {/*    <MDBBtn onClick={e => deleteFloor({"e": e, "index": props.index})} className='text-dark' color='light' block>Удалить этаж</MDBBtn>*/}
            {/*</MDBCol>*/}
            {/*<MDBCol className='mt-2'>*/}
            {/*    <AddToWork index={props.index} roomIndex={false} label="Добавить работу на этаж" />*/}
            {/*</MDBCol>*/}
        </MDBRow>
    );
};

export default ButtonsFloor;