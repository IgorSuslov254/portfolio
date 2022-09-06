import React, {useContext} from 'react';
import {MDBBtn, MDBCol, MDBIcon, MDBRow, MDBTextArea, MDBInput} from "mdb-react-ui-kit";
import FloorData from "./floors_data/FloorData";
import ToWork from "./ToWork";
import {MeasuringContext} from "../../context";
import AddToWork from "./AddToWork";

const FloorsData = (props) => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const deleteFloor = (params) => {
        params.e.preventDefault();

        if (!confirm("Все данные об этаже буду удалены.\r\nВы уверены?")) return false;

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

    const updateCommentFloor = (params) => {
        params.e.preventDefault();
        measuringContext.showedFloor[params.index].comment = params.e.target.value;
        setMeasuringContext({...measuringContext});
    }

    return (
        <>
            <div className="mt-4 d-flex align-items-center justify-content-center">
                <h2 className="text-white d-inline-block me-2">{props.sf.name}</h2>
                <MDBInput style={{
                    width: "100px"
                }} onChange={e => {
                    e.preventDefault();
                    measuringContext.showedFloor[props.index].height = e.target.value;
                    setMeasuringContext({...measuringContext});
                }} value={measuringContext.showedFloor[props.index].height} label='Высота, м' type='text' contrast />
                <MDBBtn className="ms-2" size='sm' onClick={e => deleteFloor({"e": e, "index": props.index})} color='danger'>
                    <MDBIcon far icon="trash-alt" />
                </MDBBtn>
            </div>

            {props.sf.rooms.map((room, roomIndex) =>
                <FloorData room={room} roomIndex={roomIndex} index={props.index} key={roomIndex} />
            )}

            <MDBRow className="text-white">
                <MDBCol sm="6" size="12" className='mt-3'>
                    <MDBTextArea onChange={e => updateCommentFloor({"e": e, "index": props.index})} value={props.sf.comment} label='Комментарий к этажу' contrast />
                </MDBCol>
                <MDBCol className='mt-3'>
                    <AddToWork index={props.index} roomIndex={false} label="Добавить работу на этаж" />
                </MDBCol>
            </MDBRow>

            {props.sf.addWorks.map((addWorkFloor, indexAddWorkFloor) =>
                <ToWork addWork={addWorkFloor} indexAddWork={indexAddWorkFloor} key="floorToWork" index={props.index} roomIndex={false} label="Название работы на этаже" />
            )}

            <hr className="text-white mt-4" style={{
                "opacity": 1,
                "height": "2px"
            }}/>
        </>
    );
};

export default FloorsData;