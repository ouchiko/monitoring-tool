<?php
namespace trains\models\database;

class DBMysql extends \mysqli
{
    // The number of errors before we abort the running script
    const ABORT_ERRORS = 100;

    // Number of times to retry query if a deadlock error is encountered
    const DEADLOCK_RETRIES = 3;

    // Logging information
    private $_dbResult;
    private $returnFunc;
    private $productName;
    private $logType;                                                       // Can be no_logging, mail_logging or sentry_logging
    private $emailContact;
    private $errorCount;
    private $replicate;
    private $tablesToReplicate;
    private $connected;
    private $throwExceptions;

    function __construct($host, $user, $password, $logType = 'sentry_logging', $emailContact = "", $replicate = false)
    {
        $this->connected = false;
        $this->throwExceptions = false;
        $this->errorCount = 0;
        $this->logType = ((isset($_SERVER['GLOBALVISION_ENVIRONMENT']) && $_SERVER['GLOBALVISION_ENVIRONMENT']) == 'development' ? $logType : 'sentry_logging');
        $this->returnFunc = "fetch_object";
        $this->replicate = false;//$replicate;

        parent::__construct($host, $user, $password);
        parent::set_charset('utf8'); // php5 defaults to latin1 client charset, which breaks a lot of stuff.

        /*parent::init();

        if (parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5))
        {
            if (parent::real_connect($host, $user, $password))
            {
                parent::set_charset('utf8'); // php5 defaults to latin1 client charset, which breaks a lot of stuff.
                $this->connected = true;
            }
        }*/

        if ($this->connect_errno)
            $this->logError();
        else
            $this->connected = true;
    }

    function __destruct()
    {
        parent::close();
    }

    public function throwExceptions()
    {
        $this->throwExceptions = true;
    }

    public function getConnectionError()
    {
        return ($this->connect_errno ? $this->connect_error : "");
    }

    public function setReturnType($type)
    {
        if ($type == "object" || $type == "row" || $type == "assoc")
            $this->returnFunc = "fetch_" . $type;
    }

    public function getReturnType()
    {
        return preg_replace('/^fetch_/', '', $this->returnFunc);
    }

    public function multi_string_query($str)
    {
        parent::multi_query($str);
    }

    public function queryRows($query)
    {
        $this->query($query);

        return $this->obtainRows();
    }

    // Will return an array with the indices set to the value for $fieldName - if the column has no value, the row is skipped
    public function queryRowsWithIndex($query, $fieldName)
    {
        $indexed = array();

        foreach ($this->queryRows($query) as $row)
        {
            $fieldVal = null;

            if ($this->returnFunc == "fetch_object")
                $fieldVal = $row->$fieldName;
            else if ($this->returnFunc == "fetch_assoc")
                $fieldVal = $row[$fieldName];

            if ($fieldVal)
                $indexed[$fieldVal] = $row;
        }

        return $indexed;
    }

    // Returns the response as a numerically indexed array (must be a single field name)
    public function queryRowsAsArray($query, $fieldName)
    {
        $response = array();

        if ($this->query($query))
        {
            foreach ($this->obtainRows() as $row)
            {
                if ($this->returnFunc == "fetch_object")
                    $response[] = $row->$fieldName;
                else if ($this->returnFunc == "fetch_assoc")
                    $response[] = $row[$fieldName];
            }
        }

        return $response;
    }


    /*
     * $fieldNames can be a string or an array of strings
     *
     * This function will return either a value (if $fieldNames is a string)
     * or an object or array depending on the return type set
     * or the row if $fieldNames is not set
     * or false if the whole thing fails for whatever reason
     *
     */

