<?php

class User {
    protected $id;
    protected $email;
    protected $password;
    protected $isAdmin;

    public function __construct($id, $email, $password, $isAdmin = 0) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->isAdmin = $isAdmin;
    }

    public function getId() {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
    }

    public function getEmail() {
        return $this->email;
    }
    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPassword() {
        return $this->password;
    }
    public function setPassword($password) {
        $this->password = $password;
    }

    public function isAdmin() {
        return $this->isAdmin == 1;
    }
    public function setIsAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
    }
}
