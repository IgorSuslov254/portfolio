import React, {useContext, useState} from 'react';
import {MDBBtn, MDBIcon, MDBModal, MDBModalBody, MDBModalContent, MDBModalDialog} from "mdb-react-ui-kit";
import InputMask from "react-input-mask";
import {MainContext} from "../context";

const ModalMessageComponent = () => {
    const {showMessageModal, setShowMessageModal, mainContext} = useContext(MainContext);

    return (
        <MDBModal
            animationDirection='bottom'
            show={showMessageModal}
            setShow={setShowMessageModal}
            tabIndex='-1'
        >
            <MDBModalDialog className="mw-100 m-0">
                <MDBModalContent className={"bg-" +mainContext.typeMessageModal+ " text-white"}>
                    <MDBModalBody className='py-1'>
                        <div className='d-flex justify-content-center align-items-center my-3'>
                            <p className='mb-0'>{mainContext.textMessageModal}</p>
                            <MDBBtn color='light' size='sm' className='ms-2 text-dark' onClick={e => setShowMessageModal(false)}>
                                Понятно
                            </MDBBtn>
                        </div>
                    </MDBModalBody>
                </MDBModalContent>
            </MDBModalDialog>
        </MDBModal>
    );
};

export default ModalMessageComponent;