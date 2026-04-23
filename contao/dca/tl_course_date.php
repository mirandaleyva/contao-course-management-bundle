<?php

use Contao\DC_Table;
use Contao\Input;
use Contao\DataContainer;
use Contao\Database;

$GLOBALS['TL_DCA']['tl_course_date'] = [
  'config' => [
    'dataContainer' => DC_Table::class,
    'ptable' => 'tl_course',
    'enableVersioning' => true,
    'sql' => [
      'keys' => [
        'id' => 'primary',
        'pid' => 'index',
      ],
    ],
  ],

  'list' => [
    'sorting' => [
      'mode' => 4,
      'fields' => ['start_date'],
      'flag' => 6,
      'panelLayout' => 'sort,filter;search,limit',
      'headerFields' => ['title'],
    ],
    'label' => [
      'fields' => ['start_date', 'end_date', 'venue'],
      'format' => '%s - %s | %s',
    ],
    'global_operations' => [
      'all' => [
        'href' => 'act=select',
        'class' => 'header_edit_all',
        'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
      ],
    ],
    'operations' => [
      'edit' => [
        'href' => 'act=edit',
        'icon' => 'edit.svg',
      ],
      'copy' => [
        'href' => 'act=paste&amp;mode=copy',
        'icon' => 'copy.svg',
      ],
      'delete' => [
        'href' => 'act=delete',
        'icon' => 'delete.svg',
        'attributes' => 'onclick="if(!confirm(\'Are you sure you want to delete this entry?\'))return false;Backend.getScrollOffset()"',
      ],
      'show' => [
        'href' => 'act=show',
        'icon' => 'show.svg',
      ],
    ],
  ],

  'palettes' => [
    '__selector__' => ['add_time'],

    'default' => '
            {date_legend},start_date,end_date,add_time;
            {location_legend},venue,postal_code,street,house_number;
            {status_legend},fully_booked,published
        ',
  ],

  'subpalettes' => [
    'add_time' => 'start_time,end_time',
  ],

  'fields' => [
    'id' => [
      'sql' => "int(10) unsigned NOT NULL auto_increment",
    ],
    'pid' => [
      'foreignKey' => 'tl_course.title',
      'relation' => [
        'type' => 'belongsTo',
        'load' => 'lazy',
      ],
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'tstamp' => [
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'sorting' => [
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'start_date' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['start_date'],
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'date',
        'datepicker' => true,
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'save_callback' => [
        ['tl_course_date', 'validateDateRange'],
        ['tl_course_date', 'validateDateOverlap'],
      ],
      'sql' => "varchar(10) NOT NULL default ''",
    ],
    'end_date' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['end_date'],
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'date',
        'datepicker' => true,
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'save_callback' => [
        ['tl_course_date', 'validateDateRange'],
        ['tl_course_date', 'validateDateOverlap'],
      ],
      'sql' => "varchar(10) NOT NULL default ''",
    ],
    'add_time' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['add_time'],
      'inputType' => 'checkbox',
      'eval' => [
        'submitOnChange' => true,
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
    'start_time' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['start_time'],
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'time',
        'timepicker' => true,
        'placeholder' => 'HH:MM',
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'save_callback' => [
        ['tl_course_date', 'validateTimeRange'],
      ],
      'sql' => "varchar(5) NOT NULL default ''",
    ],
    'end_time' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['end_time'],
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'time',
        'timepicker' => true,
        'placeholder' => 'HH:MM',
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'save_callback' => [
        ['tl_course_date', 'validateTimeRange'],
      ],
      'sql' => "varchar(5) NOT NULL default ''",
    ],
    'venue' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['venue'],
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 255,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(255) NOT NULL default ''",
    ],
    'postal_code' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['postal_code'],
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 20,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(20) NOT NULL default ''",
    ],
    'street' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['street'],
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 255,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(255) NOT NULL default ''",
    ],
    'house_number' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['house_number'],
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 20,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(20) NOT NULL default ''",
    ],
    'fully_booked' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['fully_booked'],
      'inputType' => 'checkbox',
      'eval' => [
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
    'published' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course_date']['published'],
      'inputType' => 'checkbox',
      'eval' => [
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
  ],
];

