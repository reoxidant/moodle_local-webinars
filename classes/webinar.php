<?php

namespace classes;

class webinar
{
    private static $instance = null;
    private $sqlText;
    private $moodle_database;

    public static function getInstance($sqlText)
    {
        if (null === self::$instance) {
            self::$instance = new self($sqlText);
        }
        return self::$instance;
    }

    public function getLinks()
    {
        $this->getDatabaseResult();
    }

    private function __construct($sqlText)
    {
        $this->sqlText = $sqlText;
        global $DB;
        $this->moodle_database = $DB;
    }

    private function getDatabaseResult()
    {
        $this->moodle_database->get_records_sql($this->sqlText);
    }
}