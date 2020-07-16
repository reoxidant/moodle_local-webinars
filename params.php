<?php

$params = [
    'sql' => "
        SELECT 
        urls.id,
        course.id as course_id,
        course.fullname as course_name,
        urls.name as topic_name,
        urls.parameters as ulr_param,
        urls.externalurl as src_url,
        user_enrols.userid as userid,
        user_enrols.status as status
        FROM mdl_user_enrolments as user_enrols
        JOIN mdl_enrol ON user_enrols.enrolid = mdl_enrol.id AND mdl_enrol.enrol = 'manual'
        JOIN mdl_course as course ON mdl_enrol.courseid = course.id
        JOIN mdl_course_modules as course_mods ON course.id = course_mods.course
        JOIN mdl_modules as mods ON course_mods.module = mods.id AND mods.name = 'url'
        JOIN mdl_url as urls ON course_mods.instance = urls.id WHERE user_enrols.userid = ?
    "
];
