import React, {useState} from 'react';
import { MDBContainer } from 'mdb-react-ui-kit';
import AuthentificationComponent from "../components/AuthentificationComponent";
import Logo from "../components/Logo";
import RegistrationComponent from "../components/RegistrationComponent";
import ForgotPassword from "../components/ForgotPassword";

const Authentication = () => {
    const [authentificationType, setAuthentificationType] = useState('AuthentificationComponent');

    const changeAT = (at) => {
        setAuthentificationType(at);
    }

    return (
        <MDBContainer fluid>
            <Logo className="mt-4 bg-dark rounded shadow-4"/>
            {authentificationType == 'AuthentificationComponent' &&
                <AuthentificationComponent changeAT={changeAT}/>
            }
            {authentificationType == 'RegistrationComponent' &&
                <RegistrationComponent changeAT={changeAT}/>
            }
            {authentificationType == 'ForgotPassword' &&
                <ForgotPassword changeAT={changeAT}/>
            }
        </MDBContainer>
    );
};

export default Authentication;