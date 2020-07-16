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
            $array[$res -> status][$res -> course_id][] = get_object_vars($res);
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
     * @param $course
     * @param $key
     * @return string HTML content
     * @throws \moodle_exception
     */
    private function getHtmlCourse($course, $key): string
    {
        return
            \html_writer ::start_tag('h3') .
            \html_writer ::start_tag('span') .
            \html_writer ::start_tag('a', array('href' => new moodle_url('/course/view.php', ['id' => $key]))) . $course[0]['course_name'] .
            \html_writer ::end_tag('a') .
            \html_writer ::end_tag('span') .
            \html_writer ::end_tag('h3');
    }

    /**
     * @param $course_data
     * @return string HTML content
     */
    private function getNavTopicList($course_data): string
    {
        $content = \html_writer ::start_tag('ul', array('class' => "nav_topic-list section"));
        foreach ($course_data as $key => $topic) {
            $content .= $this -> getHtmlTopic($topic);
        }
        $content .= \html_writer ::end_tag('ul');
        return $content;
    }

    /**
     * @param $topic
     * @return string HTML content
     * @throws \moodle_exception
     */
    private function getHtmlTopic($topic): string
    {
        return
            \html_writer ::start_tag('li', array('class' => 'activity')) .
            \html_writer ::start_tag('a', array('class' => "topic_link", 'href' => $topic['src_url'], 'target' => '_blank')) .
            \html_writer ::start_tag('img', array('class' => 'iconlarge activityicon', 'src' => new moodle_url('/theme/image.php/boost/url/1594822249/icon'))) .
            \html_writer ::start_tag('span') .
            $topic['topic_name'] .
            \html_writer ::end_tag('span') .
            \html_writer ::end_tag('a') .
            \html_writer ::end_tag('li');
    }

    /**
     * @param $data
     * @return string HTML content
     * @throws \moodle_exception
     */
    private function getHtmlContent($data): string
    {
        $content = \html_writer ::start_tag('div', array('class' => 'course-content'));
        foreach ($data as $key => $course) {
            $content .= $this -> getHtmlCourse($course, $key);
            $content .= $this -> getNavTopicList($course);
        }
        $content .= \html_writer ::end_tag('div');
        return $content;
    }

    /**
     * @param $status
     * @return string HTML content
     * @throws \coding_exception
     */
    private function getHtmlTitle($status): string
    {
        $title = \html_writer ::start_tag('h2', array('class' => 'module_title'));
        if (!$status) {
            $title .= get_string('active_course_title', 'local_webinars');
        } else {
            $title .= get_string('past_course_title', 'local_webinars');
        }
        $title .= \html_writer ::end_tag('h2');
        return $title;
    }

    /**
     * @return string HTML content
     * @throws \coding_exception
     */
    public function getHtml(): string
    {
        $html = '';
        foreach ($this -> getData() as $key => $data) {
            $html .= $this -> getHtmlTitle($key) .
                $this -> getHtmlContent($data);
        }
        return $html;
    }
}