import React, {useContext} from 'react';
import {MDBCol, MDBBtn, MDBIcon} from "mdb-react-ui-kit";
import TitleObjectData from "./TitleObjectData";
import ToWork from "../ToWork";
import ChoiceObjectData from "./ChoiceObjectData";
import {MeasuringContext} from "../../../context";

const ChildrenObjectData = () => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateShowObjectData = (e) => {
        e.preventDefault();

        if(measuringContext.showObjectData){
            measuringContext.showObjectData = false;
        } else {
            measuringContext.showObjectData = true;
        }

        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBCol size="12">
            <h2 className="text-white text-center mt-5">
                Данные Объекта
                <MDBBtn onClick={e => updateShowObjectData(e)} className='ms-3' size='sm' color='light'>
                    {measuringContext.showObjectData
                        ? <MDBIcon fas icon="angle-up" />
                        : <MDBIcon fas icon="angle-down" />
                    }
                </MDBBtn>
            </h2>

            {measuringContext.showObjectData &&
                <div>
                    <TitleObjectData />

                    {measuringContext.toWorkObject.map((WorkObject, indexWorkObject) =>
                        <ToWork addWork={WorkObject} indexAddWork={indexWorkObject} key="objectToWork" index={false} roomIndex={false} label="Название работы объекта" />
                    )}

                    <ChoiceObjectData />
                </div>
            }
        </MDBCol>
    );
};

export default ChildrenObjectData;