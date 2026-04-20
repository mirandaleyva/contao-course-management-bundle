<?php

use Contao\DC_Table;

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
      'fields' => ['start_date', 'end_date', 'location'],
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
        {location_legend},location;
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
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'date',
        'datepicker' => true,
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'sql' => "varchar(10) NOT NULL default ''",
    ],
    'add_time' => [
      'inputType' => 'checkbox',
      'eval' => [
        'submitOnChange' => true,
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
    'start_time' => [
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'time',
        'timepicker' => true,
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'sql' => "varchar(5) NOT NULL default ''",
    ],
    'end_time' => [
      'inputType' => 'text',
      'eval' => [
        'rgxp' => 'time',
        'timepicker' => true,
        'tl_class' => 'w50 wizard',
        'mandatory' => true,
      ],
      'sql' => "varchar(5) NOT NULL default ''",
    ],
    'location' => [
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 255,
        'tl_class' => 'clr long',
      ],
      'sql' => "varchar(255) NOT NULL default ''",
    ],
    'fully_booked' => [
      'inputType' => 'checkbox',
      'eval' => [
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
    'published' => [
      'inputType' => 'checkbox',
      'eval' => [
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
  ],
];
