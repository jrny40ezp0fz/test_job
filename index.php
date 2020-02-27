<?php

    function addNewBook($db_connection, $title, $authors, $genres, $rating) {
        $authorsID = array();
        $genresID  = array();

        for ($i = 0; $i < count($authors); $i++) {
            if ( !($result = pg_query($db_connection, "SELECT id FROM authors WHERE name = '" . $authors[$i] . "'"))) {
                exit;
            }
            if (!pg_num_rows($result)) {
                echo "Add author <i>$authors[$i]</i> first";
                return;
            }
            $authorsID[$i] = pg_fetch_row($result)[0];
        }

        for ($i = 0; $i < count($genres); $i++) {
            if ( !($result = pg_query($db_connection, "SELECT id FROM genres WHERE title = '" . $genres[$i] . "'"))) {
                exit;
            }
            if (!pg_num_rows($result)) {
                echo "Add genre <i>$genres[$i]</i> first";
                return;
            }
            $genresID[$i] = pg_fetch_row($result)[0];
        }

        $bookID = pg_fetch_row(pg_query($db_connection, "INSERT INTO books (title, rating) VALUES ('" . $title ."', $rating) RETURNING id"))[0];

        for ($i = 0; $i < count($authorsID); $i++) {
            if ( !($result = pg_query($db_connection, "INSERT INTO book_author (book_id, author_id) VALUES ($bookID, $authorsID[$i])"))) {
                exit;
            }
        }

        for ($i = 0; $i < count($genresID); $i++) {
            if ( !($result = pg_query($db_connection, "INSERT INTO book_genre (book_id, genre_id) VALUES ($bookID, $genresID[$i])"))) {
                exit;
            }
        }
    }

 ?>

<?php

    $db_connection = pg_connect("host=localhost port=5432 dbname=libdb user=postgres password=password") or die();


    if (isset($_POST['deleteBook'])) {
        pg_query($db_connection, "DELETE FROM books WHERE id = " . $_POST['bookID']) or die();
    }

    if (isset($_POST['addNewBook'])) {
        $title = $_POST['newBookTitle'];
        $authors = explode(', ', $_POST['newBookAuthors']);
        $genres = explode(', ', $_POST['newBookGenres']);
        $rating = $_POST['newBookRating'];

        addNewBook($db_connection, $title, $authors, $genres, $rating);
    }

    if (isset($_POST['editBookInfo'])) {
        pg_query($db_connection, "UPDATE books SET title = '" . $_POST['bookTitle'] . "', rating = " . $_POST['bookRating'] . " WHERE id = " . $_POST['bookID']) or die();
    }

    if (isset($_POST['editAuthor'])) {
        pg_query($db_connection, "UPDATE authors SET name = '" . $_POST['authorName'] ."' WHERE id = " . $_POST['authorID']) or die();
    }

    if (isset($_POST['deleteAuthor'])) {
        pg_query($db_connection, "DELETE FROM authors WHERE id = " . $_POST['authorID']) or die();
    }

    if (isset($_POST['addNewAuthor'])) {
        pg_query($db_connection, "INSERT INTO authors (name) VALUES ('" . $_POST['newAuthorName'] . "')") or die();
    }

    if (isset($_POST['editGenre'])) {
        pg_query($db_connection, "UPDATE genres SET title = '" . $_POST['genreTitle'] ."' WHERE id = " . $_POST['genreID']) or die();
    }

    if (isset($_POST['deleteGenre'])) {
        pg_query($db_connection, "DELETE FROM genres WHERE id = " . $_POST['genreID']) or die();
    }

    if (isset($_POST['addNewGenre'])) {
        pg_query($db_connection, "INSERT INTO genres (title) VALUES ('" . $_POST['newGenreTitle'] . "')") or die();
    }

    $result = pg_query($db_connection, "SELECT * FROM books") or die();

    $booksInfoTableContent = '';

    while ( ($row = pg_fetch_row($result))) {
        $booksInfoTableContent .= '<tr><form class="" action="index.php" method="post">';
        $booksInfoTableContent .= "<td>$row[0]<td>";
        $booksInfoTableContent .= '<td> <input type="text" name="bookTitle" value="' . $row[1] . '" required> </td>';
        $booksInfoTableContent .= '<td> <input type="number" min="0" max="100" name="bookRating" value="' . $row[2] . '" required> </td>';
        $booksInfoTableContent .= '<td> <input id="" type="submit" name="deleteBook" value="-"></td>';
        $booksInfoTableContent .= '<td> <input id="" type="submit" name="editBookInfo" value="edit"></td>';
        $booksInfoTableContent .=  '<input type="hidden" name="bookID" value="' . $row[0] . '">';
        $booksInfoTableContent .= '</form></tr>';
    }

    $booksInfoTableContent .= '<br>';

    $result = pg_query($db_connection, "SELECT * FROM books_view") or die();

    $booksFullInfoTableContent = '';

    while ( ($row = pg_fetch_row($result))) {
        $booksFullInfoTableContent .= '<tr><form class="" action="index.php" method="post">';
        for ($i = 0; $i < count($row); $i++)
            $booksFullInfoTableContent .=  "<td>$row[$i]</td>";

        $booksFullInfoTableContent .= '<td><input id="" type="submit" name="deleteBook" value="-"></td>';
        $booksFullInfoTableContent .=  '<input type="hidden" name="bookID" value="' . $row[0] . '">';
        $booksFullInfoTableContent .= '</form></tr>';
    }

    $booksFullInfoTableContent .= '<br>';

    $result = pg_query($db_connection, "SELECT * FROM authors_view") or die();

    $authorsInfoTableContent = '';

    while ( ($row = pg_fetch_row($result))) {
        $authorsInfoTableContent .= '<form class="" action="index.php" method="post"><tr>';
        $authorsInfoTableContent .= '<td>' . $row[0] . '</td>';
        $authorsInfoTableContent .= '<td> <input type="text" name="authorName" value="' . $row[1] . '" required> </td>';
        $authorsInfoTableContent .= '<td>' . $row[2] . '</td>';
        $authorsInfoTableContent .= '<td colspan="2"><input id="" type="submit" name="deleteAuthor" value="-"></td>';
        $authorsInfoTableContent .= '<td colspan="2"><input id="" type="submit" name="editAuthor" value="edit"></td>';
        $authorsInfoTableContent .=  '<input type="hidden" name="authorID" value="' . $row[0] . '">';
        $authorsInfoTableContent .= '</form></tr>';
    }

    $result = pg_query($db_connection, "SELECT * FROM genres") or die();

    $genresInfoTableContent = '';

    while ( ($row = pg_fetch_row($result))) {
        $genresInfoTableContent .= '<form class="" action="index.php" method="post"><tr>';
        $genresInfoTableContent .= '<td>' . $row[0] . '</td>';
        $genresInfoTableContent .= '<td> <input type="text" name="genreTitle" value="' . $row[1] . '" required> </td>';
        $genresInfoTableContent .= '<td><input id="" type="submit" name="deleteGenre" value="-"></td>';
        $genresInfoTableContent .= '<td><input id="" type="submit" name="editGenre" value="edit"></td>';
        $genresInfoTableContent .=  '<input type="hidden" name="genreID" value="' . $row[0] . '">';
        $genresInfoTableContent .= '</form></tr>';
    }

    $genresInfoTableContent .= '<br>';
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Library management</title>
        <style media="screen">
            table {
                text-align: center;
                border: 1px solid rgb(51, 51, 51);
            }
        </style>
    </head>
    <body>

        <b> <i>Books list</i> </b>

        <table id="booksInfoTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>

