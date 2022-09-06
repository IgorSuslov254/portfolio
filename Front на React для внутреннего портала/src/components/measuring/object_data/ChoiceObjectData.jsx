import React, {useContext} from 'react';
import {MDBCol, MDBInput, MDBRadio, MDBRow, MDBTypography, MDBCheckbox} from "mdb-react-ui-kit";
import {MeasuringContext} from "../../../context";

const ChoiceObjectData = () => {
    const {measuringContext, setMeasuringContext} = useContext(MeasuringContext); //глобальные данные

    const updateDataObject = (params) => {
        if (params.name == "carryingMore"){
            measuringContext.dataObject[params.name] = !measuringContext.dataObject[params.name];
        } else if (params.name == "fullGeometry" || params.name == "subcontracting" || params.name == "oldFoundation") {
            measuringContext.dataObject.segmentationMultiChoice[params.name] = !measuringContext.dataObject.segmentationMultiChoice[params.name];
        } else if (params.name == "concreteContact.km") {
            measuringContext.dataObject.concreteContact.km = params.e.target.value;
        } else if (params.name == "concreteContact.pm") {
            measuringContext.dataObject.concreteContact.pm = params.e.target.value;
        } else if (params.name == "concreteContact.percent") {
            measuringContext.dataObject.concreteContact.percent = params.e.target.value;
        } else if (params.name == "gasBlock.km") {
            measuringContext.dataObject.gasBlock.km = params.e.target.value;
        } else if (params.name == "gasBlock.pm") {
            measuringContext.dataObject.gasBlock.pm = params.e.target.value;
        } else if (params.name == "gasBlock.percent") {
            measuringContext.dataObject.gasBlock.percent = params.e.target.value;
        } else{
            measuringContext.dataObject[params.name] = params.e.target.value;
        }

        setMeasuringContext({...measuringContext});
    }

    return (
        <MDBRow className="mt-5 text-white" id="ChoiceObjectData">
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>380</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.threeHundredEighty == '1'} onChange={e => updateDataObject({"e": e, "name": "threeHundredEighty"})} name="380" value='1' label='Да' />
                        <MDBRadio checked={measuringContext.dataObject.threeHundredEighty == '0'} onChange={e => updateDataObject({"e": e, "name": "threeHundredEighty"})} name="380" value='0' label='Нет' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Слоя</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.layer == '1'} onChange={e => updateDataObject({"e": e, "name": "layer"})} name='layer' value='1' label='Да' />
                        <MDBRadio checked={measuringContext.dataObject.layer == '0'} onChange={e => updateDataObject({"e": e, "name": "layer"})} name='layer' value='0' label='Нет' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Медиа</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.media == '1'} onChange={e => updateDataObject({"e": e, "name": "media"})} name='media' value='1' label='Фото Фасада' />
                        <MDBRadio checked={measuringContext.dataObject.media == '0'} onChange={e => updateDataObject({"e": e, "name": "media"})} name='media' value='0' label='Видео' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Вода</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.water == '1'} onChange={e => updateDataObject({"e": e, "name": "water"})} name='water' value='1' label='Автоматика' />
                        <MDBRadio checked={measuringContext.dataObject.water == '0'} onChange={e => updateDataObject({"e": e, "name": "water"})} name='water' value='0' label='Бочка' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Замывка</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.washing == '1'} onChange={e => updateDataObject({"e": e, "name": "washing"})} name='washing' value='1' label='Да' />
                        <MDBRadio checked={measuringContext.dataObject.washing == '0'} onChange={e => updateDataObject({"e": e, "name": "washing"})} name='washing' value='0' label='Нет' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Электрика</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.electrics == '1'} onChange={e => updateDataObject({"e": e, "name": "electrics"})} name='electrics' value='1' label='Да' />
                        <MDBRadio checked={measuringContext.dataObject.electrics == '0'} onChange={e => updateDataObject({"e": e, "name": "electrics"})} name='electrics' value='0' label='Нет' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Занос материала</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.skidding == '0'} onChange={e => updateDataObject({"e": e, "name": "skidding"})} name='skidding' value='0' label='Дом' />
                        <MDBRadio checked={measuringContext.dataObject.skidding == '1'} onChange={e => updateDataObject({"e": e, "name": "skidding"})} name='skidding' value='1' label='Лифт' />
                        <MDBRadio checked={measuringContext.dataObject.skidding == '2'} onChange={e => updateDataObject({"e": e, "name": "skidding"})} name='skidding' value='2' label='Лифт 2 этаж' />
                        <MDBCheckbox onChange={e => updateDataObject({"e": e, "name": "carryingMore"})} name='carryingMore' label='Пронос > 20м.' defaultChecked={measuringContext.dataObject.carryingMore} />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Сегментация</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBRadio checked={measuringContext.dataObject.segmentation == '0'} onChange={e => updateDataObject({"e": e, "name": "segmentation"})} name='segmentation' value='0' label='Застройщик' />
                        <MDBRadio checked={measuringContext.dataObject.segmentation == '1'} onChange={e => updateDataObject({"e": e, "name": "segmentation"})} name='segmentation' value='1' label='Эконом' />
                        <MDBRadio checked={measuringContext.dataObject.segmentation == '2'} onChange={e => updateDataObject({"e": e, "name": "segmentation"})} name='segmentation' value='2' label='Стандарт' />
                        <MDBRadio checked={measuringContext.dataObject.segmentation == '3'} onChange={e => updateDataObject({"e": e, "name": "segmentation"})} name='segmentation' value='3' label='Премиум' />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="6">
                        <MDBTypography variant='h4'>Сегментация</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="6">
                        <MDBCheckbox className="me-0" onChange={e => updateDataObject({"e": e, "name": "fullGeometry"})} name='fullGeometry' label='Полная геометрия' defaultChecked={measuringContext.dataObject.segmentationMultiChoice.fullGeometry} />
                        <MDBCheckbox onChange={e => updateDataObject({"e": e, "name": "subcontracting"})} name='subcontracting' label='Субподряд' defaultChecked={measuringContext.dataObject.segmentationMultiChoice.subcontracting} />
                        <MDBCheckbox onChange={e => updateDataObject({"e": e, "name": "oldFoundation"})} name='oldFoundation' label='Старый фонд' defaultChecked={measuringContext.dataObject.segmentationMultiChoice.oldFoundation} />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 my-md-4">
                <MDBInput label='До объекта, км' value={measuringContext.dataObject.km} onChange={e => updateDataObject({"e": e, "name": "km"})} type='text' contrast />
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 my-md-4">
                <MDBInput label='Кабель, м' value={measuringContext.dataObject.cable} onChange={e => updateDataObject({"e": e, "name": "cable"})} type='text' contrast />
            </MDBCol>
            <MDBCol lg="4" md="6" className="mt-2 my-md-4">
                <MDBInput label='Шланг, м' value={measuringContext.dataObject.tube} onChange={e => updateDataObject({"e": e, "name": "tube"})} type='text' contrast />
            </MDBCol>
            <MDBCol lg="6" md="12" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="5">
                        <MDBTypography variant='h4'>Количество бетоноконтакта</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="2">
                        <MDBInput disabled={measuringContext.dataObject.concreteContact.percent} label='М.кв.' value={measuringContext.dataObject.concreteContact.km} onChange={e => updateDataObject({"e": e, "name": "concreteContact.km"})} type='text' contrast />
                    </MDBCol>
                    <MDBCol center size="2">
                        <MDBInput disabled={measuringContext.dataObject.concreteContact.percent} label='М.п.' value={measuringContext.dataObject.concreteContact.pm} onChange={e => updateDataObject({"e": e, "name": "concreteContact.pm"})} type='text' contrast />
                    </MDBCol>
                    <MDBCol center size="3">
                        <MDBInput disabled={measuringContext.dataObject.concreteContact.km || measuringContext.dataObject.concreteContact.pm} label='процент' value={measuringContext.dataObject.concreteContact.percent} onChange={e => updateDataObject({"e": e, "name": "concreteContact.percent"})} type='text' contrast />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
            <MDBCol lg="6" md="12" className="mt-2 border border-white">
                <MDBRow>
                    <MDBCol center size="5">
                        <MDBTypography variant='h4'>Количество газоблока</MDBTypography>
                    </MDBCol>
                    <MDBCol center size="2">
                        <MDBInput disabled={measuringContext.dataObject.gasBlock.percent} label='М.кв.' value={measuringContext.dataObject.gasBlock.km} onChange={e => updateDataObject({"e": e, "name": "gasBlock.km"})} type='text' contrast />
                    </MDBCol>
                    <MDBCol center size="2">
                        <MDBInput disabled={measuringContext.dataObject.gasBlock.percent} label='М.п.' value={measuringContext.dataObject.gasBlock.pm} onChange={e => updateDataObject({"e": e, "name": "gasBlock.pm"})} type='text' contrast />
                    </MDBCol>
                    <MDBCol center size="3">
                        <MDBInput disabled={measuringContext.dataObject.gasBlock.km || measuringContext.dataObject.gasBlock.pm} label='процент' value={measuringContext.dataObject.gasBlock.percent} onChange={e => updateDataObject({"e": e, "name": "gasBlock.percent"})} type='text' contrast />
                    </MDBCol>
                </MDBRow>
            </MDBCol>
        </MDBRow>
    );
};

export default ChoiceObjectData;