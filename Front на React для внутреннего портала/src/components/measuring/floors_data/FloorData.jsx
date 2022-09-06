import React, {useContext} from 'react';
import {MDBCol, MDBInput, MDBRow, MDBBtn, MDBIcon} from "mdb-react-ui-kit";
import ToWork from "../ToWork";
import AddToWork from "../AddToWork";
import {MeasuringContext} from "../../../context";

const FloorData = (props) => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateShowedFloor = (params) =>{
        params.e.preventDefault();
        measuringContext.showedFloor[params.index].rooms[params.roomIndex][params.name] = params.e.target.value;
        setMeasuringContext({...measuringContext});
    }
    const addRoom = (params) => {
        params.e.preventDefault();
        let countRoom = measuringContext.showedFloor[params.index].rooms.length + 1;
        measuringContext.showedFloor[params.index].rooms.push({"name": "Помещение " + countRoom, "SQM": "", "MP": "", "corners": "", "profile": "", "typeMixture": "", "addWorks": [],});
        setMeasuringContext({...measuringContext});
    }
    const deleteRoom = (params) => {
        params.e.preventDefault();

        if (!confirm("Помещение будет удалено.\r\nВы уверены?")) return false;
        measuringContext.showedFloor[params.index].rooms.splice(params.roomIndex, 1);
        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow className="text-white">
            <MDBCol lg="2" sm='4' size="6" className='mt-4'>
                <MDBInput className="bg-success" label='Название помещения' value={props.room.name} onChange={e => updateShowedFloor({"e": e, "name": "name", "index": props.index, "roomIndex": props.roomIndex})} type='text' contrast required />
                <MDBIcon className="times-room" size='lg' fas icon="times" onClick={e => deleteRoom({"e": e, "index": props.index, "roomIndex": props.roomIndex})}/>
            </MDBCol>
            <MDBCol lg="1" sm='2' size="6" className='mt-4'>
                <MDBInput label='Уголки' contrast type='text' value={props.room.corners} onChange={e => updateShowedFloor({"e": e, "name": "corners", "index": props.index, "roomIndex": props.roomIndex})} required />
            </MDBCol>
            <MDBCol lg="1" sm='2' size="6" className='mt-4'>
                <MDBInput label='кв.м.' contrast type='text' value={props.room.SQM} onChange={e => updateShowedFloor({"e": e, "name": "SQM", "index": props.index, "roomIndex": props.roomIndex})} required />
            </MDBCol>
            <MDBCol lg="1" sm='2' size="6" className='mt-4'>
                <MDBInput label='Профиля' contrast type='text' value={props.room.profile} onChange={e => updateShowedFloor({"e": e, "name": "profile", "index": props.index, "roomIndex": props.roomIndex})} required />
            </MDBCol>
            <MDBCol lg="1" sm='2' size="6" className='mt-4'>
                <MDBInput label='м.п.' contrast type='text' value={props.room.MP} onChange={e => updateShowedFloor({"e": e, "name": "MP", "index": props.index, "roomIndex": props.roomIndex})} required />
            </MDBCol>
            <MDBCol lg="2" size="6" className='mt-4'>
                <select defaultValue={(measuringContext.showedFloor[props.index].rooms[props.roomIndex].typeMixture ? measuringContext.showedFloor[props.index].rooms[props.roomIndex].typeMixture : "1")} onChange={e => updateShowedFloor({"e": e, "name": "typeMixture", "index": props.index, "roomIndex": props.roomIndex})} className="select-css" required>
                    <option value="" disabled>Тип смеси</option>
                    {measuringContext.materials.map(material =>
                        <option value={material.id} key={material.id}>{material.name}</option>
                    )}
                </select>
            </MDBCol>
            <MDBCol lg="4" sm='6' size="12" className='mt-4'>
                <AddToWork index={props.index} roomIndex={props.roomIndex} label="Добавить работу в помещении" />
            </MDBCol>

            {props.room.addWorks.map((addWork, indexAddWork) =>
                <ToWork addWork={addWork} indexAddWork={indexAddWork} key="roomToWork" index={props.index} roomIndex={props.roomIndex} label="Название работы в помещении" />
            )}

            <div>
                {/*{props.roomIndex}*/}
                {/*{measuringContext.showedFloor[props.index].rooms.length - 1}*/}
                {/*{console.log(measuringContext.showedFloor)}*/}
            </div>

            <MDBCol size="12" className='mt-4 text-center'>
                {props.roomIndex == measuringContext.showedFloor[props.index].rooms.length - 1 &&
                    <MDBBtn className="shadow-0 rounded-0" onClick={e => addRoom({"e": e, "index": props.index})}
                            color='success'>
                        <MDBIcon fas icon="plus"/>
                    </MDBBtn>
                }
                <hr className="text-success w-75 mt-0 mx-auto" style={{
                    "opacity": 1,
                    "height": "2px"
                }}/>
            </MDBCol>
        </MDBRow>
    );
};

export default FloorData;