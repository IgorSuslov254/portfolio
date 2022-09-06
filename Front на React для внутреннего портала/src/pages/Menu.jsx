import React, {useState} from 'react';
import {useIsToken} from "../hooks/useIsToken";
import {MDBBtn, MDBContainer, MDBRow, MDBCol} from 'mdb-react-ui-kit';
import '../styles/Menu.css';
import {useNavigate} from "react-router-dom";
import Navbar from "../components/navbar";
import ModalSearchComponent from "../components/ModalSearchComponent";

const Menu = () => {
    useIsToken();

    const navigate = useNavigate();
    const [showSearchModal, setShowSearchModal] = useState(false);
    const [dataMenu, setDataMenu] = useState([]);
    const [showMenu, setShowMenu] = useState(true);

    const findMeasuring = (e) => {
        e.preventDefault();
        setShowSearchModal(true);
    }
    const measuring = (e) => {
        e.preventDefault();
        navigate('/measuring');
    }
    const goToMeasuring = (papams) => {
        papams.e.preventDefault();
        localStorage.setItem('measuring', papams.measuring);
        navigate('/measuring');
    }

    return (
        <>
            <Navbar active="menu" />
            <MDBContainer fluid>
                <MDBContainer id="menu">
                    {showMenu ?
                        <MDBRow>
                            <MDBCol size="12" className="text-center">
                                <MDBBtn className="py-3 mt-5"
                                        color='dark' block onClick={e => measuring(e)}>
                                    Произвести замер
                                </MDBBtn>
                            </MDBCol>
                            <MDBCol size="12" className="text-center">
                                <MDBBtn className="py-3 mt-3"
                                        color='dark' block onClick={e => findMeasuring(e)}>
                                    Найти замер
                                </MDBBtn>
                            </MDBCol>
                        </MDBRow>
                    :
                        <MDBRow>
                            {dataMenu.data.map((data, dataIndex) =>
                                <MDBCol size="12" className="text-center" key={data.buildingObject.id}>
                                    <MDBBtn
                                        className="py-3 mt-3"
                                        color='dark'
                                        block
                                        onClick={e => goToMeasuring({"e": e, "measuring": data.buildingObject.measuring.data_json})}
                                    >
                                        {data.buildingObject.phone + " " + data.buildingObject.address + " " + new Date(data.buildingObject.created_at).toLocaleString() }
                                    </MDBBtn>
                                </MDBCol>
                            )}
                        </MDBRow>
                    }
                </MDBContainer>
            </MDBContainer>
            <ModalSearchComponent
                showSearchModal={showSearchModal}
                setShowSearchModal={setShowSearchModal}
                setDataMenu={setDataMenu}
                setShowMenu={setShowMenu}
            />
        </>
    );
};

export default Menu;