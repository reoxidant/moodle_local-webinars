<?php

namespace classes;

class webinar
{
    private static $instance = null;
    private $sqlText;
    private $sqlParam;
    private $moodle_database;
    private $data;

    public static function getInstance($sqlText, $sqlParam)
    {
        if (null === self ::$instance) {
            self ::$instance = new self($sqlText, $sqlParam);
        }
        return self ::$instance;
    }

    public function getLinks()
    {
        return $this -> data;
    }

    private function __construct($sqlText, $sqlParam)
    {
        $this -> sqlParam = $sqlParam;
        $this -> sqlText = $sqlText;
        global $DB;
        $this -> moodle_database = $DB;
        $this -> setData();
    }

    private function setData()
    {
        foreach ($this -> getDatabaseResult() as $res) {
            $this -> data[$res -> course_id][] = get_object_vars($res);
        }
    }

    private function getDatabaseResult()
    {
        return $this -> moodle_database -> get_records_sql($this -> sqlText, $this -> sqlParam);
    }
}