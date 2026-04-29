<?php
// /src/modules/users/user-list-controller.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/user-repository.php';
require_once __DIR__ . '/user-service.php';
require_once __DIR__ . '/../../core/avatar-service.php';

// Instantiate Architecture
$repository = new UserRepository();
$userService = new UserService($repository);

// Fetch all the users
$users = $userService->getAllUserDetails();

require_once __DIR__ . '/views/list.php';