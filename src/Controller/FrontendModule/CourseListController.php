<?php

namespace MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(type: 'course_list', category: 'courses', template: 'mod_course_list')]
class CourseListController extends AbstractFrontendModuleController
{
  protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
  {
    System::loadLanguageFile('modules');

    $order = $model->course_order === 'desc' ? 'DESC' : 'ASC';
    $todayTimestamp = strtotime('today');

    $detailPage = null;
    $detailUrlBase = null;

    if ($model->jumpTo) {
      $detailPage = PageModel::findPublishedById($model->jumpTo);

      if ($detailPage !== null) {
        $detailUrlBase = $detailPage->getFrontendUrl();
      }
    }

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
                    SELECT id, start_date, end_date
                    FROM tl_course_date
                    WHERE pid = ?
                    AND published = '1'
                ")
        ->execute($coursesResult->id);

      $hasUpcomingDates = false;
      $nextDateTimestamp = null;

      while ($datesResult->next()) {
        $startTimestamp = $this->parseDateValue($datesResult->start_date);
        $endTimestamp = $this->parseDateValue($datesResult->end_date) ?? $startTimestamp;

        if (null === $startTimestamp || null === $endTimestamp || $endTimestamp < $todayTimestamp) {
          continue;
        }

        $hasUpcomingDates = true;

        if (null === $nextDateTimestamp || $startTimestamp < $nextDateTimestamp) {
          $nextDateTimestamp = $startTimestamp;
        }
      }

      if (!$hasUpcomingDates) {
        continue;
      }

      $detailUrl = null;

      if ($detailUrlBase !== null) {
        $detailUrl = $detailUrlBase . '?course=' . $coursesResult->id;
      }

      $courses[] = [
        'id' => $coursesResult->id,
        'title' => $coursesResult->title,
        'author' => $coursesResult->author,
        'description' => $coursesResult->description,
        'form_reference' => $coursesResult->form_reference,
        'preview_image' => $coursesResult->preview_image,
        'detail_url' => $detailUrl,
        'next_date' => null === $nextDateTimestamp ? '' : date('d.m.Y', $nextDateTimestamp),
      ];
    }

    $template->set('courses', $courses);
    $template->set('empty', empty($courses));
    $template->set('labels', [
      'empty' => $GLOBALS['TL_LANG']['MSC']['course_list_empty'] ?? 'Aktuell sind keine Kurse verfügbar.',
      'author' => $GLOBALS['TL_LANG']['MSC']['course_list_author'] ?? 'Autor',
      'details' => $GLOBALS['TL_LANG']['MSC']['course_list_details'] ?? 'Details anzeigen',
      'next_date' => $GLOBALS['TL_LANG']['MSC']['course_list_next_date'] ?? 'Nächster Termin',
    ]);

    return $template->getResponse();
  }

  private function parseDateValue(?string $value): ?int
  {
    if (!$value) {
      return null;
    }

    if (ctype_digit($value)) {
      return (int) $value;
    }

    $timestamp = strtotime($value);

    return false === $timestamp ? null : strtotime(date('Y-m-d', $timestamp));
  }
}
