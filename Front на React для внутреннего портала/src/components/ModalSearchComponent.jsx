import React, {useContext, useState} from 'react';
import {MDBCheckbox, MDBRow, MDBBtn, MDBModal, MDBModalBody, MDBModalContent, MDBModalDialog, MDBCol, MDBIcon, MDBPopover, MDBPopoverBody, MDBPopoverHeader} from "mdb-react-ui-kit";
import InputMask from "react-input-mask";
import MeasuringAPI from "../API/MeasuringAPI";
import {MainContext} from "../context";
import DatePicker, {registerLocale} from "react-datepicker";
import "../styles/Datepicker.css";
import "react-datepicker/dist/react-datepicker.css";
import ru from 'date-fns/locale/ru';
import { format } from 'date-fns'
registerLocale('ru', ru);

const ModalSearchComponent = (props) => {
    const [searchValue, setSearchValue] = useState("");
    const {mainContext, setMainContext} = useContext(MainContext); //глобальные данные
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [checkBox, setCheckBox] = useState(false);

    const hideSearch = (e) => {
        e.preventDefault();
        props.setShowSearchModal(false);
    }
    const searchEstimate = async (e) => {
        e.preventDefault();

        let data = {
            "phone": searchValue,
            "startDate": (startDate ? format(startDate, 'yyyy-MM-dd') : null ),
            "endDate": (endDate ? format(endDate, 'yyyy-MM-dd') : null ),
            "checkBox": checkBox
        }

        props.setShowSearchModal(false);

        mainContext.showLoad = true;
        setMainContext({...mainContext});

        const response = await MeasuringAPI.searchEstimate(data);

        if(response.status == "error") {
            mainContext.info.info(response, "danger");
            return false;
        }

        props.setDataMenu(response);
        props.setShowMenu(false);

        mainContext.showLoad = false;
        setMainContext({...mainContext});
    }

    return (
        <MDBModal
            animationDirection='bottom'
            show={props.showSearchModal}
            setShow={props.setShowSearchModal}
            tabIndex='-1'
            id="ModalSearchComponent"
        >
            <MDBModalDialog className="mw-100 m-0">
                <MDBModalContent className="bg-dark text-white">
                    <MDBModalBody className='py-1'>
                        <form className="py-3 row" onSubmit={e => searchEstimate(e)}>
                            <MDBCol size='12' sm='6' className="mt-2">
                                <DatePicker
                                    locale="ru"
                                    selected={startDate}
                                    onChange={(date) => setStartDate(date)}
                                    selectsStart
                                    startDate={startDate}
                                    endDate={endDate}
                                    dateFormat="dd-MM-yyyy"
                                    placeholderText="Дата начала периода"
                                    className="my-date-picker"
                                />
                            </MDBCol>
                            <MDBCol size='12' sm='6' className="mt-2">
                                <DatePicker
                                    locale="ru"
                                    selected={endDate}
                                    onChange={(date) => setEndDate(date)}
                                    selectsEnd
                                    startDate={startDate}
                                    endDate={endDate}
                                    minDate={startDate}
                                    dateFormat="dd-MM-yyyy"
                                    placeholderText="Дата конца периода"
                                    className="my-date-picker"
                                />
                            </MDBCol>
                            <MDBCol size='12' sm='6' className="mt-2">
                                <div className="form-outline form-white w-100">
                                    <InputMask value={searchValue} onChange={e => setSearchValue(e.target.value)} id="searchPhone" className={"form-control" + (searchValue ? ' active' : '')} mask="+7\(999)999-99-99"></InputMask>
                                    <label className="form-label" htmlFor="searchPhone">Номер телефона</label>
                                    <div className="form-notch">
                                        <div className="form-notch-leading"></div>
                                        <div className="form-notch-middle" style={{width: "110px"}}></div>
                                        <div className="form-notch-trailing"></div>
                                    </div>
                                </div>
                            </MDBCol>
                            <MDBCol size='12' sm='6' className="mt-2">
                                <MDBRow className="align-items-center">
                                    <MDBCol size="12" xl="5" >
                                        <MDBCheckbox defaultChecked={checkBox} onChange={() => setCheckBox(!checkBox)} label='Необработанные замеры'/>
                                    </MDBCol>
                                    <MDBCol size="12" xl="7" className="text-xl-end text-center mt-2 mt-xl-0">
                                        <MDBPopover type="button" color='white' btnChildren={<MDBIcon fas icon="question" />}>
                                            <MDBPopoverHeader>Правила поиска</MDBPopoverHeader>
                                            <MDBPopoverBody>Поиск имеет аккумулирующий эффект. Т.е. при выборе периода, указании номера телефона и установки галочки "Необработанные замеры", будет произведён поиск необработанных замеров в указанный промежуток времени по конкретному номеру телефона. Если же установить только галочку "Необработанные замеры", то будет произведён поиск всех необработанных замеров за весь период времени. Удачи! </MDBPopoverBody>
                                        </MDBPopover>
                                        <MDBBtn className='ms-2 text-dark' color='light' tag='input' type='submit' value='Найти' />
                                        <MDBBtn className='ms-2 text-dark' color='light' onClick={e => hideSearch(e)}>
                                            <MDBIcon fas icon="times" />
                                        </MDBBtn>
                                    </MDBCol>
                                </MDBRow>
                            </MDBCol>
                        </form>
                    </MDBModalBody>
                </MDBModalContent>
            </MDBModalDialog>
        </MDBModal>
    )
};

export default ModalSearchComponent;