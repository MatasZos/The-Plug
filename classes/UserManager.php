<?php

require_once 'User.php';

class UserManager {
    private $users = [];

    public function addUser(User $user) {
        $this->users[$user->getId()] = $user;
    }

    public function removeUser($userId) {
        if (isset($this->users[$userId])) {
            unset($this->users[$userId]);
        }
    }

    public function getUserById($userId) {
        return $this->users[$userId] ?? null;
    }

    public function getAllUsers() {
        return array_values($this->users);
    }

    public function authenticate($email, $password) {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email && $user->getPassword() === $password) {
                return $user;
            }
        }
        return null;
    }

    public function getAdmins() {
        return array_filter($this->users, fn($user) => $user->isAdmin());
    }
}
