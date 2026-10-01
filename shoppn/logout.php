<?php
require_once __DIR__ . '/core/core.php';

session_destroy();
redirect(BASE_URL . '/index.php');