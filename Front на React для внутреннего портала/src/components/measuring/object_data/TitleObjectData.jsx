import React, {useContext} from 'react';
import {MDBCol, MDBRow, MDBTextArea} from "mdb-react-ui-kit";
import AddToWork from "../AddToWork";
import {MeasuringContext} from "../../../context";

const TitleObjectData = () => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateCommentObject = (params) => {
        params.e.preventDefault();
        measuringContext.commentObject = params.e.target.value;
        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow>
            <MDBCol sm="6" className="mt-2">
                <MDBTextArea onChange={e => updateCommentObject({"e": e})} value={measuringContext.commentObject} label='Комментарий к объекту' className="pt-2" contrast />
            </MDBCol>
            <MDBCol sm="6" className="mt-2">
                <AddToWork index={false} roomIndex={false} label="Добавить работу к объекту" />
            </MDBCol>
        </MDBRow>
    );
};

export default TitleObjectData;