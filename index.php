<?php

    global $warnings;
    $warnings = '<i></i><br>';

 ?>

<?php

    function generateBookInfoContent($dbconnection) {
        global $warnings;

        $content = '';

        if ( !($result = pg_query($dbconnection, "SELECT id, title, authors, genres, rating FROM book_info ORDER BY title"))) {
            $warnings .= "<i>Database query error.</i><br>";
            return;
        }

        $content .= '<div class="contentTable">';

        $content .= '<span class="contentRow">' .
                    '<span class="contentCell"><b><i>ID</i></b></span>' .
                    '<span class="contentCell"><b><i>Title</i></b></span>' .
                    '<span class="contentCell"><b><i>Authors</i></b></span>' .
                    '<span class="contentCell"><b><i>Genres</i></b></span>' .
                    '<span class="contentCell"><b><i>Rating</i></b></span>' .
                    '</span>';

        while ( $row = pg_fetch_row($result)) {
            $content .= '<form class="contentRow" action="index.php" method="post">';

            $content .= '<span class="contentCell">' . $row[0] . '</span>';
            $content .= '<span class="contentCell"><input type="text" placeholder="Book title" name="bookTitle" value="' . $row[1] . '" required></span>';
            $content .= '<span class="contentCell">' . $row[2] . '</span>';
            $content .= '<span class="contentCell">' . $row[3] . '</span>';
            $content .= '<span class="contentCell"><input type="number" min="0" max="100" name="bookRating" value="' . $row[4] . '"></span>';
            $content .= '<span class="contentCell"><input type="submit" name="editBook" value="edit"></span>';
            $content .= '<span class="contentCell"><input type="submit" name="deleteBook" value="-"></span>';
            $content .= '<span class="contentCell"><input type="hidden" name="bookID" value="' . $row[0] . '"></span>';

            $content .= '</form>';
        }

        $content .= '<form class="contentRow" action="index.php" method="post">' .
                    '<span class="contentCell"></span>' .
                    '<span class="contentCell"><input type="text" placeholder="Book title" name="newBookTitle" required></span>' .
                    '<span class="contentCell"><input type="text" placeholder="Authors (comma space separated)" title="Enter authors comma space separated." name="newBookAuthors" required></span>' .
                    '<span class="contentCell"><input type="text" placeholder="Genres (comma space separated)" title="Enter genres comma space separated." name="newBookGenres" required></span>' .
                    '<span class="contentCell"><input type="number" min="0" max="100" placeholder="rating" name="newBookRating"></span>' .
                    '<span class="contentCell"></span>' .
                    '<span class="contentCell"><input type="submit" name="addNewBook" value="+"></span>';

        $content .= '</div>';

        return $content;
    }

    function generateAuthorInfoContent($dbconnection) {
        global $warnings;

        $content = '';

        if ( !($result = pg_query($dbconnection, "SELECT id, name, rating FROM author_info ORDER BY name"))) {
            $warnings .= "<i>Database query error.</i><br>";
            return;
        }

        $content .= '<div class="contentTable">';

        $content .= '<span class="contentRow">' .
                    '<span class="contentCell"><b><i>ID</i></b></span>' .
                    '<span class="contentCell"><b><i>Name</i></b></span>' .
                    '<span class="contentCell"><b><i>Rating</i></b></span>' .
                    '</span>';

        while ( $row = pg_fetch_row($result)) {
            $content .= '<form class="contentRow" action="index.php" method="post">';

            $content .= '<span class="contentCell">' . $row[0] . '</span>';
            $content .= '<span class="contentCell"><input type="text" placeholder="Author name" name="authorName" value="' . $row[1] . '" required></span>';
            $content .= '<span class="contentCell">' . $row[2] . '</span>';
            $content .= '<span class="contentCell"><input type="submit" name="editAuthor" value="edit"></span>';
            $content .= '<span class="contentCell"><input type="submit" name="deleteAuthor" value="-"></span>';
            $content .= '<span class="contentCell"><input type="hidden" name="authorID" value="' . $row[0] . '"></span>';

            $content .= '</form>';
        }

        $content .= '<form class="contentRow" action="index.php" method="post">' .
                    '<span class="contentCell"></span>' .
                    '<span class="contentCell"><input type="text" placeholder="Author name" name="newAuthorName" required></span>' .
                    '<span class="contentCell"></span>' .
                    '<span class="contentCell"></span>' .
                    '<span class="contentCell"><input type="submit" name="addNewAuthor" value="+"></span>' .
                    '</form>';

        $content .= '</div>';

        return $content;
    }

    function generateGenreInfoContent($dbconnection) {
        global  $warnings;

        $content = '';

        if ( !($result = pg_query($dbconnection, "SELECT id, title FROM genre ORDER BY title"))) {
            $warnings .= "<i>Database query error.</i><br>";
            return;
        }

        $content .= '<div class="contentTable">';

        $content .= '<span class="contentRow">' .
                    '<span class="contentCell"><b><i>ID</i></b></span>' .
                    '<span class="contentCell"><b><i>Title</i></b></span>' .
                    '</span>';

        while ( $row = pg_fetch_row($result)) {
            $content .= '<form class="contentRow" action="index.php" method="post">';

            $content .= '<span class="contentCell">' . $row[0] . '</span>';
            $content .= '<span class="contentCell"><input type="text" placeholder="Genre title" name="genreTitle" value="' . $row[1] . '" required></span>';
            $content .= '<span class="contentCell"><input type="submit" name="editGenre" value="edit"></span>';
            $content .= '<span class="contentCell"><input type="submit" name="deleteGenre" value="-"></span>';
            $content .= '<span class="contentCell"><input type="hidden" name="genreID" value="' . $row[0] . '"></span>';

            $content .= '</form>';
        }

        $content .= '<form class="contentRow" action="index.php" method="post">';
        $content .= '<span class="contentCell"></span>';
        $content .= '<span class="contentCell"><input type="text" placeholder="Genre title" name="newGenreTitle" required></span>';
        $content .= '<span class="contentCell"></span>';
        $content .= '<span class="contentCell"><input type="submit" name="addNewGenre" value="+"></span>';
        $content .= '</form>';

        $content .= '</div>';

        return $content;
    }

 ?>


 <?php

    function addNewBook($dbconnection, $newBookTitle, $newBookAuthors, $newBookGenres, $newBookRating) {
        global $warnings;

        $newBookTitle = trim($newBookTitle);
        $newBookAuthors = trim($newBookAuthors);
        $newBookGenres = trim($newBookGenres);

        if (!strlen($newBookTitle) || !strlen($newBookAuthors) || !strlen($newBookGenres)) {
            $warnings .= "<i>Fill out empty fields.</i><br>";
            return;
        }
        if ($newBookRating != NULL && !ctype_digit($newBookRating)) {
            $warnings .= "<i>Invalid book rating.</i><br>";
            return;
        }
        $authorsList = explode(', ', $newBookAuthors);
        $genresList = explode(', ', $newBookGenres);

        $authorsID = array();
        $genresID = array();

        $query = 'SELECT id FROM author WHERE name IN ' . '(';

        for ($i = 1; $i < count($authorsList); $i++) {
            $query .= '$' . "$i, ";
        }
        $query .= '$' . count($authorsList) . ')';

        if ( !($result = pg_query_params($dbconnection, $query, $authorsList))) {
            $warnings .= "<i>Database query error.</i><br>";
            return;
        }
        if (pg_num_rows($result) < count($authorsList)) {
            $warnings .= "<i>Add authors first.</i><br>";
            return;
        }

        $authorsID = array();

        for ($i = 0; $i < pg_num_rows($result); $i++) {
            $authorsID = array_merge($authorsID, pg_fetch_row($result));
        }

        $query = 'SELECT id FROM genre WHERE title IN ' . '(';

        for ($i = 1; $i < count($genresList); $i++) {
            $query .= '$' . "$i, ";
        }
        $query .= '$' . count($genresList) . ')';

        if ( !($result = pg_query_params($dbconnection, $query, $genresList))) {
            $warnings .= "<i>Database query error.</i><br>";
            return;
        }
        if (pg_num_rows($result) < count($genresList)) {
            $warnings .= "<i>Add genres first.</i><br>";
            return;
        }

        $genresID = array();

        for ($i = 0; $i < pg_num_rows($result); $i++) {
            $genresID = array_merge($genresID, pg_fetch_row($result));
        }

        $query = 'INSERT INTO book (title, rating) VALUES ($1, $2) RETURNING id';

        if ( !($result = pg_query_params($dbconnection, $query, array($newBookTitle, (!$newBookRating) ? NULL : $newBookRating)))) {
            $warnings .= "<i>Error adding new book.</i><br>";
            return;
        }

        $newBookID = pg_fetch_row($result);

        $query = 'INSERT INTO book_author (book_id, author_id) VALUES ';

        for ($i = 1; $i < count($authorsID); $i++) {
            $query .= '($1, $' . ($i + 1) . '), ';
        }
        $query .= '($1, $' . (count($authorsID) + 1) . ')';

        $queryparams = array_merge($newBookID, $authorsID);

        if ( !($result = pg_query_params($dbconnection, $query, $queryparams))) {
            $warnings .= "<i>Error adding new book.</i><br>";
            return;
        }

        $query = 'INSERT INTO book_genre (book_id, genre_id) VALUES ';

        for ($i = 1; $i < count($genresID); $i++) {
            $query .= '($1, $' . ($i + 1) . '), ';
        }
        $query .= '($1, $' . (count($genresID) + 1) . ')';

        $queryparams = array_merge($newBookID, $genresID);

        if ( !($result = pg_query_params($dbconnection, $query, $queryparams))) {
            $warnings .= "<i>Error adding new book.</i><br>";
            return;
        }

        $warnings .= "<i>Book $newBookTitle added</i><br>";

        return $newBookID;
    }

    function deleteBook($dbconnection, $bookID) {
        global $warnings;

        if (!ctype_digit($bookID)) {
            $warnings .= "<i>Invalid book ID.</i><br>";
            return;
        }
        if ( !($result = pg_query($dbconnection, "DELETE FROM book WHERE id = $bookID"))) {
            $warnings .= "<i>Error deleting book.</i><br>";
            return;
        }
        $warnings .= "<i>Book deleted.</i><br>";
    }

    function editBook($dbconnection, $bookID, $bookTitle, $bookRating) {
        global $warnings;

        if (!ctype_digit($bookID)) {
            $warnings .= "<i>Invalid book ID.</i><br>";
            return;
        }
        if (!strlen($bookTitle)) {
            $warnings .= "<i>Book title is required.</i><br>";
            return;
        }
        if (!ctype_digit($bookRating)) {
            $warnings .= "<i>Invalid book rating.</i><br>";
            return;
        }
        $query = "UPDATE book SET title = $1, rating = $2 WHERE id = $3";
        if ( !($result = pg_query_params($dbconnection, $query, array($bookTitle, $bookRating, $bookID)))) {
            $warnings .= "<i>Error book editing.</i><br>";
            return;
        }

        $warnings .= "<i>Book edited.</i><br>";
    }

    function addNewAuthor($dbconnection, $newAuthorName) {
        global $warnings;

        $newAuthorName = trim($newAuthorName);

        if (preg_match('/,/', $newAuthorName)) {
            $warnings .= "<i>Incorrect author name.</i><br>";
            return;
        }

        if (!strlen($newAuthorName)) {
            $warnings .= "<i>Author name is required.</i><br>";
            return;
        }
        $query = "INSERT INTO author (name) VALUES ($1) RETURNING id";
        if ( !($result = pg_query_params($dbconnection, $query, array($newAuthorName)))) {
            $warnings .= "<i>Error adding author.</i><br>";
            return;
        }

        $warnings .= "<i>Author $newAuthorName added.</i><br>";

        return $newAuthorID = pg_fetch_row($result)[0];
    }

    function deleteAuthor($dbconnection, $authorID) {
        global $warnings;

        if (!ctype_digit($authorID)) {
            $warnings .= "<i>Invalid author ID.</i><br>";
            return;
        }
        if ( !($result = pg_query($dbconnection, "DELETE FROM author WHERE id = $authorID"))) {
            $warnings .= "<i>Error deleting author.</i><br>";
            return;
        }
        $warnings .= "<i>Author deleted.</i><br>";
    }

    function editAuthor($dbconnection, $authorID, $authorName) {
        global $warnings;

        $authorName = trim($authorName);

        if (preg_match('/,/', $authorName)) {
            $warnings .= "<i>Incorrect author name.</i><br>";
            return;
        }

        if (!ctype_digit($authorID)) {
            $warnings .= "<i>Invalid author ID.</i><br>";
            return;
        }
        if (!strlen($authorName)) {
            $warnings .= "<i>Author name is required.</i><br>";
            return;
        }
        $query = "UPDATE author SET name = $1 WHERE id = $2";
        if ( !($result = pg_query_params($dbconnection, $query, array($authorName, $authorID)))) {
            $warnings .= "<i>Error author editing</i><br>";
            return;
        }
        $warnings .= "<i>Author edited.</i><br>";
    }

    function addNewGenre($dbconnection, $newGenreTitle) {
        global $warnings;

        $newGenreTitle = trim($newGenreTitle);

        if (preg_match('/,/', $newGenreTitle)) {
            $warnings .= "<i>Incorrect genre title.</i><br>";
            return;
        }

        if (!strlen($newGenreTitle)) {
            $warnings .= "<i>Genre title is required.</i><br>";
            return;
        }
        $query = "INSERT INTO genre (title) VALUES ($1) RETURNING id";
        if ( !($result = pg_query_params($dbconnection, $query, array($newGenreTitle)))) {
            $warnings .= "<i>Error adding genre.</i><br>";
            return;
        }

        $warnings .= "<i>Genre $newGenreTitle added.</i><br>";

        return $newGenreID = pg_fetch_row($result)[0];
    }

    function deleteGenre($dbconnection, $genreID) {
        global $warnings;

        if (!ctype_digit($genreID)) {
            $warnings .= "<i>Invalid genre ID.</i><br>";
            return;
        }
        if ( !($result = pg_query($dbconnection, "DELETE FROM genre WHERE id = $genreID"))) {
            $warnings .= "<i>Error deleting genre.</i><br>";
            return;
        }
        $warnings .= "<i>Genre deleted.</i><br>";
    }

    function editGenre($dbconnection, $genreID, $genreTitle) {
        global $warnings;

        $genreTitle = trim($genreTitle);

        if (preg_match('/,/', $genreTitle)) {
            $warnings .= "<i>Incorrect genre title.</i><br>";
            return;
        }

        if (!ctype_digit($genreID)) {
            $warnings .= "<i>Invalid genre ID.</i><br>";
            return;
        }
        if (!strlen($genreTitle)) {
            $warnings .= "<i>Genre title is required.</i><br>";
            return;
        }
        $query = "UPDATE genre SET title = $1 WHERE id = $2";
        if ( !($result = pg_query_params($dbconnection, $query, array($genreTitle, $genreID)))) {
            $warnings .= "<i>Error genre editing</i><br>";
            return;
        }

        $warnings .= "<i>Genre edited.</i><br>";
    }

  ?>

