CREATE DATABASE library;

\connect library;

CREATE TABLE IF NOT EXISTS book (
    id SERIAL PRIMARY KEY,
    title VARCHAR(64) NOT NULL,
    rating REAL
);

CREATE TABLE IF NOT EXISTS author (
    id SERIAL PRIMARY KEY,
    name VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS genre (
    id SERIAL PRIMARY KEY,
    title VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS book_author (
    book_id INTEGER NOT NULL,
    author_id INTEGER NOT NULL,
    PRIMARY KEY (book_id, author_id),
    FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS book_genre (
    book_id INTEGER NOT NULL,
    genre_id INTEGER NOT NULL,
    PRIMARY KEY (book_id, genre_id),
    FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE,
    FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE
);

CREATE VIEW book_info AS (
    SELECT *
    FROM book

    LEFT JOIN

    (SELECT b_a.book_id AS id, STRING_AGG(a.name, ', ') AS authors
    FROM book_author AS b_a
    LEFT JOIN
    author AS a
    ON b_a.author_id = a.id
    GROUP BY b_a.book_id) AS author_list

    USING (id)

    LEFT JOIN

    (SELECT b_g.book_id AS id, STRING_AGG(g.title, ', ') AS genres
    FROM book_genre AS b_g
    LEFT JOIN genre AS g
    ON b_g.genre_id = g.id
    GROUP BY b_g.book_id) AS genre_list

    USING (id)
);

CREATE VIEW author_info AS (
    SELECT a.id AS id, a.name AS name, AVG(b.rating) AS rating
    FROM author AS a
    LEFT JOIN book_author AS b_a
    ON a.id = b_a.author_id
    LEFT JOIN book AS b
    ON b.id = b_a.book_id
    GROUP BY a.id
);

-- CREATE TABLE book_init AS (SELECT * FROM book) WITH NO DATA;
-- CREATE TABLE author_init AS (SELECT * FROM author) WITH NO DATA;
-- CREATE TABLE genre_init AS (SELECT * FROM genre) WITH NO DATA;
-- CREATE TABLE book_author_init AS (SELECT * FROM book_author) WITH NO DATA;
-- CREATE TABLE book_genre_init AS (SELECT * FROM book_genre) WITH NO DATA;

INSERT INTO book (title, rating)
VALUES ('Book A', 72),
        ('Book B', 64),
        ('Book C', 87),
        ('Book D', 91),
        ('Book E', NULL),
        ('Book F', 68),
        ('Book G', 75),
        ('Book H', NULL),
        ('Book I', 79),
        ('Book J', NULL),
        ('Book K', 81),
        ('Book L', 69);

INSERT INTO author (name)
VALUES ('Author 1'),
        ('Author 2'),
        ('Author 3'),
        ('Author 4'),
        ('Author 5'),
        ('Author 6'),
        ('Author 7'),
        ('Author 8');

INSERT INTO genre (title)
VALUES ('Genre 1'),
        ('Genre 2'),
        ('Genre 3'),
        ('Genre 4'),
        ('Genre 5');

INSERT INTO book_author (book_id, author_id)
VALUES (1, 1),
        (1, 2),
        (2, 2),
        (2, 3),
        (3, 1),
        (3, 3),
        (5, 1),
        (5, 4),
        (6, 5),
        (7, 5),
        (8, 1),
        (8, 5),
        (8, 7),
        (9, 7),
        (11, 5),
        (12, 1);

INSERT INTO book_genre (book_id, genre_id)
VALUES (1, 3),
        (2, 1),
        (3, 3),
        (4, 1),
        (5, 2),
        (7, 3),
        (8, 1),
        (9, 1),
        (10, 1),
        (11, 4),
        (11, 1);