    public function queryRow($query, $fieldNames = false)
    {
        if (!$this -> query($query))
            return false;

        $response = null;

        if ($fieldNames)
        {
            if (!is_array($fieldNames))
                $fieldNames = array($fieldNames);

            if (count($fieldNames) == 1)
                $response = $this->getValue($this->row(), $fieldNames[0]);
            else
            {
                $row = $this->row();

                if ($row)
                {
                    if ($this->returnFunc == "fetch_object")
                    {
                        $response = new \stdClass();

                        foreach ($fieldNames as $fieldName)
                            $response->$fieldName = $row->$fieldName;
                    }
                    else if ($this->returnFunc == "fetch_assoc")
                    {
                        $response = array();

                        foreach ($fieldNames as $fieldName)
                            $response[$fieldName] = $row[$fieldName];
                    }
                }
            }
        }
        else
            $response = $this->row();

        return $response;
    }

    public function query($query)
    {
        if (!$this->connected)
            return false;



        $success = false;

        if ($query)
        {
            $queryFmt = preg_replace('/\s{2,}/', ' ', preg_replace('/[\r\n]/', ' ', $query));

            $attempt = 1;

            do
            {
                $retry = false;
                $this->_dbResult = parent::query($query);

                if (!$this->_dbResult)
                {
                    // Deadlock error number seems to be 1213
                    if ($this->errno == 1213 && $attempt++ <= self::DEADLOCK_RETRIES)
                    {
                        // Half second sleep
                        usleep(500000);
                        $retry = true;
                    }
                    else
                        $this->logError($query);
                }
                else
                    $success = true;
            } while($retry);
        }

        return $success;
    }

    public function chunk($chunkSize)
    {
        $chunk = array();

        while (count($chunk) < $chunkSize)
        {
            $row = $this->row();

            if ($row)
                $chunk[] = $row;
            else
                break;
        }
        return $chunk;
    }

    public function row()
    {
        $row = null;

        if ($this->connected && $this->_dbResult)
        {
            $tmp = $this->returnFunc;
            $row = $this->_dbResult->$tmp();
        }

        return $row;
    }

    private function getValue($row, $field)
    {
        $value = "";

        if ($this->connected && $row)
        {
            if ($this->returnFunc == "fetch_object")
                $value = $row->$field;
            else if ($this->returnFunc == "fetch_assoc")
                $value = $row[$field];
        }

        return $value;
    }

    /**
     * json
     * A correct implementation of JSON - we didn't have the extension installed to make this
     * work  previously. I will need to check existing code to make sure they work correctly with
     * this new method before I update "getAsJSON"
     */
    public function json()
    {
        $result = null;

        if ($this->connected && $this->_dbResult)
        {
            $tmp = $this -> returnFunc;

            while ( $row = $this -> _dbResult -> $tmp() ) if ( $row ) $rows[] = $row;

            $result = json_encode($rows);
        }

        return $result;
    }


    public function obtainRows()
    {
        $results = array();

        if ($this->connected && $this->_dbResult)
        {
            $tmp = $this->returnFunc;
            while ($result = $this->_dbResult->$tmp())
                $results[] = $result;

            $this->_dbResult->close();
            $this->_dbResult = null;
        }

        return $results;
    }

    public function numRows()
    {
        return ($this->connected && $this->_dbResult ? $this->_dbResult->num_rows : 0);
    }

    public function lastInsertID()
    {
        return ($this->connected ? $this->insert_id : null);
    }

    public function affectedRows()
    {
        return ($this->connected ? $this->affected_rows : 0);
    }

    public function getError()
    {
        return $this->error;
    }

    private function logError($extraText)
    {
        $err = ($this->error ? $this->error : $this->connect_error);

        if ($err)
        {
            $locat = debug_backtrace();
            $fileInfo = '';

            foreach ($locat as $trace)
                $fileInfo .= "File: " . $trace['file'] . "\tLine: " . $trace['line'] . "\r\n";

            $server = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:"";

            $mess = "SQL Error\nFile: " . $_SERVER['PHP_SELF'] . "\n\nServer: " . $server . "\n\nError: " . $err . "\n\nError code: " . $this->errno . "\n\nTrace:\n" . $fileInfo . "\n\nQuery: " . $extraText;

            switch ($this->logType)
            {
                default:
                break;
            }

            if ($this->throwExceptions)
                throw new Exception($err);
        }
    }
}
