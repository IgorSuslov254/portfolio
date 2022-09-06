import React, {useContext} from 'react';
import {MDBBtn, MDBCol, MDBRow} from "mdb-react-ui-kit";
import ChildrenObjectData from "./object_data/ChildrenObjectData";
import {MeasuringContext} from "../../context";


const ObjectData = () => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateSelectFloor = (params) => {
        params.e.preventDefault();

        measuringContext.selectFloor = params.e.target.value;
        setMeasuringContext({...measuringContext});
    }

    const addFloor = (e) => {
        e.preventDefault();

        if(!measuringContext.selectFloor) return false

        let newShowedFloor;
        measuringContext.floors.map((floor) => {
            if(floor.id == measuringContext.selectFloor) newShowedFloor = floor;
        })

        newShowedFloor.rooms = [{
            "name": "Помещение 1",
            "SQM": "",
            "MP": "",
            "corners": "",
            "profile": "",
            "typeMixture": "1",
            "addWorks": [],
            "comment": "",
        }];
        newShowedFloor.addWorks = [];
        newShowedFloor.height = "";

        measuringContext.showedFloor.push(newShowedFloor);
        measuringContext.floors = measuringContext.floors.filter(item => item.id != measuringContext.selectFloor);
        measuringContext.selectFloor = "";
        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow className="mt-5">
            <MDBCol>
                <select defaultValue={measuringContext.selectFloor} onChange={e => updateSelectFloor({"e": e})} className="select-css">
                    <option value="">Выберете этаж</option>
                    {measuringContext.floors.map(floor =>
                        <option value={floor.id} key={floor.id}>{floor.name}</option>
                    )}
                </select>
            </MDBCol>
            <MDBCol>
                <MDBBtn onClick={e => addFloor(e)} className='text-dark' color='light' block>Добавить этаж</MDBBtn>
            </MDBCol>

            {measuringContext.isNullObject.isNullObject(measuringContext.showedFloor) &&
                <ChildrenObjectData/>
            }
        </MDBRow>
    );
};

export default ObjectData;