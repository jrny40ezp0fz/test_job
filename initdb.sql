CREATE DATABASE libdb;
\c libdb;

CREATE TABLE books (
	id SERIAL PRIMARY KEY,
	title VARCHAR NOT NULL,
	rating REAL
);

CREATE TABLE genres (
	id SERIAL PRIMARY KEY,
	title VARCHAR NOT NULL UNIQUE
);

CREATE TABLE authors (
	id SERIAL PRIMARY KEY,
	name VARCHAR NOT NULL UNIQUE
);

CREATE TABLE book_genre (
	book_id INT NOT NULL,
	genre_id INT NOT NULL,
	FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
	FOREIGN KEY (genre_id) REFERENCES genres (id) ON DELETE CASCADE
);

CREATE TABLE book_author (
	book_id INT NOT NULL,
	author_id INT NOT NULL,
	FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
	FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE CASCADE
);

CREATE VIEW books_view AS (
	SELECT books.id AS id, books.title AS title, BQUERY.authors AS authors, BQUERY.genres AS genres, books.rating AS rating
	FROM books
	INNER JOIN
		(SELECT AQUERY.book_id AS book_id, AQUERY.authors AS authors, GQUERY.genres AS genres
		FROM
				(SELECT book_author.book_id AS book_id, STRING_AGG(authors.name, ',') AS authors
				FROM authors
				INNER JOIN
				book_author ON authors.id = book_author.author_id GROUP BY book_author.book_id)
				AS AQUERY

			INNER JOIN

				(SELECT book_genre.book_id AS book_id, STRING_AGG(genres.title, ',') AS genres
				FROM genres
				INNER JOIN
				book_genre ON genres.id = book_genre.genre_id GROUP BY book_genre.book_id)
				AS GQUERY
			ON AQUERY.book_id = GQUERY.book_id)
		AS BQUERY
	ON books.id = BQUERY.book_id
);

CREATE VIEW authors_view AS (
	SELECT authors.id AS id, authors.name AS name, AVG(books.rating) AS rating
	FROM book_author LEFT JOIN authors ON book_author.author_id = authors.id
	LEFT JOIN books ON book_author.book_id = books.id
	GROUP BY authors.id
);