<?php

$GLOBALS['BE_MOD']['content']['courses'] = [
  'tables' => ['tl_course', 'tl_course_date'],
];

$GLOBALS['FE_MOD']['courses'] = [
  'course_list' => MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule\CourseListController::class,
  'course_reader' => MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule\CourseReaderController::class,
];
