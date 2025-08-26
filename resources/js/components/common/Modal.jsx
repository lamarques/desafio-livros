import React from 'react';

const Modal = ({ title, children, onClose }) => {
    return (
        <div className="modal">
            <div className="modal-content">
                <h2>{title}</h2>
                <div>{children}</div>
                <button onClick={onClose}>Close</button>
            </div>
        </div>
    );
};

export default Modal;
