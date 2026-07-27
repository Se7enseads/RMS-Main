<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Core\View;

/**
 * Fetch all users from the database, including their roles.
 *
 * @return array List of users
 */
class UserController
{
  private UserService $userService;

  public function __construct()
  {
    $this->userService = new UserService();
  }

  public function index(): void
  {
    $users = $this->userService->getAllActiveUsers();
    View::render('users/index', ['users' => $users]);
  }
}
