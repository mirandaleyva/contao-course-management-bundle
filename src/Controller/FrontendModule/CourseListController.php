<?php

namespace MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\ModuleModel;
use Contao\Template;
use Contao\Database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CourseListController extends AbstractFrontendModuleController
{
  protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
  {
    $order = $model->course_order === 'desc' ? 'DESC' : 'ASC';
    $today = date('Y-m-d');

    $coursesResult = Database::getInstance()
      ->prepare("
                SELECT *
                FROM tl_course
                WHERE published = '1'
                ORDER BY title $order
            ")
      ->execute();

    $courses = [];

    while ($coursesResult->next()) {
      $datesResult = Database::getInstance()
        ->prepare("
                    SELECT *
                    FROM tl_course_date
                    WHERE pid = ?
                    AND published = '1'
                    AND end_date >= ?
                    ORDER BY start_date ASC, start_time ASC
                ")
        ->execute($coursesResult->id, $today);

      $dates = [];

      while ($datesResult->next()) {
        $dates[] = [
          'id' => $datesResult->id,
          'start_date' => $datesResult->start_date,
          'end_date' => $datesResult->end_date,
          'add_time' => $datesResult->add_time,
          'start_time' => $datesResult->start_time,
          'end_time' => $datesResult->end_time,
          'venue' => $datesResult->venue,
          'postal_code' => $datesResult->postal_code,
          'street' => $datesResult->street,
          'house_number' => $datesResult->house_number,
          'fully_booked' => $datesResult->fully_booked,
        ];
      }

      if (empty($dates)) {
        continue;
      }

      $courses[] = [
        'id' => $coursesResult->id,
        'title' => $coursesResult->title,
        'author' => $coursesResult->author,
        'description' => $coursesResult->description,
        'form_reference' => $coursesResult->form_reference,
        'preview_image' => $coursesResult->preview_image,
        'dates' => $dates,
      ];
    }

    $template->courses = $courses;
    $template->empty = empty($courses);

    return $template->getResponse();
  }
}
