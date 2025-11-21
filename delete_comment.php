<?php
include('db_connect.php');
if (isset($_POST['comment_id'])) {
    $id = $_POST['comment_id'];
    mysqli_query($conn, "DELETE FROM comments WHERE id = $id");
}
header("Location: admin_dashboard.php");
exit();
?>
