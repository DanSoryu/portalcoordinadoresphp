<?php
session_start();
if (!isset($_SESSION['usuario'])) {
	header('Location: Login/Login.php');
	exit();
} else {
	header('Location: Dashboard/Dashboard.php');
	exit();
}