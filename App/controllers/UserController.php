<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;

class UserController {
    protected $db;

    public function __construct() {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /** 
     * Show the login page
     * 
     * @return void
     */
    public function login() {
        loadView('users/login');
    }


    /**
     * Show the register page
     * 
     * @return void
     */
    public function create() {
        loadView('users/create');
    }


    /**
     * Store user in the db
     * 
     * @return void
     */
    public function store() {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $passwordConfirmation = $_POST['password_confirmation'];

        $errors = [];

        // Validate email
        if (!Validation::email($email)) {
            $errors['email'] = 'Invalid email address';
        }

        // Validate name
        if (!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        // Validate password
        if (!Validation::string($password, 6, 50)) {
            $errors['password'] = 'Password must be between 6 and 50 characters';
        }

        // Validate password confirmation
        if (!Validation::match($password, $passwordConfirmation)) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state
                ]
            ]);
            exit;
        }

        // Check if the email exists in DB
        $params = [
            'email' => $email
        ];

        $user = $this->db->query(
            'SELECT * FROM users WHERE email = :email',
            $params
        )->fetch();

        if ($user) {
            $errors['email'] = 'Email already exists';
            loadView('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state
                ]
            ]);
            exit;
        }

        // Create the user account
        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $this->db->query(
            'INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)',
            $params
        );

        // Get the user id
        $userId = $this->db->conn->lastInsertId();

        // Set the user session
        Session::set('user', [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state
        ]);

        redirect('/');
    }
}
