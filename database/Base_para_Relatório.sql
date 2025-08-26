SELECT
    l.Codl,
    l.Titulo,
    l.Edicao,
    l.Editora,
    l.AnoPublicacao,
    l.Valor,
    GROUP_CONCAT(DISTINCT a.Nome ORDER BY a.Nome SEPARATOR ', ') AS Autores,
    GROUP_CONCAT(DISTINCT ass.Descricao ORDER BY ass.Descricao SEPARATOR ', ') AS Assuntos
FROM
    Livro l
        INNER JOIN Livro_Autor la ON la.Livro_Codl = l.Codl
        INNER JOIN Autor a ON a.CodAu = la.Autor_CodAu
        INNER JOIN Livro_Assunto las ON las.Livro_Codl = l.Codl
        INNER JOIN Assunto ass ON ass.CodAs = las.Assunto_CodAs
GROUP BY
    l.Codl, l.Titulo, l.Edicao, l.Editora, l.AnoPublicacao, l.Valor
;
