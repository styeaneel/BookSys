<?php
session_start();
include('connection.php');

$user = null;

// Fetch user details if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}

// Handle user update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_type = $_POST['role'];

    $query = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', user_type='$user_type' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: edit_user.php?id=$id&success=1");
        exit();
    } else {
        echo "<p class='message'>Error updating record: " . mysqli_error($conn) . "</p>";
    }
}

// Handle user search
$search = "";
$searchResults = [];
if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    $query = "SELECT * FROM users WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR email LIKE '%$search%'";
    $searchResults = mysqli_query($conn, $query);
} else {
    $query = "SELECT * FROM users";
    $searchResults = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <style>

        body { font-family: 'Poppins', sans-serif; background-color: #F9F9F9; }
        .container { max-width: 800px; margin: auto; padding: 20px; }
        .form-container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        label { font-weight: bold; color: #5A2D82; display: block; margin-bottom: 5px; }
        input, select, button { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; }
        button { background-color: #5A2D82; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #482366; }
        .message { text-align: center; margin-top: 10px; color: red; }
        .search-bar { margin-bottom: 20px; display: flex; gap: 10px; }
        .user-list { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
        .user-list table { width: 100%; border-collapse: collapse; }
        .user-list th, .user-list td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .user-list a { color: #5A2D82; text-decoration: none; font-weight: bold; }
        .user-list a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>

    <!-- User Search -->
    <div class="form-container">
        <h3>Search Users</h3>
        <form method="POST" class="search-bar">
            <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- User List -->
    <div class="user-list">
        <h3>User List</h3>
        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($searchResults)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_type']); ?></td>
                    <td><a href="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Edit User Form -->
    <?php if ($user) : ?>
        <div class="form-container">
            <h3>Editing: <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <label>Role:</label>
                <select name="role">
                    <option value="student" <?php echo $user['user_type'] == 'student' ? 'selected' : ''; ?>>Student</option>
                    <option value="staff" <?php echo $user['user_type'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                </select>
                <button type="submit" name="update">Update User</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
