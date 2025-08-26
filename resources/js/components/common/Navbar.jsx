import React from 'react';

const Navbar = () => {
     return (
        <nav className="navbar navbar-expand-lg navbar-light bg-light">
            <div className="container-fluid">
                <a className="navbar-brand" href="/">Cadastro de Livros</a>
                <div className="collapse navbar-collapse">
                    <ul className="navbar-nav me-auto mb-2 mb-lg-0">
                        <li className="nav-item">
                            <a className="nav-link" href="/books">Livros</a>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link" href="/authors">Autores</a>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link" href="/subjects">Assuntos</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
