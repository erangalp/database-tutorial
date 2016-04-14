<?php
/**
 * Connect to the database
 * 
 * @return bool false on failure / mysqli MySQLi object instance on success
 */
function db_connect() {
    // Define the connection as static variable
    static $connection;

    // Check if connection is already set
    if( isset( $connection ) ) {
        return $connection;
    }

    // Check if last connect failed
    if( $connection === false ) {
        return false;
    }
    
    // Start connecting
    try {
        $config = parse_ini_file( './config.ini' );
        $perm = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE;
        $connection = new SQLite3( $config['database'] , $perm , $config['encryption'] );
    } catch ( Exception $e ) {
        var_dump( $e );
        return false;
    }
    return $connection;
}

/**
 * Query the database
 *
 * @param $query The query string
 * @return mixed The result of the mysqli::query() function
 */
function db_query( $query ) {
    // Get connection
    $connection = db_connect();

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
function db_select($query) {
    $rows = array();
    $result = db_query( $query );

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
function db_error() {
    $connection = db_connect();
    return $connection -> lastErrorMsg();
}

/**
 * Quote and escape value for use in a database query
 *
 * @param string $value The value to be quoted and escaped
 * @return string The quoted and escaped string
 */
function db_quote( $value ) {
    $connection = db_connect();
    $escaped = $connection -> escapeString( $value );
    return "'" . $escaped . "'";
}
