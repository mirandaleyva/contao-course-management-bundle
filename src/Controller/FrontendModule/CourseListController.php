<?php

namespace MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Database;
use Contao\ModuleModel;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(type: 'course_list', category: 'courses', template: 'mod_course_management')]
class CourseListController extends AbstractFrontendModuleController
{
  protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
  {
    System::loadLanguageFile('modules');

    $order = $model->course_order === 'desc' ? 'DESC' : 'ASC';
    $todayTimestamp = strtotime('today');

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
                ")
        ->execute($coursesResult->id);

      $dates = [];

      while ($datesResult->next()) {
        $startTimestamp = $this->parseDateValue($datesResult->start_date);
        $endTimestamp = $this->parseDateValue($datesResult->end_date) ?? $startTimestamp;

        if (null === $startTimestamp || null === $endTimestamp || $endTimestamp < $todayTimestamp) {
          continue;
        }

        $addressParts = array_filter([
          trim(($datesResult->postal_code ?: '') . ' ' . ($datesResult->venue ?: '')),
          trim(implode(' ', array_filter([$datesResult->street, $datesResult->house_number]))),
        ]);

        $dates[] = [
          'id' => $datesResult->id,
          'start_date' => $this->formatDateValue($datesResult->start_date),
          'end_date' => $this->formatDateValue($datesResult->end_date),
          'add_time' => (bool) $datesResult->add_time,
          'start_time' => $this->formatTimeValue($datesResult->start_time),
          'end_time' => $this->formatTimeValue($datesResult->end_time),
          'venue' => $datesResult->venue,
          'location' => implode(', ', $addressParts),
          'fully_booked' => (bool) $datesResult->fully_booked,
          '_sort_start' => $startTimestamp,
          '_sort_time' => $this->parseTimeValue($datesResult->start_time),
        ];
      }

      if (empty($dates)) {
        continue;
      }

      usort($dates, static function (array $left, array $right): int {
        return [$left['_sort_start'], $left['_sort_time']] <=> [$right['_sort_start'], $right['_sort_time']];
      });

      $dates = array_map(static function (array $date): array {
        unset($date['_sort_start'], $date['_sort_time']);

        return $date;
      }, $dates);

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

    $template->set('courses', $courses);
    $template->set('empty', empty($courses));
    $template->set('labels', [
      'empty' => $GLOBALS['TL_LANG']['MSC']['course_list_empty'] ?? 'There are currently no courses with upcoming dates available.',
      'author' => $GLOBALS['TL_LANG']['MSC']['course_list_author'] ?? 'Author',
      'course_dates' => $GLOBALS['TL_LANG']['MSC']['course_list_dates'] ?? 'Course dates',
      'date' => $GLOBALS['TL_LANG']['MSC']['course_list_date'] ?? 'Date',
      'time' => $GLOBALS['TL_LANG']['MSC']['course_list_time'] ?? 'Time',
      'location' => $GLOBALS['TL_LANG']['MSC']['course_list_location'] ?? 'Location',
      'status' => $GLOBALS['TL_LANG']['MSC']['course_list_status'] ?? 'Status',
      'fully_booked' => $GLOBALS['TL_LANG']['MSC']['course_list_fully_booked'] ?? 'Fully booked',
      'available' => $GLOBALS['TL_LANG']['MSC']['course_list_available'] ?? 'Available',
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

  private function formatDateValue(?string $value): string
  {
    if (!$value) {
      return '';
    }

    $timestamp = $this->parseDateValue($value);

    return null === $timestamp ? $value : date('d.m.Y', $timestamp);
  }

  private function parseTimeValue(?string $value): int
  {
    if (!$value) {
      return 0;
    }

    $timestamp = strtotime($value);

    return false === $timestamp ? 0 : ((int) date('H', $timestamp) * 60) + (int) date('i', $timestamp);
  }

  private function formatTimeValue(?string $value): string
  {
    if (!$value) {
      return '';
    }

    $timestamp = strtotime($value);

    return false === $timestamp ? $value : date('H:i', $timestamp);
  }
}
