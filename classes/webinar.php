<?php

namespace classes;

use moodle_url;

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

    private function getHtmlCourse($course, $key) : string
    {
        return
        \html_writer ::start_tag('h3')
        .
        \html_writer ::start_tag('span')
        .
        \html_writer ::start_tag('a', array('href' => new moodle_url('/course/view.php', ['id' => $key]))). $course[$key][0]['course_name'] .\html_writer ::end_tag('a')
        .
        \html_writer ::end_tag('span')
        .
        \html_writer ::end_tag('h3');
    }

    private function getNavTopicList($course_data) : string
    {
        $content = \html_writer ::start_tag('ul', array('class' => "nav_topic-list"));
        foreach ($course_data as $key => $topic) {
            $this->getHtmlTopic();
        }
        $content .= \html_writer::end_tag('ul');
        return $content;
    }

    private function getHtmlTopic() : string
    {
        return
            \html_writer ::start_tag('li');
            \html_writer ::start_tag('img', array('class' => 'iconlarge activityicon'));
            \html_writer ::start_tag('a', array('class' => "topic_link", 'href' => new moodle_url('/mod/url/view.php', ['id'=> $topic['id']])))
            .
            $topic['topic_name']
            .
            \html_writer ::end_tag('a');
            \html_writer ::end_tag('li');
    }

    /**
     * @return string HTML content
     */
    private function getHtmlContent(): string
    {
        $content = \html_writer ::start_tag('div', array('class' => 'module_content'));

        foreach ($this -> data as $key => $course) {
            $content .= $this -> getHtmlCourse($course, $key);
            $content .= $this -> getNavTopicList($course);
        }
        $content .= \html_writer ::end_tag('div');
        return $content;
    }

    /**
     * @return string HTML content
     */
    private function getHtmlStatus() : string
    {
        return \html_writer ::start_tag('div', array('class' => 'module_status')) .
                "status content".
                \html_writer ::end_tag('div');
    }

    /**
     * @return string HTML content
     */
    private function getHtmlTitle() : string
    {
        return \html_writer ::start_tag('h2', array('class' => 'module_title')) .
                "Каталог вебинаров" .
               \html_writer ::end_tag('h2');
    }

    /**
     * @return string HTML content
     */
    public function getHtml(): string
    {
        return
            $this -> getHtmlTitle() .
            $this -> getHtmlContent() .
            $this -> getHtmlStatus();
    }
}