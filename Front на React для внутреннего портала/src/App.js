import React, {useState} from 'react';
import { Routes, Route } from "react-router-dom";
import Authentication from "./pages/Authentication";
import Menu from "./pages/Menu";
import Measuring from "./pages/Measuring";
import Test from "./pages/Test";
import {MainContext} from "./context";
import ModalMessageComponent from "./components/ModalMessageComponent";
import './styles/UIComponents.css'
import LoadComponent from "./components/LoadComponent";
import {useNavigate} from "react-router-dom";

function App() {
    const navigate = useNavigate();

    const info = (response, type) => {
        if(response.headers == 419){
            localStorage.clear();
            navigate('/authentication');
        }

        setShowMessageModal(true);
        mainContext.messageModal = true;
        mainContext.textMessageModal = response.data;
        mainContext.typeMessageModal = type;
        mainContext.showLoad = false;
        setMainContext({...mainContext});
    }

    const [mainContext, setMainContext] = useState({
        "messageModal": false,
        "textMessageModal": "",
        "typeMessageModal": "",
        "showLoad": false,
        "info": {info}
    });

    const [showMessageModal, setShowMessageModal] = useState(false);

  return (
      <>
          <MainContext.Provider value={{mainContext, setMainContext, showMessageModal, setShowMessageModal }}>
              <ModalMessageComponent />

              {mainContext.showLoad &&
                  <LoadComponent/>
              }

              <Routes>
                  <Route path="/" element={<Menu />} />
                  <Route path="/authentication" element={<Authentication />} />
                  <Route path="/measuring" element={<Measuring />} />
                  <Route path="/test" element={<Test />} />
              </Routes>
          </MainContext.Provider>
      </>
  );
}

export default App;
