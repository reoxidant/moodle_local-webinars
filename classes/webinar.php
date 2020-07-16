<?php

namespace classes;

/**
 * Class webinar
 * @package classes
 */
class webinar
{
    /**
     * @var null
     */
    private static $instance = null;
    /**
     * @var string
     */
    private $sqlText;
    /**
     * @var array
     */
    private $sqlParam;
    /**
     * @var \moodle_database|null
     */
    private $moodle_database;
    /**
     * @var array
     */
    private $data;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this -> data;
    }

    /**
     * @param array $data
     */
    private function setData($data): void
    {
        $this -> data = $data;
    }

    /**
     * @param $sqlText
     * @param $sqlParam
     * @return webinar
     */
    public static function getInstance($sqlText, $sqlParam): webinar
    {
        if (null === self ::$instance) {
            self ::$instance = new self($sqlText, $sqlParam);
        }
        return self ::$instance;
    }

    /**
     * webinar constructor.
     * @param $sqlText
     * @param $sqlParam
     */
    private function __construct($sqlText, $sqlParam)
    {
        $this -> sqlParam = $sqlParam;
        $this -> sqlText = $sqlText;
        global $DB;
        $this -> moodle_database = $DB;
        $this -> setDataFromDatabase();
    }

    /**
     * setData $array Database
     */
    private function setDataFromDatabase(): void
    {
        $array = array();
        foreach ($this -> getDatabaseResult() as $res) {
            $array[$res -> course_id][] = get_object_vars($res);
        }
        $this -> setData($array);
    }

    /**
     * @return array
     * @throws \dml_exception
     */
    private function getDatabaseResult(): array
    {
        return $this -> moodle_database -> get_records_sql($this -> sqlText, $this -> sqlParam);
    }

    /**
     * @return string HTML content
     */
    private function getContent(): string
    {
        $content = \html_writer ::start_tag('div', array('class' => 'module_content'));
        foreach ($this -> data as $key => $course) {
            $content .= \html_writer ::start_tag('h4') . $this -> data[$key][0]['course_name'] . \html_writer ::end_tag('h4');
            foreach ($course as $topic) {
                $content .= \html_writer ::start_tag('a', array('class' => "webinar_link")) . $topic['topic_name'] . \html_writer ::end_tag('a');
            }
        }
        $content .= \html_writer ::end_tag('div');
        return $content;
    }

    /**
     * @return string HTML content
     */
    public function getHtmlContent(): string
    {
        return \html_writer ::start_tag('h4', array('class' => 'module_title')) . "Каталог вебинаров" . \html_writer ::end_tag('h4') .
            $this -> getContent();
        \html_writer ::start_tag('div', array('class' => 'module_status')) . "
                StatusContent
                " . \html_writer ::end_tag('div');
    }
}