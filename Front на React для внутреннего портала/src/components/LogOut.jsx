import React, {useContext} from 'react';
import {MDBBtn} from "mdb-react-ui-kit";
import {useNavigate} from "react-router-dom";
import Authenticated from "../API/Authenticated";
import {MainContext} from "../context";

const LogOut = () => {
    const {mainContext, setMainContext} = useContext(MainContext);
    const navigate = useNavigate();

    async function logOut(e){
        e.preventDefault();

        let newMainContext = {
            "showLoad": true
        }
        setMainContext({...newMainContext});

        await Authenticated.logOut();
        localStorage.clear();
        navigate('/authentication');

        setMainContext({...mainContext, "showLoad": false});
    }

    return (
        <div className="w-100 text-end">
            <MDBBtn className="text-dark" color='link' onClick={e => logOut(e)}>Выйти</MDBBtn>
        </div>
    );
};

export default LogOut;