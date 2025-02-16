<?php
session_start();

if (isset($_SESSION['user_name'])) {
    $user_name = $_SESSION['user_name'];
} else {
    $user_name = "Guest";
}

$conn = new mysqli("localhost", "root", "", "booksys");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$genre_filter = isset($_GET['genre']) ? $_GET['genre'] : '';
$availability_filter = isset($_GET['availability']) ? $_GET['availability'] : '';
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'ASC';

$query = "SELECT * FROM books WHERE 1";
if (!empty($genre_filter)) {
    $query .= " AND genre = '" . $conn->real_escape_string($genre_filter) . "'";
}
if ($availability_filter === "available") {
    $query .= " AND status = 'Available'";
} elseif ($availability_filter === "borrowed") {
    $query .= " AND status = 'Borrowed'";
}
$query .= " ORDER BY title $sort_order";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Books</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .filter-container { margin-bottom: 20px; }
        .filter-container select, .filter-container button { margin-right: 10px; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #5A2D82; color: white; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; text-decoration: none; color: white; }
        .edit-btn { background-color: #FFA500; }
        .add-btn { background-color: #28A745; margin-bottom: 10px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Books</h2>
        <a href="add_book.php" class="btn add-btn">➕ Add New Book</a>
        <form method="GET" class="filter-container">
            <label>Genre:</label>
            <select name="genre">
                <option value="">All</option>
                <option value="Fiction" <?= $genre_filter == "Fiction" ? "selected" : "" ?>>Fiction</option>
                <option value="Non-Fiction" <?= $genre_filter == "Non-Fiction" ? "selected" : "" ?>>Non-Fiction</option>
                <option value="Fantasy" <?= $genre_filter == "Fantasy" ? "selected" : "" ?>>Fantasy</option>
                <option value="Mystery & Thriller" <?= $genre_filter == "Mystery & Thriller" ? "selected" : "" ?>>Mystery & Thriller</option>
                <option value="Action & Adventure" <?= $genre_filter == "Action & Adventure" ? "selected" : "" ?>>Action & Adventure</option>
                <option value="Historical" <?= $genre_filter == "Historical" ? "selected" : "" ?>>Historical</option>
                <option value="Politics" <?= $genre_filter == "Politics" ? "selected" : "" ?>>Politics</option>
            </select>
            
            <label>Availability:</label>
            <select name="availability">
                <option value="">All</option>
                <option value="available" <?= $availability_filter == "available" ? "selected" : "" ?>>Available</option>
                <option value="borrowed" <?= $availability_filter == "borrowed" ? "selected" : "" ?>>Borrowed</option>
            </select>
            
            <label>Sort:</label>
            <select name="sort">
                <option value="ASC" <?= $sort_order == "ASC" ? "selected" : "" ?>>A-Z</option>
                <option value="DESC" <?= $sort_order == "DESC" ? "selected" : "" ?>>Z-A</option>
            </select>
            
            <button type="submit">Apply Filters</button>
        </form>
        
        <div class="table-container">
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['author']) ?></td>
                        <td><?= htmlspecialchars($row['genre']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <a href="edit_book.php?id=<?= $row['id'] ?>" class="btn edit-btn">✏️ Edit</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