<?php

    $host = 'localhost';
    $port = 5432;
    $dbname = 'library';
    $dbuser = 'postgres';
    $password = 'password';

    $dbconnection = pg_connect("host=$host port=$port dbname=$dbname user=$dbuser password=$password") or die;

    if (isset($_POST['addNewBook'])) {
        addNewBook($dbconnection, $_POST['newBookTitle'], $_POST['newBookAuthors'], $_POST['newBookGenres'], $_POST['newBookRating']);
    }
    if (isset($_POST['deleteBook'])) {
        deleteBook($dbconnection, $_POST['bookID']);
    }
    if (isset($_POST['editBook'])) {
        editBook($dbconnection, $_POST['bookID'], $_POST['bookTitle'], $_POST['bookRating']);
    }
    if (isset($_POST['addNewAuthor'])) {
        addNewAuthor($dbconnection, $_POST['newAuthorName']);
    }
    if (isset($_POST['deleteAuthor'])) {
        deleteAuthor($dbconnection, $_POST['authorID']);
    }
    if (isset($_POST['editAuthor'])) {
        editAuthor($dbconnection, $_POST['authorID'], $_POST['authorName']);
    }
    if (isset($_POST['addNewGenre'])) {
        addNewGenre($dbconnection, $_POST['newGenreTitle']);
    }
    if (isset($_POST['deleteGenre'])) {
        deleteGenre($dbconnection, $_POST['genreID']);
    }
    if (isset($_POST['editGenre'])) {
        editGenre($dbconnection, $_POST['genreID'], $_POST['genreTitle']);
    }

    $bookInfoContent = generateBookInfoContent($dbconnection);
    $authorInfoContent = generateAuthorInfoContent($dbconnection);
    $genreInfoContent = generateGenreInfoContent($dbconnection);

    pg_close($dbconnection);

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
        <style media="screen">
            div.mainContainer {
                width: 100%;
            }
            div.authorInfoContainer {
                float: left;
            }
            div.genreInfoContainer {
                float: right;
            }
            div.bookInfoContainer {
                max-width: 50%;
                margin: auto;
            }
            div.contentTable {
                display: table;
            }
            form.contentRow, span.contentRow {
                display: table-row;
            }
            form.contentRow:hover {
                background-color: #f1f1f1;
            }
            span.contentCell {
                display: table-cell;
                padding-left: 0.5em;
                padding-right: 0.5em;
            }
            input[type="text"] {
                max-width: 8em;
            }
            input[type="number"] {
                max-width: 3em;
            }
        </style>
    </head>
    <body>
        <div class="mainContainer">
            <div class="authorInfoContainer">

<?php

    echo $authorInfoContent;

 ?>

            </div>
            <div class="genreInfoContainer">

<?php

    echo $genreInfoContent;

 ?>

            </div>
            <div class="bookInfoContainer">

<?php

    echo $bookInfoContent;

 ?>

            </div>
        </div>

<?php
    global $warnings;

    echo "$warnings";

 ?>

    </body>
</html>
