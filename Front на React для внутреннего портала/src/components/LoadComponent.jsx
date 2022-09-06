import React from 'react';
import { MDBIcon } from 'mdb-react-ui-kit';

const LoadComponent = () => {
    return (
        <div className="position-fixed d-flex align-items-center justify-content-center h-100 w-100 bg-white" style={{"zIndex": "1010"}}>
            <div className="animate__animated animate__rotateOut animate__infinite">
                <MDBIcon size='4x' fas icon="spinner" />
            </div>
        </div>
    );
};

export default LoadComponent;