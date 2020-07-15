<?php

namespace classes;

use setasign\Fpdi\PdfReader\Page;

class webinar
{
    private static $instance = null;
    private $sqlText;
    private $sqlParam;
    private $moodle_database;
    private $data;

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    private function setData($data): void
    {
        $this -> data = $data;
    }

    public static function getInstance($sqlText, $sqlParam) : webinar
    {
        if (null === self ::$instance) {
            self ::$instance = new self($sqlText, $sqlParam);
        }
        return self ::$instance;
    }

    private function __construct($sqlText, $sqlParam)
    {
        $this -> sqlParam = $sqlParam;
        $this -> sqlText = $sqlText;
        global $DB;
        $this -> moodle_database = $DB;
        $this -> setDataFromDatabase();
    }

    private function setDataFromDatabase() : void
    {
        foreach ($this -> getDatabaseResult() as $res) {
            $array[$res -> course_id][] = get_object_vars($res)
        }
        $this->setData();
    }

    private function getDatabaseResult() : array
    {
        return $this -> moodle_database -> get_records_sql($this -> sqlText, $this -> sqlParam);
    }

    private function getContent() : string
    {
        $content = \html_writer::start_tag('div', array('class' => 'module_content'));
        foreach ($this->data as $key => $course){
            $content .= \html_writer::start_tag('h4').$this->data[$key][0]['course_name'].\html_writer::end_tag('h4');
            foreach ($course as $topic){
                $content .= \html_writer::start_tag('a', array('class' => "webinar_link")).$topic['topic_name'].\html_writer::end_tag('a');
            }
        }
        $content .= \html_writer::end_tag('div');
        return $content;
    }

    public function getHtmlContent() : string
    {
        return \html_writer::start_tag('h4', array('class' => 'module_title'))."Каталог вебинаров" .\html_writer::end_tag('h4').
               $this->getContent();
                \html_writer::start_tag('div', array('class' => 'module_status'))."
                StatusContent
                ".\html_writer::end_tag('div');
    }
}