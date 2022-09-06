import {useNavigate} from "react-router-dom";
import React, {useEffect} from 'react';

export const useIsToken = () =>{
    const navigate = useNavigate();

    useEffect(() => {
        if(!localStorage.getItem('token') || localStorage.getItem('token') == 'undefined') navigate('/authentication');
    }, []);
}