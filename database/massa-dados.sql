-- Inserir autores
INSERT INTO Autor (CodAu, Nome) VALUES
(1, 'Machado de Assis'),
(2, 'Clarice Lispector'),
(3, 'Guimarães Rosa'),
(4, 'Jorge Amado'),
(5, 'José de Alencar'),
(6, 'Graciliano Ramos'),
(7, 'Monteiro Lobato'),
(8, 'Rachel de Queiroz'),
(9, 'Cecília Meireles'),
(10, 'Carlos Drummond de Andrade');

-- Inserir assuntos
INSERT INTO Assunto (CodAs, Descricao) VALUES
(1, 'Literatura Brasileira'),
(2, 'Romance'),
(3, 'Ficção'),
(4, 'Clássicos'),
(5, 'Poesia'),
(6, 'Contos'),
(7, 'Drama'),
(8, 'Aventura'),
(9, 'História'),
(10, 'Fantasia');

-- Inserir livros
INSERT INTO Livro (Codl, Titulo, Editora, Edicao, AnoPublicacao, Valor) VALUES
(1, 'Dom Casmurro', 'Editora A', 1, '1899', 29.90),
(2, 'A Hora da Estrela', 'Editora B', 1, '1977', 19.90),
(3, 'Grande Sertão: Veredas', 'Editora C', 1, '1956', 39.90),
(4, 'Capitães da Areia', 'Editora D', 1, '1937', 24.90),
(5, 'O Cortiço', 'Editora E', 1, '1890', 15.50),
(6, 'Memórias Póstumas de Brás Cubas', 'Editora F', 2, '1881', 20.00),
(7, 'Vidas Secas', 'Editora G', 3, '1938', 25.00),
(8, 'Iracema', 'Editora H', 4, '1865', 30.00),
(9, 'Macunaíma', 'Editora I', 5, '1928', 35.00),
(10, 'O Guarani', 'Editora J', 1, '1857', 40.00),
-- Adicionar mais livros até 1000
-- Exemplo de livros gerados dinamicamente
(11, 'Livro 11', 'Editora A', 1, '2000', 22.50),
(12, 'Livro 12', 'Editora B', 2, '2001', 18.75),
(13, 'Livro 13', 'Editora C', 3, '2002', 27.30),
(14, 'Livro 14', 'Editora D', 4, '2003', 33.10),
(15, 'Livro 15', 'Editora E', 5, '2004', 29.99);

-- Relacionar livros com autores
INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 1),
(7, 6),
(8, 5),
(9, 7),
(10, 5),
-- Relacionar dinamicamente
(11, 8),
(12, 9),
(13, 10),
(14, 2),
(15, 3);

-- Relacionar livros com assuntos
INSERT INTO Livro_Assunto (Livro_Codl, Assunto_CodAs) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(4, 1),
(4, 3),
(5, 2),
(6, 4),
(7, 5),
(8, 6),
(9, 7),
(10, 8),
-- Relacionar dinamicamente
(11, 9),
(12, 10),
(13, 1),
(14, 2),
(15, 3);