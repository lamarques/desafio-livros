import React, { useState, useEffect } from 'react';

const ReportMenu = () => {
    const [filters, setFilters] = useState({
        q: '',
        ano_ini: '',
        ano_fim: '',
        order: 'AnoPublicacao',
        per_page: 50,
        valor_min: '',
        valor_max: ''
    });
    const [results, setResults] = useState([]);
    const [data, setData] = useState([]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFilters({ ...filters, [name]: value });
    };

    const generateReport = async () => {
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const response = await fetch(`/api/relatorios/livros?${queryParams}`);
            if (response.ok) {
                const data = await response.json();
                setResults(data);
                setData(data.data);
            } else {
                console.error('Failed to fetch report data');
            }
        } catch (error) {
            console.error('Error fetching report data:', error);
        }
    };

    const exportCSV = () => {
        const queryParams = new URLSearchParams(filters).toString();
        window.open(`/api/relatorios/livros.csv?${queryParams}`, '_blank');
    };

    return (
        <div className="container">
            <h1>Relatórios de Livros</h1>

            <form className="mb-4">
                <div className="row">
                    <div className="col-md-6">
                        <label htmlFor="q">Busca</label>
                        <input type="text" id="q" name="q" className="form-control" placeholder="Autor ou título" value={filters.q} onChange={handleChange} />
                    </div>
                    <div className="col-md-3">
                        <label htmlFor="ano_ini">Ano Inicial</label>
                        <input type="number" id="ano_ini" name="ano_ini" className="form-control" value={filters.ano_ini} onChange={handleChange} />
                    </div>
                    <div className="col-md-3">
                        <label htmlFor="ano_fim">Ano Final</label>
                        <input type="number" id="ano_fim" name="ano_fim" className="form-control" value={filters.ano_fim} onChange={handleChange} />
                    </div>
                </div>

                <div className="row mt-3">
                    <div className="col-md-4">
                        <label htmlFor="order">Ordenar por</label>
                        <select id="order" name="order" className="form-control" value={filters.order} onChange={handleChange}>
                            <option value="AnoPublicacao">Ano de Publicação</option>
                            <option value="Titulo">Título</option>
                            <option value="Autor">Autor</option>
                        </select>
                    </div>
                    <div className="col-md-4">
                        <label htmlFor="per_page">Itens por página</label>
                        <input type="number" id="per_page" name="per_page" className="form-control" value={filters.per_page} onChange={handleChange} />
                    </div>
                    <div className="col-md-2">
                        <label htmlFor="valor_min">Valor Mínimo</label>
                        <input type="number" id="valor_min" name="valor_min" className="form-control" step="0.01" value={filters.valor_min} onChange={handleChange} />
                    </div>
                    <div className="col-md-2">
                        <label htmlFor="valor_max">Valor Máximo</label>
                        <input type="number" id="valor_max" name="valor_max" className="form-control" step="0.01" value={filters.valor_max} onChange={handleChange} />
                    </div>
                </div>

                <div className="mt-4">
                    <button type="button" className="btn btn-primary" onClick={generateReport}>Gerar Relatório</button>
                    <button type="button" className="btn btn-secondary" onClick={exportCSV}>Exportar CSV</button>
                </div>
            </form>

            <div id="report-results">
                <table className="table table-striped mt-4">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Edição</th>
                            <th>Editora</th>
                            <th>Ano de Publicação</th>
                            <th>Valor</th>
                            <th>Autores</th>
                            <th>Assuntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.length > 0 ? (
                            data.map((item, index) => (
                                <tr key={index}>
                                    <td>{index + 1}</td>
                                    <td>{item.Titulo}</td>
                                    <td>{item.Edicao}</td>
                                    <td>{item.Editora}</td>
                                    <td>{item.AnoPublicacao}</td>
                                    <td>{item.Valor}</td>
                                    <td>{item.Autores}</td>
                                    <td>{item.Assuntos}</td>
                                </tr>
                            ))
                        ) : (
                            <tr>
                                <td colSpan="8" className="text-center">Nenhum resultado encontrado.</td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default ReportMenu;
