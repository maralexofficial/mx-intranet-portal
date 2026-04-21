<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/classes/ComponentLoader.php';

$loader = new ComponentLoader(__DIR__ . '/components');

$currentComponent = $_GET['component'] ?? null;

require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/children.php';
require __DIR__ . '/partials/footer.php';
