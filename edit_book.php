<?php
include 'init.php';
include 'check_admin.php';
include 'views/header.php';
include 'views/aside.php';

$operation = 'Add';
$infoMessage = '';

$title = '';
$author = '';
$genre_id = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_POST && $_POST['submit']) {
    $operation = 'Edit';

    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre_id = $_POST['genre_id'];
    $target_dir = "images/";
    $cover = $target_dir . basename($_FILES["cover"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($cover, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["cover"]["tmp_name"]);
        if ($check !== false) {
            echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($cover)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["cover"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (
        $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif"
    ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["cover"]["tmp_name"], $cover)) {
            echo "The file " . htmlspecialchars(basename($_FILES["cover"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    if ($id > 0) { // Edit
        $query = 'UPDATE Books SET
                      title = "' . $title . '", author = "' . $author . '", genre_id = ' . $genre_id . '
                      WHERE id = ' . $id;

        $operation = 'Edit';
    } else { // Add
        $query = 'INSERT INTO Books (title, author, genre_id) VALUES
                      ("' . $title . '", "' . $author . '", ' . $genre_id . ')';

        $operation = 'Add';
    }

    $mysqli->query($query);

    $id = $id ? $id : $mysqli->insert_id;

    $infoMessage = 'Book successfully ' . ($operation == 'Edit' ? 'edited' : 'added') . '!';
} else if ($id) {
    $query = 'SELECT * FROM Books WHERE id = ' . $id;
    $result = $mysqli->query($query);

    if ($row = $result->fetch_assoc()) {
        $operation = 'Edit';

        $title = $row['title'];
        $author = $row['author'];
        $genre_id = $row['genre_id'];
    }
}

echo

'<section class="col-md-10">
        <form action="edit_book.php" method="post" class="form-horizontal">
            <input type="hidden" name="id" value="' . $id . '">
            <fieldset>
                <legend>' . $operation . ' Book</legend>';
if ($infoMessage) {
    echo '<div class="alert alert-success" role="alert">' . $infoMessage . '</div>';
}
echo

'<div class="form-group">
<p> punten kang/teh uploadnya belum jalan :D jadi manual lewat db</p>
                    <label for="title" class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-8">
                        <input type="text" name="title" id="title" placeholder="Title" class="form-control" value="' . $title . '">
                    </div>
                </div>
                <div class="form-group">
                    <label for="author" class="col-sm-2 control-label">Author</label>
                    <div class="col-sm-8">
                        <input type="text" name="author" id="author" placeholder="Author" class="form-control" value="' . $author . '">
                    </div>
                </div>
                <form action="upload.php" method="post" enctype="multipart/form-data">
                                 Select image to upload:
             <input type="file" name="cover" id="cover">
             <input type="submit" value="Upload Image" name="submit">
                </form>
                <div class="form-group">
                    <label for="genre_id" class="col-sm-2 control-label">Genre</label>
                    <div class="col-sm-8">
                        <select name="genre_id" id="genre_id" class="form-control">';
$query = 'SELECT * FROM Genres ORDER BY name';
$result = $mysqli->query($query);
while ($row = $result->fetch_assoc()) { ?>
    <option value="<?= $row['id'] ?>" <?= ($row['id'] == $genre_id ? 'selected' : '') ?>><?= $row['name'] ?></option>
<?php }
echo
'</select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" name="submit" value="' . $operation . '" class="btn btn-primary">
                    </div>
                </div>
            </fielset>
        </form>
    </section>';

include 'views/footer.php';
?>