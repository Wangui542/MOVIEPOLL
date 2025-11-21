<?php
session_start();
require_once __DIR__ . "/db_connect.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Delete comment
    if (isset($_POST['delete_comment_id'])) {
        $delete_comment_id = intval($_POST['delete_comment_id']);
        $deleteStmt = $conn->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $delete_comment_id, $user_id);
        if ($deleteStmt->execute()) {
            // Refresh the page after successful deletion
            header("Location: results.php");
            exit();
        }
        $deleteStmt->close();
    }

    // ✅ Add comment
    if (isset($_POST['comment_text']) && isset($_POST['movie_id'])) {
        $comment_text = trim($_POST['comment_text']);
        $movie_id = intval($_POST['movie_id']);

        if (!empty($comment_text)) {
            $stmt = $conn->prepare("INSERT INTO comments (movie_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $movie_id, $user_id, $comment_text);
            $stmt->execute();
            $stmt->close();
            
            // Refresh to show the new comment
            header("Location: results.php");
            exit();
        }
    }
}

// Fetch ALL movies from database
$movies_result = $conn->query("SELECT * FROM movies ORDER BY votes DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🎬 Movie Poll Results</title>
<style>
body {font-family: Arial, sans-serif; background: #0a0a0a; color: white; margin:0; padding:0;}
nav {background-color: #111; padding: 15px; display: flex; justify-content: center; gap: 40px;}
nav a {color: white; text-decoration: none; font-weight: bold;}
nav a:hover {color: #00bfff;}
h1 {text-align: center; margin-top: 20px; color: #00bfff;}
.movies-grid {display: flex; justify-content: center; gap: 25px; flex-wrap: wrap; padding: 30px;}
.movie-card {background-color: #1a1a1a; width: 300px; border-radius: 12px; padding: 15px; box-shadow: 0 0 10px rgba(0,0,0,0.5);}
.movie-card img {width: 100%; height: 200px; border-radius: 10px; object-fit: cover;}
.comment-section {margin-top: 15px;}
.comment {background-color: rgba(255,255,255,0.1); padding: 8px; border-radius: 8px; margin-top: 5px; display:flex; justify-content: space-between; align-items: center;}
textarea {width: 100%; border-radius: 8px; padding: 8px; resize: none;}
button {margin-top: 8px; padding: 8px 16px; border: none; border-radius: 8px; background-color: #00bfff; color: black; font-weight: bold; cursor: pointer;}
button:hover {background-color: #33ccff;}
.delete-btn {background: red; color: white; border: none; padding: 5px 10px; border-radius: 8px; cursor: pointer; font-size: 12px; margin-left:10px;}
.delete-btn:hover {background: darkred;}
</style>
</head>
<body>

<nav>
    <a href="home.php">Home</a>
    <a href="about.php">About</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
</nav>

<h1>🎬 Movie Poll Results</h1>

<div class="movies-grid">

    <?php
    // Display ALL movies from database
    if ($movies_result && $movies_result->num_rows > 0) {
        while ($movie = $movies_result->fetch_assoc()) {
            $movie_id = $movie['id'];
            $votes = $movie['votes'];
            ?>
            <div class="movie-card">
                <h2><?= htmlspecialchars($movie['title']) ?></h2>
                <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <p><?= htmlspecialchars($movie['description']) ?></p>
                <p><strong>Total Votes:</strong> <?= $votes ?></p>

                <?php
                // Fetch comments for this movie
                $commentQuery = $conn->prepare("
                    SELECT c.id, c.comment_text, u.fullname, c.user_id
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.movie_id = ?
                    ORDER BY c.id DESC
                ");
                $commentQuery->bind_param("i", $movie_id);
                $commentQuery->execute();
                $commentsResult = $commentQuery->get_result();
                ?>
                <div class="comment-section">
                    <h3>Comments</h3>
                    <?php
                    if ($commentsResult->num_rows > 0) {
                        while ($comment = $commentsResult->fetch_assoc()) {
                            echo "<div class='comment'>
                                    <span><strong>" . htmlspecialchars($comment['fullname']) . ":</strong> " . htmlspecialchars($comment['comment_text']) . "</span>";
                            // Show delete button if the comment belongs to the logged-in user
                            if ($comment['user_id'] == $_SESSION['user_id']) {
                                echo "<form method='POST' style='display:inline'>
                                        <input type='hidden' name='delete_comment_id' value='" . $comment['id'] . "'>
                                        <button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this comment?\")'>Delete</button>
                                      </form>";
                            }
                            echo "</div>";
                        }
                    } else {
                        echo "<p>No comments yet.</p>";
                    }
                    $commentQuery->close();
                    ?>
                    <form method="POST" action="">
                        <input type="hidden" name="movie_id" value="<?= $movie_id ?>">
                        <textarea name="comment_text" placeholder="Write a comment..." required></textarea><br>
                        <button type="submit">Post Comment</button>
                    </form>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p>No movies found.</p>";
    }
    ?>

</div>

</body>
</html>



























