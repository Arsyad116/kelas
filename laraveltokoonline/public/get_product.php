<?php
// File ini hanya untuk kompatibilitas dengan AJAX di frontend
// Sebenarnya kita sudah punya route Laravel untuk ini di /admin/get_product

// Redirect ke route Laravel
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    header("Location: /admin/get_product?id=$id");
    exit;
} else {
    header("Location: /admin");
    exit;
}
