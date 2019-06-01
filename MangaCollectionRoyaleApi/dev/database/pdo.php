<?php
    // ################ Configurations ######################
		/* Your SQL Database server */
		define('DBHOST','rdbms.strato.de');
		/* Your SQL Database Username */
		define('DBUSER','U3336730');
		/* Your SQL Database Password */
		define('DBPASS','q2KPbtI0QHns2Lji');
		/* Your SQL Database Name */
        define('DBNAME','DB3336730');
        // Your Site Domain
        define('DIR','localhost');
        // Your Site Email
        define('SITEEMAIL','noreply@domain.com');
// Create DB Class
class DB {
    // set variables
        private $con;
        private $error;
        private $qError;
        private $stmt;
        // Method: connect()
        public function connect(){
            // create connection string
            $dbs = "mysql:host=".DBHOST.";dbname=".DBNAME.";charset=utf8";
            // create option array
            $options = array(   PDO::ATTR_PERSISTENT    => true, // use existing connection if exists, otherwise try to connect
                                PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION,  // Information output for possible PDO errors
                                PDO::ATTR_EMULATE_PREPARES => false // mysql server can handle prepared statements
                                                                    // if the mysql server can't handle prepared statements set to true
                                                                    // then pdo prepared statements will be used
                            );   
            // exception handling 
            try{
                // try to connect to Database and create new Object
                // if the connection is successful, everything is ok
                $this->con = new PDO($dbs, DBUSER, DBPASS, $options);
            } catch (PDOException $e){
                // If no connection is possible, use PDOException to output error message
                $this->error = $e->getMessage();
                exit;
            }
        }
        // Method: query()
        // Parameter: $query
        public function query($query){
            // user $query string for prepared statement
            // and save in variable $this->stmt
            $this->stmt = $this->con->prepare($query);
        }
        // Method: bind()
        // Parameter: $paramn, $value, $type (default: null)
        public function bind($param, $value, $type = null){
            // the default value of type is "null" so go through this if statement
            if(is_null($type)){
                // Go through each case until a check returns true
                switch (true){
                    // cheack if the value of the variable $value is an integer
                    case is_int($value): // if false go to next case
                        $type = PDO::PARAM_INT; // set $type as integer
                        break; // terminates the execution of the switch case
                    // cheack if the value of the variable $value is a boolean    
                    case is_bool($value): 
                        $type = PDO::PARAM_BOOL; // set $type as boolean
                        break; 
                    // cheack if the value of the variable $value is null        
                    case is_null($value): 
                        $type = PDO::PARAM_NULL; // set $type as null
                        break; 
                    // if nothing is true the default vaulue will string    
                    default:
                        $type = PDO::PARAM_STR; // set $type as string
                }
            }
            // use the variables and bind their values
            // Example: bindValue(':userID', '2' , PDO::PARAM_INT);
            $this->stmt->bindValue($param, $value, $type);
        }
        // Method: execute()
        public function execute(){
            // return pdo execute method
            return $this->stmt->execute();
        }
        // Method: results()
        public function results(){
            // use pdo execute method
            $this->execute();
            // return an array of each row in the result set, or false if the method call fails.
            return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        // Method: result()
        public function result(){
            // use pdo execute method
            $this->execute();
            // returns a mixed value that returns a row or false.
            return $this->stmt->fetch(PDO::FETCH_ASSOC);
        }
        // Method: rowCount()
        public function rowCount(){
            // returns the number of rows added, deleted, or changed.
            return $this->stmt->rowCount();
        }
        // Method: lastInsertId()
        public function lastInsertId(){
            // returns the identifier for the row most recently inserted into a table in the database.
           return $this->con->lastInsertId();
        }
        // Method: close()
        public function close(){
            // to close the Database connection
            $this->con = null;
        }
}
?>