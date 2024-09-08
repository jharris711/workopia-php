<?php

namespace App\Controllers;

use Framework\Authorization;
use Framework\Database;
use Framework\Session;
use Framework\Validation;

class ListingController {
    protected $db;

    public function __construct() {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show all listings
     *
     * @return void
     */
    public function index() {
        $listings = $this->db->query(
            'SELECT * FROM listings ORDER BY created_at DESC '
        )->fetchAll();

        loadView('listings/index', ['listings' => $listings]);
    }

    /**
     * Create a listing
     *
     * @return void
     */
    public function create() {
        loadView('listings/create');
    }

    /**
     * Show a single listing
     *
     * @param array $params
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query(
            'SELECT * FROM listings WHERE id = :id',
            $params
        )->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store a listing in the DB
     * 
     * @return void
     */
    public function store() {
        $allowedFields = [
            "title",
            "description",
            "salary",
            "requirements",
            "benefits",
            "company",
            "address",
            "city",
            "state",
            "phone",
            "email",
            "tags"
        ];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = Session::get('user')['id'];

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = [
            'title',
            'description',
            'salary',
            'email',
            'city',
            'state',
        ];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Reload the view with errors
            loadView(('listings/create'), [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            // Submit data
            $fields = [];

            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);

            $values = [];

            foreach ($newListingData as $field => $value) {
                if ($value === '') {
                    $newListingData[$field] = null;
                }

                $values[] = ":" . $field;
            }

            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);

            redirect('/listings');
        }
    }

    /**
     * Delete a listing
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query(
            'SELECT * FROM listings WHERE id = :id',
            $params
        )->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        // Authorization check
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query(
            'DELETE FROM listings WHERE id = :id',
            $params
        );

        // Set the flash message
        Session::setFlash('success_message', 'Listing deleted successfully');

        redirect('/listings');
    }

    /**
     * Show the listing edit form
     *
     * @param array $params
     * @return void
     */
    public function edit($params) {


        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query(
            'SELECT * FROM listings WHERE id = :id',
            $params
        )->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        // Authorization check
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to update this listing');
            return redirect('/listings/' . $listing->id);
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */
    public function update($params) {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id,
        ];

        $listing = $this->db->query(
            'SELECT * FROM listings WHERE id = :id',
            $params
        )->fetch();

        // Check if listing exists
        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        // Authorization check
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to update this listing');
            return redirect('/listings/' . $listing->id);
        }

        $allowedFields = [
            "title",
            "description",
            "salary",
            "requirements",
            "benefits",
            "company",
            "address",
            "city",
            "state",
            "phone",
            "email",
            "tags"
        ];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = [
            'title',
            'description',
            'salary',
            'email',
            'city',
            'state',
        ];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            // Reload the view with errors
            loadView(('listings/edit'), [
                'errors' => $errors,
                'listing' => $listing
            ]);
            exit;
        } else {
            // Submit data
            $updateFields = [];

            foreach ($updateValues as $field => $value) {
                $updateFields[] = $field . ' = :' . $field;
            }

            $updateFields = implode(', ', $updateFields);

            $query = "UPDATE listings SET {$updateFields} WHERE id = :id";

            $updateValues['id'] = $id;

            $this->db->query($query, $updateValues);

            Session::setFlash('success_message', 'Listing updated successfully');

            redirect('/listings/' . $id);
        }
    }
}
