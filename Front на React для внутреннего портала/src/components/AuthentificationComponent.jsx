import React, {useContext, useState} from 'react';
import {MDBBtn, MDBCol, MDBContainer, MDBInput, MDBRow} from "mdb-react-ui-kit";
import '../styles/Authentification.css';
import 'animate.css';
import Authenticated from "../API/Authenticated";
import {useNavigate} from "react-router-dom";
import {MainContext} from "../context";

const AuthentificationComponent = ({changeAT}) => {
    const {mainContext, setMainContext} = useContext(MainContext);
    const [formData, setFormData] = useState({email: '', password: ''});
    const navigate = useNavigate();
    const setAT = (data) => {
        data.event.preventDefault();
        changeAT(data.at);
    }

    async function authenticated(e){
        e.preventDefault();

        mainContext.showLoad = true;
        setMainContext({...mainContext});

        const response = await Authenticated.login(formData);

        if(response && response.status == 'success'){
            mainContext.showLoad = false;
            setMainContext({...mainContext});

            localStorage.setItem('token', response.data.token);
            localStorage.setItem('role', response.data.role);
            navigate('/');
        } else {
            mainContext.info.info(response, "danger");
            return false;
        }
    }

    return (
        <MDBContainer id="AuthentificationComponent">
            <form
                className="mt-3 shadow-4 rounded p-4 p-sm-5 m-auto"
                onSubmit={e => authenticated(e)}
            >
                <MDBInput
                    className='mb-4'
                    type='email'
                    name="email"
                    label='Email'
                    required="required"
                    value={formData.email}
                    onChange={e => setFormData({...formData, email: e.target.value})}
                />
                <MDBInput
                    className='mb-4'
                    type='password'
                    name="password"
                    label='Пароль'
                    required="required"
                    value={formData.password}
                    onChange={e => setFormData({...formData, password: e.target.value})}
                />

                <MDBBtn type='submit' className='mb-4' color='dark' block>
                    Войти
                </MDBBtn>

                <MDBRow className='mb-4'>
                    <MDBCol className='d-flex justify-content-center'>
                        <a href='#!' className="text-dark" onClick={e => setAT({'event': e, 'at': 'RegistrationComponent'})}>Регистарация</a>
                    </MDBCol>
                    <MDBCol className="d-flex justify-content-center">
                        <a href='#!' className="text-dark" onClick={e => setAT({'event': e, 'at': 'ForgotPassword'})}>Забыли пароль?</a>
                    </MDBCol>
                </MDBRow>
            </form>
        </MDBContainer>
    );
};

export default AuthentificationComponent;