class tl_course_date
{
  public function validateDateRange($value, DataContainer $dc)
  {
    $startDate = Input::post('start_date') ?: ($dc->activeRecord->start_date ?? null);
    $endDate = Input::post('end_date') ?: ($dc->activeRecord->end_date ?? null);

    if (!$startDate || !$endDate) {
      return $value;
    }

    $start = strtotime($startDate);
    $end = strtotime($endDate);

    if ($start === false || $end === false) {
      return $value;
    }

    if ($end < $start) {
      throw new \Exception('Das Enddatum darf nicht vor dem Startdatum liegen.');
    }

    return $value;
  }

  public function validateDateOverlap($value, DataContainer $dc)
  {
    $startDate = Input::post('start_date') ?: ($dc->activeRecord->start_date ?? null);
    $endDate = Input::post('end_date') ?: ($dc->activeRecord->end_date ?? null);

    $addTime = Input::post('add_time') ?: ($dc->activeRecord->add_time ?? null);
    $startTime = Input::post('start_time') ?: ($dc->activeRecord->start_time ?? null);
    $endTime = Input::post('end_time') ?: ($dc->activeRecord->end_time ?? null);

    $pid = $dc->activeRecord->pid ?? Input::get('pid');
    $currentId = $dc->id ?? 0;

    if (!$startDate || !$endDate || !$pid) {
      return $value;
    }

    $start = strtotime($startDate);
    $end = strtotime($endDate);

    if ($start === false || $end === false) {
      return $value;
    }

    if ($end < $start) {
      return $value;
    }

    $result = Database::getInstance()
      ->prepare("
            SELECT id, start_date, end_date, add_time, start_time, end_time
            FROM tl_course_date
            WHERE pid = ?
              AND id != ?
              AND start_date <= ?
              AND end_date >= ?
        ")
      ->execute($pid, $currentId, $end, $start);

    while ($result->next()) {
      $existingStart = (int) $result->start_date;
      $existingEnd = (int) $result->end_date;

      if (!is_numeric($result->start_date)) {
        $existingStart = strtotime($result->start_date);
      }
      if (!is_numeric($result->end_date)) {
        $existingEnd = strtotime($result->end_date);
      }

      if ($existingStart === false || $existingEnd === false) {
        continue;
      }

      if ($existingStart > $end || $existingEnd < $start) {
        continue;
      }

      $existingHasTime = !empty($result->add_time) && !empty($result->start_time) && !empty($result->end_time);
      $newHasTime = !empty($addTime) && !empty($startTime) && !empty($endTime);

      if ($existingHasTime && $newHasTime && $existingStart === $start && $existingEnd === $end) {
        $newStartMinutes = $this->timeToMinutes($startTime);
        $newEndMinutes = $this->timeToMinutes($endTime);

        $existingStartMinutes = $this->timeToMinutes($result->start_time);
        $existingEndMinutes = $this->timeToMinutes($result->end_time);

        if (
          $newStartMinutes < $existingEndMinutes &&
          $newEndMinutes > $existingStartMinutes
        ) {
          throw new \Exception('Der Kurstermin überschneidet sich mit einem bestehenden Termin.');
        }

        continue;
      }

      throw new \Exception('Der Kurstermin überschneidet sich mit einem bestehenden Termin.');
    }

    return $value;
  }

  private function timeToMinutes(string $time): int
  {
    $timestamp = strtotime($time);

    if ($timestamp === false) {
      return 0;
    }

    return ((int) date('H', $timestamp) * 60) + (int) date('i', $timestamp);
  }

  public function validateTimeRange($value, DataContainer $dc)
  {
    $addTime = Input::post('add_time') ?: ($dc->activeRecord->add_time ?? null);
    $startTime = Input::post('start_time') ?: ($dc->activeRecord->start_time ?? null);
    $endTime = Input::post('end_time') ?: ($dc->activeRecord->end_time ?? null);

    if (!$addTime) {
      return $value;
    }

    if (!$startTime || !$endTime) {
      return $value;
    }

    $start = strtotime($startTime);
    $end = strtotime($endTime);

    if ($start === false || $end === false) {
      return $value;
    }

    if ($end <= $start) {
      throw new \Exception('Die Endzeit muss nach der Startzeit liegen.');
    }

    return $value;
  }
}
