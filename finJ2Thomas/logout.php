<?php
session_start();
session_destroy();
header("Location: compte.php");
exit;
?>