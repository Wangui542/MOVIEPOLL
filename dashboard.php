<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "moviepoll_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the user has already voted
$voted_check = $conn->prepare("SELECT * FROM votes WHERE user_id = ?");
$voted_check->bind_param("i", $user_id);
$voted_check->execute();
$voted_result = $voted_check->get_result();
$has_voted = $voted_result->num_rows > 0;

// Handle vote submission
if (isset($_POST['vote']) && !$has_voted) {
    $movie_id = $_POST['movie_id'];
    $title = $_POST['movie_title'];
    $poster = $_POST['movie_poster'];
    $description = $_POST['movie_description'] ?? '';

    // Check if movie exists in DB
    $check = $conn->prepare("SELECT id FROM movies WHERE id = ?");
    $check->bind_param("s", $movie_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows == 0) {
        // Insert movie added via dashboard
        $insert = $conn->prepare("INSERT INTO movies (id, title, poster, description, votes) VALUES (?, ?, ?, ?, 0)");
        $insert->bind_param("ssss", $movie_id, $title, $poster, $description);
        $insert->execute();
    }

    // Record vote
    $stmt = $conn->prepare("INSERT INTO votes (user_id, movie_id) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $movie_id);
    $stmt->execute();

    // Increment movie votes
    $conn->query("UPDATE movies SET votes = votes + 1 WHERE id = '$movie_id'");

    header("Location: results.php");
    exit();
}

// Fetch ALL movies from database
$db_movies = $conn->query("SELECT * FROM movies");

// Create an array to store movie IDs that already exist in DB
$existing_movie_ids = [];
while ($movie = $db_movies->fetch_assoc()) {
    $existing_movie_ids[$movie['id']] = true;
}

// Define fixed movies
$fixed_movies = [
    ["id"=>"1", "title"=>"Night Agent", "poster"=>"images/night_agent.jpg", "description"=>"An espionage thriller where a secret agent must prevent a major attack."],
    ["id"=>"2", "title"=>"Billionaires Bunker", "poster"=>"images/billionaires_bunker.jpg", "description"=>"A thrilling tale of the world's richest trapped in a secret bunker."],
    ["id"=>"3", "title"=>"Recruit", "poster"=>"images/recruit.jpg", "description"=>"An intense story of a recruit facing impossible choices during training."]
];

// Combine fixed movies with database movies, avoiding duplicates
$all_movies = $fixed_movies;

// Add database movies that aren't in fixed movies
$db_movies = $conn->query("SELECT * FROM movies");
while ($movie = $db_movies->fetch_assoc()) {
    $is_fixed_movie = false;
    foreach ($fixed_movies as $fixed) {
        if ($movie['id'] == $fixed['id']) {
            $is_fixed_movie = true;
            break;
        }
    }
    if (!$is_fixed_movie) {
        $all_movies[] = $movie;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎬 Movie Poll Dashboard</title>
<style>
body { font-family: 'Poppins', sans-serif; background: #0d0d0d; color: #fff; margin: 0; padding: 0; }
nav { display:flex; justify-content: space-between; align-items: center; background: skyblue; padding: 15px 40px; }
nav h2 { color: #000; margin:0; }
nav ul { list-style:none; display:flex; gap:20px; margin:0; padding:0; }
nav ul li a { color:#000; text-decoration:none; font-weight:500; }
nav ul li a:hover { text-decoration:underline; }
.user-info { display:flex; align-items:center; gap:15px; }
.user-info span { color:#000; font-weight:600; }
.logout-btn, .results-btn { background:#000; color:#fff; text-decoration:none; padding:8px 16px; border-radius:20px; transition: background 0.3s ease; }
.logout-btn:hover, .results-btn:hover { background:#333; }
.results-btn { background:#222; }
.search-bar { text-align:center; margin:25px 0; }
.search-bar input { width:60%; padding:12px; border-radius:25px; border:none; font-size:16px; }
.movie-container { display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:25px; padding:20px; }
.movie-card { background:#1e1e1e; border-radius:15px; text-align:center; overflow:hidden; transition: transform 0.3s ease; }
.movie-card:hover { transform: translateY(-8px); }
.movie-card img { width:100%; height:350px; object-fit:cover; }
.movie-card h3 { margin:15px 0 5px; color:skyblue; }
.movie-card p.description { margin:0 15px 15px; font-size:14px; color:#ccc; }
.vote-btn { background: skyblue; color:#000; border:none; border-radius:25px; padding:10px 20px; margin-bottom:15px; cursor:pointer; transition:background 0.3s ease; }
.vote-btn:hover { background:deepskyblue; }
.disabled-btn { background:gray; cursor:not-allowed; }
</style>
</head>
<body>
<nav>
<h2>🎬 Movie Poll</h2>
<ul>
<li><a href="home.php">Home</a></li>
<li><a href="about.php">About</a></li>
<li><a href="dashboard.php">Dashboard</a></li>
</ul>
<div class="user-info">
<span>👤 <?= htmlspecialchars($_SESSION['fullname']) ?></span>
<a href="results.php" class="results-btn">View Results</a>
<a href="logout.php" class="logout-btn">Logout</a>
</div>
</nav>

<!-- Search bar -->
<div class="search-bar">
<input type="text" id="search" placeholder="🔍 Search for a movie..." />
</div>

<!-- All movies (fixed + admin-added without duplicates) -->
<section class="movie-container">
<?php foreach($all_movies as $movie): ?>
<div class="movie-card">
<img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
<h3><?= htmlspecialchars($movie['title']) ?></h3>
<p class="description"><?= htmlspecialchars($movie['description']) ?></p>
<?php if ($has_voted): ?>
<button class="vote-btn disabled-btn" disabled>✅ Already Voted</button>
<?php else: ?>
<form method="POST">
<input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
<input type="hidden" name="movie_title" value="<?= htmlspecialchars($movie['title']) ?>">
<input type="hidden" name="movie_poster" value="<?= htmlspecialchars($movie['poster']) ?>">
<input type="hidden" name="movie_description" value="<?= htmlspecialchars($movie['description']) ?>">
<button type="submit" name="vote" class="vote-btn">Vote</button>
</form>
<?php endif; ?>
</div>
<?php endforeach; ?>
</section>

<!-- TMDb Search Results -->
<section id="tmdb-results">
<h2 style="text-align:center; color:skyblue;">🔎 Search Results</h2>
<div class="movie-container" id="tmdb-container"></div>
</section>

<script>
const API_KEY = "06f1eb9a3fd8f796fe3eb022c961bb87";
const IMAGE_BASE = "https://image.tmdb.org/t/p/w500";

document.getElementById("search").addEventListener("keyup", function() {
    const query = this.value.trim();
    const resultContainer = document.getElementById("tmdb-container");

    if(query.length < 3) {
        resultContainer.innerHTML = "";
        return;
    }

    fetch(`https://api.themoviedb.org/3/search/movie?api_key=${API_KEY}&query=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
        resultContainer.innerHTML = "";
        if(!data.results || data.results.length===0){
            resultContainer.innerHTML = "<p style='text-align:center;'>No results found on TMDb.</p>";
            return;
        }

        data.results.slice(0,9).forEach(movie => {
            const poster = movie.poster_path ? IMAGE_BASE + movie.poster_path : "images/no-poster.png";
            const overview = movie.overview ? movie.overview.substring(0,100) + "..." : "No description available.";
            const voted = <?= json_encode($has_voted) ?>;
            const disabled = voted ? "disabled" : "";
            const buttonText = voted ? "✅ Already Voted" : "Vote";

            const card = `
            <div class="movie-card">
            <img src="${poster}" alt="${movie.title}">
            <h3>${movie.title}</h3>
            <p class="description">${overview}</p>
            <form method="POST">
                <input type="hidden" name="movie_id" value="${movie.id}">
                <input type="hidden" name="movie_title" value="${movie.title}">
                <input type="hidden" name="movie_poster" value="${poster}">
                <input type="hidden" name="movie_description" value="${overview}">
                <button type="submit" name="vote" class="vote-btn" ${disabled}>${buttonText}</button>
            </form>
            </div>
            `;
            resultContainer.innerHTML += card;
        });
    })
    .catch(err => {
        console.error(err);
        document.getElementById("tmdb-container").innerHTML = "<p style='text-align:center;'>Failed to load movies.</p>";
    });
});
</script>
</body>
</html>










































