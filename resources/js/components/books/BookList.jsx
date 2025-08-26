import React, { useEffect, useState } from 'react';
import api from '../../api';
import Creatable from 'react-select/creatable';
import makeAnimated from 'react-select/animated';
import Modal from 'react-modal';

Modal.setAppElement(document.body); // Define o elemento base para o modal como o body

const BookList = () => {
    const [books, setBooks] = useState([]);
    const [showForm, setShowForm] = useState(false);
    const [newBook, setNewBook] = useState({
        Titulo: '',
        Editora: '',
        Edicao: '',
        AnoPublicacao: '',
        AutorID: [],
        AssuntoID: [],
        Valor: '' // Adicionado campo Valor
    });
    const [authorsOptions, setAuthorsOptions] = useState([]);
    const [subjectsOptions, setSubjectsOptions] = useState([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedBook, setSelectedBook] = useState(null);

    useEffect(() => {
        fetchBooks();
        fetchAuthors();
        fetchSubjects();
    }, []);

    const formatCurrency = (value) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    };

    const fetchBooks = () => {
        api.get('/livro')
            .then((response) => {
                const formattedBooks = response.data?.data.map((book) => ({
                    ...book,
                    Valor: formatCurrency(book.Valor) // Formata o campo Valor
                }));
                setBooks(formattedBooks || []);
            })
            .catch((error) => console.error('Erro ao buscar livros:', error));
    };

    const fetchAuthors = () => {
        api.get('/autor')
            .then((response) => {
                const options = response.data.data.map((author) => ({
                    value: author.CodAu,
                    label: author.Nome
                }));
                setAuthorsOptions(options);
            })
            .catch((error) => console.error('Erro ao buscar autores:', error));
    };

    const fetchSubjects = () => {
        api.get('/assunto')
            .then((response) => {
                const options = response.data.data.map((subject) => ({
                    value: subject.CodAs,
                    label: subject.Descricao
                }));
                setSubjectsOptions(options);
            })
            .catch((error) => console.error('Erro ao buscar assuntos:', error));
    };

    const handleAddBook = (e) => {
        e.preventDefault();

        // Extrai apenas os valores numéricos de AutorID, AssuntoID e converte Valor para float
        const payload = {
            ...newBook,
            AutorID: newBook.AutorID.map((autor) => autor.value),
            AssuntoID: newBook.AssuntoID.map((assunto) => assunto.value),
            Valor: parseFloat(newBook.Valor.replace('R$', '').replace(',', '.')) // Converte para float
        };

        api.post('/livro', payload)
            .then((response) => {
                setNewBook({
                    Titulo: '',
                    Editora: '',
                    Edicao: '',
                    AnoPublicacao: '',
                    AutorID: [],
                    AssuntoID: [],
                    Valor: ''
                });
                setShowForm(false);
                fetchBooks();
            })
            .catch((error) => console.error('Erro ao adicionar livro:', error));
    };

    const handleUpdateBook = (e) => {
        e.preventDefault();

        if (!selectedBook || !selectedBook.Codl) {
            console.error('Livro selecionado é inválido ou não foi definido.');
            return;
        }

        // Extrai apenas os valores numéricos de AutorID, AssuntoID e converte Valor para float
        const payload = {
            ...newBook,
            AutorID: newBook.AutorID.map((autor) => autor.value),
            AssuntoID: newBook.AssuntoID.map((assunto) => assunto.value),
            Valor: parseFloat(newBook.Valor.replace('R$', '').replace(',', '.')) // Converte para float
        };

        api.put(`/livro/${selectedBook.Codl}`, payload)
            .then((response) => {
                setNewBook({
                    Titulo: '',
                    Editora: '',
                    Edicao: '',
                    AnoPublicacao: '',
                    AutorID: [],
                    AssuntoID: [],
                    Valor: ''
                });
                setShowForm(false);
                fetchBooks();
            })
            .catch((error) => console.error('Erro ao atualizar livro:', error));
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setNewBook((prev) => ({ ...prev, [name]: value }));
    };

    const handleSelectChange = (selectedOptions, action) => {
        const { name } = action;
        setNewBook((prev) => ({
            ...prev,
            [name]: selectedOptions // Atualiza mantendo os itens já selecionados
        }));
    };

    const handleCreateAuthor = (inputValue) => {
        api.post('/autor', { Nome: inputValue })
            .then((response) => {
                const newOption = {
                    value: response.data.id,
                    label: response.data.Nome
                };
                setAuthorsOptions((prev) => [...prev, newOption]);
                setNewBook((prev) => ({
                    ...prev,
                    AutorID: [...prev.AutorID, response.data.id]
                }));
            })
            .catch((error) => console.error('Erro ao adicionar autor:', error));
    };

    const handleCreateSubject = (inputValue) => {
        api.post('/assunto', { Descricao: inputValue })
            .then((response) => {
                const newOption = {
                    value: response.data.data.CodAs, // Usa o campo correto do payload
                    label: response.data.data.Descricao // Usa o campo correto do payload
                };
                setSubjectsOptions((prev) => [...prev, newOption]); // Atualiza a lista de opções
                setNewBook((prev) => ({
                    ...prev,
                    AssuntoID: prev.AssuntoID ? [...prev.AssuntoID, newOption.value] : [newOption.value] // Garante que o novo assunto seja selecionado
                }));
                // Atualiza o campo de seleção para exibir o novo assunto
                const updatedSubjects = [...subjectsOptions, newOption];
                setSubjectsOptions(updatedSubjects);
            })
            .catch((error) => console.error('Erro ao adicionar assunto:', error));
    };

    const handleCreateSubjectOnComma = (inputValue) => {
        if (inputValue.trim() !== '') {
            handleCreateSubject(inputValue.trim());
        }
    };

    const handleViewBookDetails = (bookId) => {
        const book = books.find((b) => b.Codl === bookId);
        setSelectedBook(book);
        setIsModalOpen(true);
    };

    const handleEditBook = (bookId) => {
        const book = books.find((b) => b.Codl === bookId);

        if (!book) {
            console.error('Livro não encontrado para edição.');
            return;
        }

        setSelectedBook(book); // Define o livro selecionado

        const mappedAuthors = book.Autores.map((autor) => ({
            value: autor.CodAu,
            label: autor.Nome
        }));

        const mappedSubjects = book.Assuntos.map((assunto) => ({
            value: assunto.CodAs,
            label: assunto.Descricao
        }));

        setNewBook({
            Titulo: book.Titulo,
            Editora: book.Editora,
            Edicao: book.Edicao,
            AnoPublicacao: book.AnoPublicacao,
            AutorID: mappedAuthors,
            AssuntoID: mappedSubjects,
            Valor: book.Valor // Adicionado campo Valor
        });

        setShowForm('edit');
    };

    const handleDeleteBook = (bookId) => {
        if (window.confirm('Tem certeza que deseja excluir este livro?')) {
            api.delete(`/livro/${bookId}`)
                .then(() => {
                    fetchBooks(); // Atualiza a lista de livros após exclusão
                })
                .catch((error) => console.error('Erro ao excluir livro:', error));
        }
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setSelectedBook(null);
    };

    const handleNewBook = () => {
        setNewBook({
            Titulo: '',
            Editora: '',
            Edicao: '',
            AnoPublicacao: '',
            AutorID: [],
            AssuntoID: [],
            Valor: '' // Adicionado campo Valor
        });
        setShowForm(true);
    };

    return (
        <div className="container mt-4">
            <h2 className="mb-4">Livros</h2>
            <button className="btn btn-primary mb-3" onClick={() => {
                if (showForm === 'add') {
                    setShowForm(false);
                } else {
                    handleNewBook();
                    setShowForm('add');
                }
            }}>
                {showForm === 'add' ? 'Cancelar' : 'Novo Livro'}
            </button>
            {showForm === 'add' && (
                <div className="card p-3 mb-4">
                    <h3>Adicionar Novo Livro</h3>
                    <form onSubmit={handleAddBook}>
                        <div className="mb-3">
                            <label htmlFor="Titulo" className="form-label">Título</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Titulo"
                                name="Titulo"
                                value={newBook.Titulo}
                                onChange={handleChange}
                                placeholder="Digite o título do livro"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Editora" className="form-label">Editora</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Editora"
                                name="Editora"
                                value={newBook.Editora}
                                onChange={handleChange}
                                placeholder="Digite a editora"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Edicao" className="form-label">Edição</label>
                            <input
                                type="number"
                                className="form-control"
                                id="Edicao"
                                name="Edicao"
                                value={newBook.Edicao}
                                onChange={handleChange}
                                placeholder="Digite a edição"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AnoPublicacao" className="form-label">Ano de Publicação</label>
                            <input
                                type="text"
                                className="form-control"
                                id="AnoPublicacao"
                                name="AnoPublicacao"
                                value={newBook.AnoPublicacao}
                                onChange={handleChange}
                                placeholder="Digite o ano de publicação"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Valor" className="form-label">Valor</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Valor"
                                name="Valor"
                                value={newBook.Valor}
                                onChange={(e) => {
                                    let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos
                                    while (rawValue.length < 3) {
                                        rawValue = '0' + rawValue; // Garante pelo menos 3 dígitos
                                    }
                                    const integerPart = rawValue.slice(0, -2); // Parte inteira
                                    const decimalPart = rawValue.slice(-2); // Parte decimal
                                    const formattedValue = `R$ ${parseInt(integerPart, 10).toLocaleString('pt-BR')},${decimalPart}`;
                                    setNewBook((prev) => ({ ...prev, Valor: formattedValue }));
                                }}
                                placeholder="Digite o valor"
                                required
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AutorID" className="form-label">Autores</label>
                            <Creatable
                                isMulti
                                name="AutorID"
                                options={authorsOptions}
                                value={newBook.AutorID} // Usa diretamente o estado newBook.AutorID
                                className="basic-multi-select"
                                classNamePrefix="select"
                                components={makeAnimated()}
                                onChange={(selectedOptions, action) => handleSelectChange(selectedOptions, action)}
                                onCreateOption={handleCreateAuthor}
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AssuntoID" className="form-label">Assuntos</label>
                            <Creatable
                                isMulti
                                name="AssuntoID"
                                options={subjectsOptions}
                                value={newBook.AssuntoID} // Usa diretamente o estado newBook.AssuntoID
                                className="basic-multi-select"
                                classNamePrefix="select"
                                components={makeAnimated()}
                                onChange={(selectedOptions, action) => handleSelectChange(selectedOptions, action)}
                                onCreateOption={(inputValue) => handleCreateSubject(inputValue)}
                                onInputKeyDown={(event) => {
                                    if (event.key === ',') {
                                        event.preventDefault();
                                        handleCreateSubjectOnComma(event.target.value);
                                    }
                                }}
                            />
                        </div>
                        <button type="submit" className="btn btn-success">Salvar</button>
                    </form>
                </div>
            )}
            {showForm === 'edit' && (
                <div className="card p-3 mb-4">
                    <h3>Editar Livro</h3>
                    <form onSubmit={handleUpdateBook}>
                        <div className="mb-3">
                            <label htmlFor="Titulo" className="form-label">Título</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Titulo"
                                name="Titulo"
                                value={newBook.Titulo}
                                onChange={handleChange}
                                placeholder="Digite o título do livro"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Editora" className="form-label">Editora</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Editora"
                                name="Editora"
                                value={newBook.Editora}
                                onChange={handleChange}
                                placeholder="Digite a editora"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Edicao" className="form-label">Edição</label>
                            <input
                                type="number"
                                className="form-control"
                                id="Edicao"
                                name="Edicao"
                                value={newBook.Edicao}
                                onChange={handleChange}
                                placeholder="Digite a edição"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AnoPublicacao" className="form-label">Ano de Publicação</label>
                            <input
                                type="text"
                                className="form-control"
                                id="AnoPublicacao"
                                name="AnoPublicacao"
                                value={newBook.AnoPublicacao}
                                onChange={handleChange}
                                placeholder="Digite o ano de publicação"
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="Valor" className="form-label">Valor</label>
                            <input
                                type="text"
                                className="form-control"
                                id="Valor"
                                name="Valor"
                                value={newBook.Valor}
                                onChange={(e) => {
                                    let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos
                                    while (rawValue.length < 3) {
                                        rawValue = '0' + rawValue; // Garante pelo menos 3 dígitos
                                    }
                                    const integerPart = rawValue.slice(0, -2); // Parte inteira
                                    const decimalPart = rawValue.slice(-2); // Parte decimal
                                    const formattedValue = `R$ ${parseInt(integerPart, 10).toLocaleString('pt-BR')},${decimalPart}`;
                                    setNewBook((prev) => ({ ...prev, Valor: formattedValue }));
                                }}
                                placeholder="Digite o valor"
                                required
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AutorID" className="form-label">Autores</label>
                            <Creatable
                                isMulti
                                name="AutorID"
                                options={authorsOptions}
                                value={newBook.AutorID} // Usa diretamente o estado newBook.AutorID
                                className="basic-multi-select"
                                classNamePrefix="select"
                                components={makeAnimated()}
                                onChange={(selectedOptions, action) => handleSelectChange(selectedOptions, action)}
                                onCreateOption={handleCreateAuthor}
                            />
                        </div>
                        <div className="mb-3">
                            <label htmlFor="AssuntoID" className="form-label">Assuntos</label>
                            <Creatable
                                isMulti
                                name="AssuntoID"
                                options={subjectsOptions}
                                value={newBook.AssuntoID} // Usa diretamente o estado newBook.AssuntoID
                                className="basic-multi-select"
                                classNamePrefix="select"
                                components={makeAnimated()}
                                onChange={(selectedOptions, action) => handleSelectChange(selectedOptions, action)}
                                onCreateOption={(inputValue) => handleCreateSubject(inputValue)}
                                onInputKeyDown={(event) => {
                                    if (event.key === ',') {
                                        event.preventDefault();
                                        handleCreateSubjectOnComma(event.target.value);
                                    }
                                }}
                            />
                        </div>
                        <button type="submit" className="btn btn-success">Atualizar</button>
                    </form>
                </div>
            )}
            <ul className="list-group">
                {Array.isArray(books) && books.map((book) => (
                    <li key={book.Codl} className="list-group-item d-flex justify-content-between align-items-center">
                        <span>{book.Titulo}</span>
                        <div>
                            <strong className="me-3">{book.Valor}</strong> {/* Exibe o valor formatado */}
                            <button
                                className="btn btn-sm btn-info me-2"
                                onClick={() => handleViewBookDetails(book.Codl)}
                            >
                                Visualizar
                            </button>
                            <button
                                className="btn btn-sm btn-warning me-2"
                                onClick={() => handleEditBook(book.Codl)}
                            >
                                Editar
                            </button>
                            <button
                                className="btn btn-sm btn-danger"
                                onClick={() => handleDeleteBook(book.Codl)}
                            >
                                Excluir
                            </button>
                        </div>
                    </li>
                ))}
            </ul>
            <Modal
                isOpen={isModalOpen}
                onRequestClose={closeModal}
                contentLabel="Detalhes do Livro"
                className="modal-dialog modal-dialog-centered"
                overlayClassName="modal-backdrop"
                style={{
                    content: {
                        position: 'relative',
                        width: 'auto',
                        margin: '1rem',
                        background: '#fff',
                        borderRadius: '0.3rem',
                        padding: '1.5rem',
                        boxShadow: '0 0.125rem 0.25rem rgba(0, 0, 0, 1)',
                        overflow: 'auto',
                    },
                }}
            >
                {selectedBook && (
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">Detalhes do Livro</h5>
                            <button type="button" className="btn-close" onClick={closeModal}></button>
                        </div>
                        <div className="modal-body">
                            <p><strong>Título:</strong> {selectedBook.Titulo}</p>
                            <p><strong>Editora:</strong> {selectedBook.Editora}</p>
                            <p><strong>Edição:</strong> {selectedBook.Edicao}</p>
                            <p><strong>Ano de Publicação:</strong> {selectedBook.AnoPublicacao}</p>
                            <p><strong>Autores:</strong> {Array.isArray(selectedBook.Autores) ? selectedBook.Autores.map((autor) => autor.Nome).join(', ') : 'N/A'}</p>
                            <p><strong>Assuntos:</strong> {Array.isArray(selectedBook.Assuntos) ? selectedBook.Assuntos.map((assunto) => assunto.Descricao).join(', ') : 'N/A'}</p>
                        </div>
                        <div className="modal-footer">
                            <button 
                                className="btn btn-secondary" 
                                onClick={closeModal}
                            >
                                Fechar
                            </button>
                        </div>
                    </div>
                )}
            </Modal>
        </div>
    );
};

export default BookList;
