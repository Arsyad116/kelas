<?php
// File ini hanya untuk kompatibilitas dengan URL lama
// Redirect ke route Laravel untuk customers

// Ambil semua parameter dari URL
$query = $_SERVER['QUERY_STRING'];
$queryString = !empty($query) ? "?$query" : "";

// Redirect ke route Laravel
header("Location: /admin/customers$queryString");
exit;
