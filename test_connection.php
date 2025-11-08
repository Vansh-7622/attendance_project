<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth_check.php';
echo "DB connected OK. PDO version: " . PDO::ATTR_DRIVER_NAME;
