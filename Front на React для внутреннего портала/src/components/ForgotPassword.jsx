import React, {useState} from 'react';
import {MDBBtn, MDBCol, MDBContainer, MDBInput, MDBRow} from "mdb-react-ui-kit";
import '../styles/Authentification.css';
import 'animate.css';

const ForgotPassword = ({changeAT}) => {
    const [formData, setFormData] = useState({email: ''});

    const setAT = (data) => {
        data.event.preventDefault();
        changeAT(data.at);
    }

    return (
        <MDBContainer id="ForgotPassword">
            <form className="mt-3 shadow-4 rounded p-5 m-auto">
                <MDBInput
                    className='mb-4'
                    type='email'
                    name="email"
                    label='Email'
                    required="required"
                    value={formData.email}
                    onChange={e => setFormData({...formData, email: e.target.value})}
                />

                <MDBBtn type='submit' className='mb-4' color='dark' block>
                    Восстановить пароль
                </MDBBtn>

                <MDBRow className='mb-4'>
                    <MDBCol className='d-flex justify-content-center'>
                        <a href='#!' className="text-dark" onClick={e => setAT({'event': e, 'at': 'RegistrationComponent'})}>Регистрация</a>
                    </MDBCol>
                    <MDBCol className="d-flex justify-content-center">
                        <a href='#!' className="text-dark" onClick={e => setAT({'event': e, 'at': 'AuthentificationComponent'})}>Форма входа</a>
                    </MDBCol>
                </MDBRow>
            </form>
        </MDBContainer>
    );
};

export default ForgotPassword;