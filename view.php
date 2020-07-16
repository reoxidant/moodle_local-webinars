<?php

require_once('classes/webinar.php');
require_once('params.php');

use classes\webinar;

require_once('../../config.php');

if (!isloggedin() or isguestuser()) {
    require_login();
    die;
}

$context_sys = context_system ::instance();

$PAGE -> set_url("$CFG->httpswwwroot/local/webinars/view.php");
$PAGE -> set_context($context_sys);
$PAGE -> set_pagelayout('standart');
$title = get_string('pluginname', 'local_webinars');
$PAGE -> navbar -> add($title);
$PAGE -> set_heading($title);
$PAGE -> set_title($title);
$PAGE -> set_cacheable(false);
//$PAGE->requires->css('/local/webinars/styles.css');
echo $OUTPUT -> header();

$webinars = webinar ::getInstance($params['sql'], [$USER -> id]);

echo $webinars -> getHtml();

echo $OUTPUT -> footer();