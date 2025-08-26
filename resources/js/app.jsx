import React from 'react';
import ReactDOM from 'react-dom/client';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import '../css/app.css';
import Navbar from './components/common/Navbar';
import BookList from './components/books/BookList';
import AuthorList from './components/authors/AuthorList';
import SubjectList from './components/subjects/SubjectList';
import ReportMenu from './components/reports/ReportMenu';

const App = () => {
    return (
        <Router>
            <Navbar />
            <div id="content" className='container-fluid'>
                <div className="row">
                    <div className="col-md-12">
                        <Routes>
                            <Route path="/books" element={<BookList />} />
                            <Route path="/authors" element={<AuthorList />} />
                            <Route path="/subjects" element={<SubjectList />} />
                            <Route path="/relatorios" element={<ReportMenu />} />
                            <Route path="/" element={<h1 className='text-center h2'>Bem-vindo ao Cadastro de Livros</h1>} />
                        </Routes>
                    </div>
                </div>
            </div>
        </Router>
    );
};

ReactDOM.createRoot(document.getElementById('react-root')).render(<App />);