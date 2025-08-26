import React, { useEffect, useState } from 'react';
import api from '../../api';

const AuthorList = () => {
    const [authors, setAuthors] = useState([]);

    useEffect(() => {
        buscarAutores();
    }, []);

    const buscarAutores = () => {
        api.get('/autor')
            .then((response) => setAuthors(response.data.data))
            .catch((error) => console.error('Erro ao buscar autores:', error));
    };

    return (
        <div className="container mt-4">
            <h2 className="mb-4">Autores</h2>
            <ul className="list-group">
                {authors.map((author) => (
                    <li key={author.CodAu} className="list-group-item">
                        {author.Nome}
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default AuthorList;
