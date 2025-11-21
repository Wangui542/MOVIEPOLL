<?php
// search.php

$api_key = "06f1eb9a3fd8f796fe3eb022c961bb87"; // ✅ Your TMDb API Key

if (isset($_GET['query'])) {
    $query = urlencode($_GET['query']);
    $url = "https://api.themoviedb.org/3/search/movie?api_key=$api_key&query=$query";

    $response = file_get_contents($url);
    $data = json_decode($response, true);

    echo "<h1 style='text-align:center;'>Search Results</h1>";

    if (!empty($data['results'])) {
        foreach ($data['results'] as $movie) {
            $title = htmlspecialchars($movie['title']);
            $overview = htmlspecialchars($movie['overview']);
            $poster = isset($movie['poster_path']) 
                ? 'https://image.tmdb.org/t/p/w300' . $movie['poster_path'] 
                : 'https://via.placeholder.com/300x450?text=No+Image';

            echo "
            <div style='background:#1f2833;color:#fff;border-radius:10px;
                        width:300px;margin:20px auto;padding:15px;text-align:center;
                        box-shadow:0 0 10px rgba(0,0,0,0.4);'>
              <h3>$title</h3>
              <img src='$poster' alt='Poster' style='width:100%;border-radius:10px;'>
              <p style='font-size:14px;color:#ccc;'>$overview</p>
              <form method='post' action='vote.php'>
                <input type='hidden' name='movie_id' value='{$movie['id']}'>
                <input type='hidden' name='title' value='$title'>
                <input type='hidden' name='poster' value='$poster'>
                <button type='submit' 
                        style='background:#66fcf1;color:#0b0c10;
                               padding:10px 20px;border:none;border-radius:5px;
                               cursor:pointer;font-weight:bold;'>
                  Vote
                </button>
              </form>
            </div>";
        }
    } else {
        echo "<p style='text-align:center;'>No movies found.</p>";
    }
} else {
    echo "<p style='text-align:center;'>Please enter a movie name.</p>";
}
?>
