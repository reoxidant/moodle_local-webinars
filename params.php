<?php

$params = [
    'sql' => "
        SELECT 
        course_mods.course as course_id,
        course_mods.instance as course_insance,
        mdl_modules.name as modules_name,
        mdl_modules.visible as modules_visible,
        mdl_url.course as url_course_id,
        mdl_url.name as url_name,
        mdl_url.parameters as ulr_param,
        course.fullname as course_name,
        enrol.courseid as enrol_course_id,
        user_enrol.status as status_course
        FROM mdl_course_modules as course_mods
        JOIN mdl_modules ON course_mods.course = mdl_modules.id
        JOIN mdl_url ON course_mods.instance = mdl_url.id
        JOIN mdl_course as course ON course_mods.course = course.id
        JOIN mdl_enrol as enrol ON course_mods.course = enrol.courseid
        JOIN mdl_user_enrolments as user_enrol ON enrol.id = user_enrol.enrolid WHERE enrol.enrol = 'manual'
    "
];
