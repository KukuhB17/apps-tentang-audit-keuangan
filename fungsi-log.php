<?php
function logActivity($user_id, $type, $detail) {
    global $conn;
    $detail_safe = mysqli_real_escape_string($conn, $detail);
    mysqli_query($conn, "INSERT INTO audit_trails (user_id, activity_type, new_values) VALUES ('$user_id', '$type', '$detail_safe')");
}
?>