<?php

/**
 * Database wrapper for SQLite3
 */
class Db {
    // Database connection
    protected static $connection;

    /**
     * Connect to the database
     * 
     * @return bool False on failure / SQLite3 object instance on success
     */
    public function connect() {
        // Check if connection is already set
        if( isset( self::$connection ) ) {
            return self::$connection;
        }

        // Check if last connect failed
        if( self::$connection === false ) {
            return false;
        }
        
        // Start connecting
        try {
            $config = parse_ini_file( './config.ini' );
            $perm = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
            self::$connection = new SQLite3( $config['database'] , $perm , $config['encryption'] );
        } catch ( Exception $e ) {
            var_dump( $e );
            return false;
        }
        return self::$connection;
    }

    /**
     * Query the database
     *
     * @param $query The query string
     * @result bool False on failure / mixed The result of the SQLite3::query() function
     */
    public function query( $query ) {
        // Get connection
        $connection = $this -> connect();
    
        // Check if valid connection
        if( $connection === false ) {
            return false;
        }

        // Query the database
        $result = $connection -> query( $query );

        return $result;
    }
	
	/**
	 * Fetch rows from the database (SELECT query)
	 *
	 * @param $query The query string
	 * @return bool False on failure / array Database rows on success
	 */
    public function select( $query ) {
        $rows = array();
        $result = $this -> query( $query );

        if( $result === false ) {
            return false;
        }

        while( $row = $result -> fetchArray() ) {
            $rows[] = $row;
        }

        return $rows;
    }
	
	/**
	 * Fetch the last error from the database
	 * 
	 * @return string Database error message
	 */
    public function error() {
        $connection = $this -> connect();
        return $connection -> lastErrorMsg();
    }
	
	/**
	 * Quote and escape value for use in a database query
	 *
	 * @param string $value The value to be quoted and escaped
	 * @return string The quoted and escaped string
	 */
    public function quote( $value ) {
        $connection = $this -> connect();
        $escaped = $connection -> escapeString( $value );
        return "'" . $escaped . "'";
    }
}
