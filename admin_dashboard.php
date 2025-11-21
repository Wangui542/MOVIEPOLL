<?php
session_start();
require_once "db_connect.php";

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle Add Movie
if (isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $poster = $_POST['poster'];
    $description = $_POST['description'];
    $conn->query("INSERT INTO movies (title, poster, description) VALUES ('$title','$poster','$description')");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle Edit Movie
if (isset($_POST['edit_movie'])) {
    $id = intval($_POST['movie_id']);
    $title = $_POST['title'];
    $poster = $_POST['poster'];
    $description = $_POST['description'];
    $conn->query("UPDATE movies SET title='$title', poster='$poster', description='$description' WHERE id=$id");
    header("Location: admin_dashboard.php");
    exit();
}

// Handle Delete Movie
if (isset($_GET['delete_movie'])) {
    $id = intval($_GET['delete_movie']);
    $conn->query("DELETE FROM movies WHERE id = $id");
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch movies
$movies = $conn->query("SELECT * FROM movies ORDER BY id ASC");

// Fetch users
$users = $conn->query("SELECT * FROM users ORDER BY id ASC");

// Fetch votes
$votes = $conn->query("SELECT v.id, u.fullname, m.title FROM votes v 
                       JOIN users u ON v.user_id = u.id 
                       JOIN movies m ON v.movie_id = m.id");

// Fetch comments
$comments = $conn->query("SELECT c.comment_text, c.created_at, u.fullname, m.title 
                          FROM comments c
                          JOIN users u ON c.user_id = u.id
                          JOIN movies m ON c.movie_id = m.id
                          ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body {font-family: Arial, sans-serif; background: #111; color: white; margin:0; padding:0;}
nav {background: skyblue; padding: 15px; display:flex; justify-content: space-between; align-items:center;}
nav h2 {color: #000; margin:0;}
nav a {color: #000; text-decoration:none; margin-left:15px; font-weight:bold;}
.container {padding:20px;}
section {margin-bottom:40px;}
table {width:100%; border-collapse: collapse; margin-top:10px;}
table, th, td {border:1px solid #555;}
th, td {padding:10px; text-align:left;}
th {background: skyblue; color:#000;}
button {padding:5px 10px; cursor:pointer; border:none; border-radius:5px;}
.add-form input, .add-form textarea {width:100%; padding:5px; margin:5px 0;}
.add-form button {background: skyblue; color:#000;}
.edit-form input, .edit-form textarea {width:100%; padding:5px; margin:5px 0;}
.edit-form button {background: orange; color:#000;}
.movie-poster {width:100px; height:auto;}
</style>
</head>
<body>

<nav>
  <h2>Admin Dashboard</h2>
  <div>
    <a href="#">Dashboard</a> <!-- stays on same page -->
    <a href="logout.php">Logout</a>
  </div>
</nav>

<div class="container">

<!-- Movies Section -->
<section>
<h3>🎬 Movies Management</h3>
<table>
<tr><th>ID</th><th>Title</th><th>Poster</th><th>Description</th><th>Votes</th><th>Action</th></tr>
<?php while($m = $movies->fetch_assoc()): ?>
<tr>
<td><?= $m['id'] ?></td>
<td><?= htmlspecialchars($m['title']) ?></td>
<td><img src="<?= htmlspecialchars($m['poster']) ?>" class="movie-poster"></td>
<td><?= htmlspecialchars($m['description']) ?></td>
<td><?= $m['votes'] ?></td>
<td>
<a href="admin_dashboard.php?delete_movie=<?= $m['id'] ?>"><button style="background:red;">Delete</button></a>
<button onclick="openEditModal(<?= $m['id'] ?>, '<?= htmlspecialchars($m['title']) ?>', '<?= htmlspecialchars($m['poster']) ?>', '<?= htmlspecialchars($m['description']) ?>')" style="background:orange;">Edit</button>
</td>
</tr>
<?php endwhile; ?>
</table>

<h4>Add New Movie</h4>
<form method="POST" class="add-form">
<input type="text" name="title" placeholder="Movie Title" required>
<input type="text" name="poster" placeholder="Poster filename (just the name e.g. bet.jpg)" required>
<textarea name="description" placeholder="Movie Description" required></textarea>
<button type="submit" name="add_movie">Add Movie</button>
</form>
</section>

<!-- Users Section -->
<section>
<h3>👥 Users</h3>
<table>
<tr><th>ID</th><th>Full Name</th><th>Admin?</th></tr>
<?php while($u = $users->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td>
<td><?= htmlspecialchars($u['fullname']) ?></td>
<td><?= $u['is_admin'] ? "Yes" : "No" ?></td>
</tr>
<?php endwhile; ?>
</table>
</section>

<!-- Votes Section -->
<section>
<h3>🗳️ Votes</h3>
<table>
<tr><th>ID</th><th>User</th><th>Movie Voted</th></tr>
<?php while($v = $votes->fetch_assoc()): ?>
<tr>
<td><?= $v['id'] ?></td>
<td><?= htmlspecialchars($v['fullname']) ?></td>
<td><?= htmlspecialchars($v['title']) ?></td>
</tr>
<?php endwhile; ?>
</table>
</section>

<!-- Comments Section -->
<section>
<h3>💬 Comments</h3>
<table>
<tr><th>User</th><th>Movie</th><th>Comment</th><th>Date</th></tr>
<?php while($c = $comments->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($c['fullname']) ?></td>
<td><?= htmlspecialchars($c['title']) ?></td>
<td><?= htmlspecialchars($c['comment_text']) ?></td>
<td><?= htmlspecialchars($c['created_at']) ?></td>
</tr>
<?php endwhile; ?>
</table>
</section>

<!-- Edit Movie Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; justify-content:center; align-items:center;">
<div style="background:#333; padding:20px; border-radius:10px; width:500px;">
<h3>Edit Movie</h3>
<form method="POST" class="edit-form">
<input type="hidden" name="movie_id" id="edit_movie_id">
<input type="text" name="title" id="edit_title" placeholder="Movie Title" required>
<input type="text" name="poster" id="edit_poster" placeholder="Poster filename" required>
<textarea name="description" id="edit_description" placeholder="Movie Description" required></textarea>
<div style="display:flex; gap:10px; margin-top:10px;">
<button type="submit" name="edit_movie" style="background:orange;">Update Movie</button>
<button type="button" onclick="closeEditModal()" style="background:gray;">Cancel</button>
</div>
</form>
</div>
</div>

<script>
function openEditModal(id, title, poster, description) {
    document.getElementById('edit_movie_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_poster').value = poster;
    document.getElementById('edit_description').value = description;
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

</div>
</body>
</html>





