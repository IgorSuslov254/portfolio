import React, {useContext, useState} from 'react';
import {
    MDBContainer,
    MDBNavbar,
    MDBNavbarBrand,
    MDBNavbarToggler,
    MDBNavbarNav,
    MDBNavbarItem,
    MDBNavbarLink,
    MDBCollapse,
    MDBIcon,
    MDBBtn
} from 'mdb-react-ui-kit';
import {MainContext} from "../context";
import {useNavigate, Link} from "react-router-dom";
import Authenticated from "../API/Authenticated";

const Navbar = (props) => {
    const {mainContext, setMainContext} = useContext(MainContext);
    const navigate = useNavigate();

    const [showNav, setShowNav] = useState(false);

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
        <MDBNavbar expand='sm' dark bgColor='dark'>
            <MDBContainer>
                <MDBNavbarBrand href='./'>
                    <svg width="174" height="47" viewBox="0 0 174 47" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16.1545 25.7503L13.8601 28.1165V32.385H9.92683V15.4502H13.8601V23.3377L21.4457 15.4502H25.8472L18.7298 23.0129L26.2218 32.385H21.5861L16.1545 25.7503Z"
                            fill="white"></path>
                        <path
                            d="M38.8643 32.385L35.5866 27.6526H35.3993H31.9343V32.385H28.001V15.4502H35.3993C39.9881 15.4502 42.8444 17.8164 42.8444 21.5746C42.8444 24.1264 41.5333 25.9823 39.3326 26.9102L43.1722 32.3386H38.8643V32.385ZM35.1652 18.6516H31.9343V24.544H35.1652C37.6001 24.544 38.8175 23.4305 38.8175 21.5746C38.7707 19.7187 37.5533 18.6516 35.1652 18.6516Z"
                            fill="white"></path>
                        <path
                            d="M47.7142 32.385H43.6405L53.2395 15.4502L62.8386 32.385H58.6712L53.1459 22.7345L47.7142 32.385Z"
                            fill="white"></path>
                        <path
                            d="M68.5511 18.6052V23.0593H76.4645V26.2143H68.5511V32.385H64.6179V15.4502H77.5414V18.6052H68.5511V18.6052Z"
                            fill="white"></path>
                        <path d="M83.8628 18.6516H78.3843V15.4502H93.2745V18.6516H87.7961V32.385H83.8628V18.6516V18.6516Z"
                              fill="white"></path>
                        <path
                            d="M103.95 15.4502V32.385H102.92V24.312H93.5086V32.385H92.4785V15.4502H93.5086V23.2449H102.92V15.4502H103.95Z"
                            fill="white"></path>
                        <path
                            d="M107.509 23.9408C107.509 19.0228 110.6 15.3574 114.814 15.3574C118.981 15.3574 122.118 19.0228 122.118 23.9408C122.118 28.8589 118.981 32.5243 114.814 32.5243C110.646 32.5243 107.509 28.8125 107.509 23.9408ZM121.088 23.9408C121.088 19.6723 118.419 16.4709 114.814 16.4709C111.208 16.4709 108.539 19.6259 108.539 23.9408C108.539 28.2093 111.208 31.4107 114.814 31.4107C118.419 31.3643 121.088 28.2093 121.088 23.9408Z"
                            fill="white"></path>
                        <path
                            d="M125.583 25.1935V15.4502H126.614V25.1471C126.614 29.3692 128.299 31.3643 131.249 31.3643C134.199 31.3643 135.838 29.3692 135.838 25.1471V15.4502H136.868V25.1935C136.868 30.0188 134.714 32.4778 131.249 32.4778C127.784 32.4778 125.583 30.0188 125.583 25.1935Z"
                            fill="white"></path>
                        <path
                            d="M140.193 30.2044L140.661 29.2765C141.644 30.4828 143.424 31.4107 145.297 31.4107C148.106 31.4107 149.37 29.926 149.37 28.1166C149.37 23.0593 140.614 26.0751 140.614 19.8579C140.614 17.4453 142.159 15.3574 145.578 15.3574C147.123 15.3574 148.715 15.9606 149.792 16.8885L149.417 17.8629C148.247 16.8885 146.842 16.4246 145.578 16.4246C142.862 16.4246 141.644 17.9556 141.644 19.8115C141.644 24.8688 150.4 21.8994 150.4 28.0238C150.4 30.4364 148.808 32.4779 145.343 32.4779C143.236 32.5243 141.223 31.5499 140.193 30.2044Z"
                            fill="white"></path>
                        <path
                            d="M163.558 31.2715V32.385H153.865V15.4502H163.277V16.5637H154.896V23.2449H162.387V24.312H154.896V31.2715H163.558V31.2715Z"
                            fill="white"></path>
                        <path d="M47.1991 24.8687H45.092V29.9723H47.1991V24.8687Z" fill="white"></path>
                        <path d="M58.4838 30.7149H51.4133L52.5839 28.395H58.4838V30.7149Z" fill="white"></path>
                    </svg>
                </MDBNavbarBrand>
                <MDBNavbarToggler
                    type='button'
                    aria-expanded='false'
                    aria-label='Toggle navigation'
                    onClick={() => setShowNav(!showNav)}
                >
                    <MDBIcon icon='bars' fas />
                </MDBNavbarToggler>
                <MDBCollapse navbar show={showNav}>
                    <ul className="navbar-nav w-100">
                        <li className="nav-item">
                            <Link
                                data-test="nav-link"
                                className={"nav-link " + (props.active == "menu" ? "active" : "")}
                                aria-current="page"
                                to="/">
                                    Меню
                            </Link>
                        </li>
                    </ul>
                    <MDBBtn className="logOut" color='white' onClick={e => logOut(e)}>Выйти</MDBBtn>
                </MDBCollapse>
            </MDBContainer>
        </MDBNavbar>
    );
};

export default Navbar;