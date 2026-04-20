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
        ['tl_course_date', 'validateEndDate'],
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
        ['tl_course_date', 'validateEndTime'],
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
  public function validateEndDate($value, DataContainer $dc)
  {
    $startDate = $dc->activeRecord->start_date;

    if ($dc->field === 'start_date') {
      $startDate = $value;
    }

    if (!$startDate || !$value) {
      return $value;
    }

    $start = strtotime($startDate);
    $end = strtotime($value);

    if ($end < $start) {
      throw new \Exception('Das Enddatum darf nicht vor dem Startdatum liegen.');
    }

    return $value;
  }

  public function validateDateOverlap($value, DataContainer $dc)
  {
    $startDate = \Contao\Input::post('start_date') ?: $dc->activeRecord->start_date;
    $pid = $dc->activeRecord->pid;
    $currentId = $dc->id;

    if (!$startDate || !$value || !$pid) {
      return $value;
    }

    $start = strtotime($startDate);
    $end = strtotime($value);

    // Nur prüfen wenn logisch korrekt
    if ($end < $start) {
      return $value;
    }

    $result = \Contao\Database::getInstance()
      ->prepare("
                SELECT id
                FROM tl_course_date
                WHERE pid = ?
                AND id != ?
                AND start_date <= ?
                AND end_date >= ?
            ")
      ->execute($pid, $currentId ?: 0, $value, $startDate);

    if ($result->numRows > 0) {
      throw new \Exception('Der Kurstermin überschneidet sich mit einem bestehenden Termin.');
    }

    return $value;
  }

  public function validateEndTime($value, DataContainer $dc)
  {
    $addTime = \Contao\Input::post('add_time');
    $startTime = \Contao\Input::post('start_time');

    if ($addTime && $startTime && $value) {
      if (strtotime($value) <= strtotime($startTime)) {
        throw new \Exception('Die Endzeit muss nach der Startzeit liegen.');
      }
    }

    return $value;
  }
}
