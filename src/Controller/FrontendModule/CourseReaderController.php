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

#[AsFrontendModule(type: 'course_reader', category: 'courses', template: 'mod_course_reader')]
class CourseReaderController extends AbstractFrontendModuleController
{
  protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
  {
    System::loadLanguageFile('modules');

    $courseId = $request->query->getInt('course');

    if ($courseId <= 0) {
      $template->set('empty', true);
      $template->set('message', 'Kein Kurs ausgewählt.');
      $template->set('labels', $this->getLabels());

      return $template->getResponse();
    }

    $courseResult = Database::getInstance()
      ->prepare("
                SELECT *
                FROM tl_course
                WHERE id = ?
                AND published = '1'
                LIMIT 1
            ")
      ->execute($courseId);

    if ($courseResult->numRows < 1) {
      $template->set('empty', true);
      $template->set('message', 'Der angeforderte Kurs wurde nicht gefunden.');
      $template->set('labels', $this->getLabels());

      return $template->getResponse();
    }

    $todayTimestamp = strtotime('today');
    $registrationPageUrl = null;

    if ($model->registration_jumpTo) {
      $registrationPage = PageModel::findPublishedById($model->registration_jumpTo);

      if ($registrationPage !== null) {
        $registrationPageUrl = $registrationPage->getFrontendUrl();
      }
    }

    $datesResult = Database::getInstance()
      ->prepare("
                SELECT *
                FROM tl_course_date
                WHERE pid = ?
                AND published = '1'
            ")
      ->execute($courseId);

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

      $registrationUrl = null;

      if ($registrationPageUrl !== null && !(bool) $datesResult->fully_booked) {
        $registrationUrl = $registrationPageUrl . '?course=' . $courseId . '&date=' . $datesResult->id;
      }

      $dates[] = [
        'id' => $datesResult->id,
        'start_date' => $this->formatDateValue($datesResult->start_date),
        'end_date' => $this->formatDateValue($datesResult->end_date),
        'add_time' => (bool) $datesResult->add_time,
        'start_time' => $this->formatTimeValue($datesResult->start_time),
        'end_time' => $this->formatTimeValue($datesResult->end_time),
        'location' => implode(', ', $addressParts),
        'fully_booked' => (bool) $datesResult->fully_booked,
        'registration_url' => $registrationUrl,
        '_sort_start' => $startTimestamp,
        '_sort_time' => $this->parseTimeValue($datesResult->start_time),
      ];
    }

    usort($dates, static function (array $left, array $right): int {
      return [$left['_sort_start'], $left['_sort_time']] <=> [$right['_sort_start'], $right['_sort_time']];
    });

    $dates = array_map(static function (array $date): array {
      unset($date['_sort_start'], $date['_sort_time']);

      return $date;
    }, $dates);

    $course = [
      'id' => $courseResult->id,
      'title' => $courseResult->title,
      'author' => $courseResult->author,
      'description' => $courseResult->description,
      'preview_image' => $courseResult->preview_image,
      'form_reference' => $courseResult->form_reference,
      'dates' => $dates,
    ];

    $template->set('empty', false);
    $template->set('course', $course);
    $template->set('labels', $this->getLabels());

    return $template->getResponse();
  }

  private function getLabels(): array
  {
    return [
      'author' => $GLOBALS['TL_LANG']['MSC']['course_reader_author'] ?? 'Author',
      'course_dates' => $GLOBALS['TL_LANG']['MSC']['course_reader_dates'] ?? 'Dates & registration',
      'no_dates' => $GLOBALS['TL_LANG']['MSC']['course_reader_no_dates'] ?? 'There are currently no upcoming dates available.',
      'date' => $GLOBALS['TL_LANG']['MSC']['course_reader_date'] ?? 'Date',
      'time' => $GLOBALS['TL_LANG']['MSC']['course_reader_time'] ?? 'Time',
      'location' => $GLOBALS['TL_LANG']['MSC']['course_reader_location'] ?? 'Location',
      'status' => $GLOBALS['TL_LANG']['MSC']['course_reader_status'] ?? 'Status',
      'fully_booked' => $GLOBALS['TL_LANG']['MSC']['course_reader_fully_booked'] ?? 'Fully booked',
      'available' => $GLOBALS['TL_LANG']['MSC']['course_reader_available'] ?? 'Available',
      'register' => $GLOBALS['TL_LANG']['MSC']['course_reader_register'] ?? 'Register now',
    ];
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
