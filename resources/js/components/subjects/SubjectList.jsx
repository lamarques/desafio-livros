import React, { useEffect, useState } from 'react';
import api from '../../api';

const SubjectList = () => {
    const [subjects, setSubjects] = useState([]);
    const [showForm, setShowForm] = useState(false);
    const [newSubjectName, setNewSubjectName] = useState('');
    const [mensagemFeedback, setMensagemFeedback] = useState('');
    const [editingSubjectId, setEditingSubjectId] = useState(null);

    useEffect(() => {
        buscarAssuntos();
    }, []);

    const buscarAssuntos = () => {
        api.get('/assunto')
            .then((response) => setSubjects(response.data.data))
            .catch((error) => console.error('Erro ao buscar assuntos:', error));
    };

    const handleNovoAssunto = () => {
        setNewSubjectName('');
        setEditingSubjectId(null);
        setShowForm(true);
    };

    const handleSalvarAssunto = (e) => {
        e.preventDefault();
        if (editingSubjectId) {
            // Enviar PUT para edição
            api.put(`/assunto/${editingSubjectId}`, { Descricao: newSubjectName })
                .then((response) => {
                    setMensagemFeedback(response.data.message || 'Assunto atualizado com sucesso!');
                    setNewSubjectName('');
                    setEditingSubjectId(null);
                    setShowForm(false);
                    buscarAssuntos();
                })
                .catch((error) => {
                    console.error('Erro ao atualizar assunto:', error);
                    setMensagemFeedback('Falha ao atualizar o assunto.');
                });
        } else {
            // Enviar POST para criação
            api.post('/assunto', { Descricao: newSubjectName })
                .then((response) => {
                    setMensagemFeedback(response.data.message || 'Assunto adicionado com sucesso!');
                    setNewSubjectName('');
                    setShowForm(false);
                    buscarAssuntos();
                })
                .catch((error) => {
                    console.error('Erro ao adicionar assunto:', error);
                    setMensagemFeedback('Falha ao adicionar o assunto.');
                });
        }
    };

    const handleEditarAssunto = (subject) => {
        setNewSubjectName(subject.Descricao);
        setEditingSubjectId(subject.CodAs);
        setShowForm(true);
    };

    const handleDeletarAssunto = (subjectId) => {
        if (window.confirm('Tem certeza que deseja deletar este assunto?')) {
            api.delete(`/assunto/${subjectId}`)
                .then((response) => {
                    setMensagemFeedback(response.data.message || 'Assunto deletado com sucesso!');
                    buscarAssuntos();
                })
                .catch((error) => {
                    console.error('Erro ao deletar assunto:', error);
                    setMensagemFeedback('Falha ao deletar o assunto.');
                });
        }
    };

    return (
        <div className="container mt-4">
            <h2 className="mb-4">Assuntos</h2>
            {mensagemFeedback && (
                <div className="alert alert-info" role="alert">
                    {mensagemFeedback}
                </div>
            )}
            <button className="btn btn-primary mb-3" onClick={handleNovoAssunto}>
                Novo Assunto
            </button>
            {showForm && (
                <div className="card p-3 mb-4">
                    <h3>{editingSubjectId ? 'Editar Assunto' : 'Adicionar Novo Assunto'}</h3>
                    <form onSubmit={handleSalvarAssunto}>
                        <div className="mb-3">
                            <label htmlFor="subjectName" className="form-label">Nome do Assunto</label>
                            <input
                                type="text"
                                className="form-control"
                                id="subjectName"
                                value={newSubjectName}
                                onChange={(e) => setNewSubjectName(e.target.value)}
                                placeholder="Digite o nome do assunto"
                            />
                        </div>
                        <button type="submit" className="btn btn-success">Salvar</button>
                    </form>
                </div>
            )}
            <ul className="list-group">
                {subjects.map((subject) => (
                    <li key={subject.CodAs} className="list-group-item d-flex justify-content-between align-items-center">
                        {subject.Descricao}
                        <div>
                            <button
                                className="btn btn-sm btn-warning me-2"
                                onClick={() => handleEditarAssunto(subject)}
                            >
                                <i className="bi bi-pencil"></i>
                            </button>
                            <button
                                className="btn btn-sm btn-danger"
                                onClick={() => handleDeletarAssunto(subject.CodAs)}
                            >
                                <i className="bi bi-trash"></i>
                            </button>
                        </div>
                    </li>
                ))}
            </ul>
        </div>
    );
};

export default SubjectList;