<?php

    echo $booksInfoTableContent;

 ?>

            </tbody>
        </table>

        <b> <i>Books info</i> </b>

        <table id="booksFullInfoTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Genres</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>
<?php

    echo $booksFullInfoTableContent;

 ?>
            </tbody>
            <tfoot>
                <form class="" action="index.php" method="post">
                    <tr>
                        <td colspan="2"> <input type="text" placeholder="Title"  name="newBookTitle" value="" required> </td>
                        <td> <input type="text" placeholder="Authors (comma separated)" name="newBookAuthors" value="" required> </td>
                        <td> <input type="text" placeholder="Genres (comma separated)" name="newBookGenres" value="" required> </td>
                        <td> <input type="number" min="0" max="100" placeholder="0" name="newBookRating" value="" required> </td>
                        <td> <input type="submit" name="addNewBook" value="+"> </td>
                    </tr>
                </form>
            </tfoot>
        </table>

        <br>

        <b> <i>Authors info</i> </b>

        <table id="authorsInfoTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Author</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>

<?php

    echo $authorsInfoTableContent;

 ?>

            </tbody>
            <tfoot>
                <form class="" action="index.php" method="post">
                    <tr>
                        <td colspan="3"> <input type="text" placeholder="Name" name="newAuthorName" value="" required> </td>
                        <td> <input type="submit" name="addNewAuthor" value="+"> </td>
                    </tr>
                </form>
            </tfoot>
        </table>

        <b> <i>Genres list</i> </b>

        <table id="genresInfoTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Genre</th>
                </tr>
            </thead>
            <tbody>

<?php

    echo $genresInfoTableContent;

 ?>

            </tbody>
            <tfoot>
                <form class="" action="index.php" method="post">
                    <tr>
                        <td colspan="2"> <input type="text" placeholder="Title" name="newGenreTitle" value="" required> </td>
                        <td> <input type="submit" name="addNewGenre" value="+"> </td>
                    </tr>
                </form>
            </tfoot>
        </table>

    </body>
</html>

<?php

    pg_close($db_connection);

 ?>
