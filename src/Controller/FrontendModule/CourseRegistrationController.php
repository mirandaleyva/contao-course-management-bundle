<?php

namespace MirandaLeyva\ContaoCourseManagementBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Database;
use Contao\FilesModel;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(type: 'course_registration', category: 'courses', template: 'mod_course_registration')]
class CourseRegistrationController extends AbstractFrontendModuleController
{
  protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
  {
    System::loadLanguageFile('modules');

    $courseId = $request->query->getInt('course');
    $dateId = $request->query->getInt('date');

    if ($courseId <= 0 || $dateId <= 0) {
      $template->set('empty', true);
      $template->set('message', $GLOBALS['TL_LANG']['MSC']['course_registration_empty'] ?? 'No valid course date was selected.');
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
      $template->set('message', $GLOBALS['TL_LANG']['MSC']['course_registration_empty'] ?? 'No valid course date was selected.');
      $template->set('labels', $this->getLabels());

      return $template->getResponse();
    }

    $dateResult = Database::getInstance()
      ->prepare("
                SELECT *
                FROM tl_course_date
                WHERE id = ?
                AND pid = ?
                AND published = '1'
                LIMIT 1
            ")
      ->execute($dateId, $courseId);

    if ($dateResult->numRows < 1) {
      $template->set('empty', true);
      $template->set('message', $GLOBALS['TL_LANG']['MSC']['course_registration_empty'] ?? 'No valid course date was selected.');
      $template->set('labels', $this->getLabels());

      return $template->getResponse();
    }

    $previewImage = null;

    if ($courseResult->preview_image) {
      $uuid = StringUtil::binToUuid($courseResult->preview_image);
      $file = FilesModel::findByUuid($uuid);

      if ($file !== null) {
        $previewImage = $file->path;
      }
    }

    $addressParts = array_filter([
      trim(($dateResult->postal_code ?: '') . ' ' . ($dateResult->venue ?: '')),
      trim(implode(' ', array_filter([$dateResult->street, $dateResult->house_number]))),
    ]);

    $course = [
      'id' => $courseResult->id,
      'title' => $courseResult->title,
      'author' => $courseResult->author,
      'description' => $courseResult->description,
      'preview_image' => $previewImage,
      'form_reference' => (int) $courseResult->form_reference,
    ];

    $date = [
      'id' => $dateResult->id,
      'start_date' => $this->formatDateValue($dateResult->start_date),
      'end_date' => $this->formatDateValue($dateResult->end_date),
      'add_time' => (bool) $dateResult->add_time,
      'start_time' => $this->formatTimeValue($dateResult->start_time),
      'end_time' => $this->formatTimeValue($dateResult->end_time),
      'location' => implode(', ', $addressParts),
      'fully_booked' => (bool) $dateResult->fully_booked,
    ];

    $template->set('empty', false);
    $template->set('course', $course);
    $template->set('date', $date);
    $template->set('labels', $this->getLabels());

    return $template->getResponse();
  }

  private function getLabels(): array
  {
    return [
      'date' => $GLOBALS['TL_LANG']['MSC']['course_registration_date'] ?? 'Date',
      'time' => $GLOBALS['TL_LANG']['MSC']['course_registration_time'] ?? 'Time',
      'location' => $GLOBALS['TL_LANG']['MSC']['course_registration_location'] ?? 'Location',
      'status' => $GLOBALS['TL_LANG']['MSC']['course_registration_status'] ?? 'Status',
      'fully_booked' => $GLOBALS['TL_LANG']['MSC']['course_registration_fully_booked'] ?? 'Fully booked',
      'available' => $GLOBALS['TL_LANG']['MSC']['course_registration_available'] ?? 'Available',
      'form' => $GLOBALS['TL_LANG']['MSC']['course_registration_form'] ?? 'Registration',
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

  private function formatTimeValue(?string $value): string
  {
    if (!$value) {
      return '';
    }

    $timestamp = strtotime($value);

    return false === $timestamp ? $value : date('H:i', $timestamp);
  }
}
