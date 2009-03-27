<?php

abstract class AetherORM {
    // Relationships
    protected $has_one = array();
    protected $belongs_to = array();
    protected $has_many = array();
    protected $habtm = array(); // Has and belongs to many

    // Object state
    protected $dirty = false;
    protected $loaded = false;
    protected $saved = false;
    
    // Related objects
    protected $object_relations = array();

    // Model table information
    protected $primaryKey = 'id';
    protected $tableColumns = array();
    protected $dbConfig = 'default';
    
    // Model config
    protected $reload_on_wakeup = true;

    // Object to hold the database connection
    protected $db;

    /**
     * Prepares the model database connection and loads the object
     *
     * @param mixed $id parameter for find
     * @return void
     */
    public function __construct($id = NULL) {
        // Initialize db connection
        $this->initialize();

        // Try to populate the object with data from the db
        // by primary key
        if ($id !== NULL) 
            $this->find($id);
    }

    /**
     * Prepares the model database connection and validates column information
     *
     * @return void
     */
    protected function initialize() {
        // Get the database connection information
        // and connect to the db

        // Make sure all the columns exist
    }

    public function __sleep() {

    }

    public function __wakeup() {

    }

    /**
     * Handles pass-trough to database methods. Calls to query with
     * (query, insert, updated, delete) are not allowed. 
     * Query builder methods are chainable.
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args) {
        if (method_exists($this->db, $method)) {


        }
    }
}