<?php

use Contao\DC_Table;
use Contao\FormModel;
use Contao\Database;

$GLOBALS['TL_DCA']['tl_course'] = [
  'config' => [
    'dataContainer' => DC_Table::class,
    'ctable' => ['tl_course_date'],
    'enableVersioning' => true,
    'ondelete_callback' => [
      ['tl_course', 'preventDeleteIfDatesExist'],
    ],
    'sql' => [
      'keys' => [
        'id' => 'primary',
      ],
    ],
  ],

  'list' => [
    'sorting' => [
      'mode' => 1,
      'fields' => ['sorting'],
      'flag' => 1,
      'panelLayout' => 'sort,filter;search,limit',
    ],
    'label' => [
      'fields' => ['title'],
      'format' => '%s',
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
      'course_dates' => [
        'label' => &$GLOBALS['TL_LANG']['tl_course']['course_dates'],
        'href' => 'table=tl_course_date',
        'icon' => 'children.svg',
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
    'default' => '
            {general_legend},title,author;
            {description_legend},description;
            {media_legend},preview_image,form_reference;
            {publish_legend},published
        ',
  ],

  'fields' => [
    'id' => [
      'sql' => "int(10) unsigned NOT NULL auto_increment",
    ],
    'tstamp' => [
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'sorting' => [
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'title' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['title'],
      'inputType' => 'text',
      'eval' => [
        'mandatory' => true,
        'maxlength' => 255,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(255) NOT NULL default ''",
    ],
    'author' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['author'],
      'inputType' => 'text',
      'eval' => [
        'maxlength' => 255,
        'tl_class' => 'w50',
      ],
      'sql' => "varchar(255) NOT NULL default ''",
    ],
    'description' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['description'],
      'inputType' => 'textarea',
      'eval' => [
        'rte' => 'tinyMCE',
        'tl_class' => 'clr',
      ],
      'sql' => "text NULL",
    ],
    'preview_image' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['preview_image'],
      'inputType' => 'fileTree',
      'eval' => [
        'files' => true,
        'fieldType' => 'radio',
        'extensions' => 'jpg,jpeg,png,webp',
        'tl_class' => 'clr',
      ],
      'sql' => "binary(16) NULL",
    ],
    'form_reference' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['form_reference'],
      'inputType' => 'select',
      'options_callback' => ['tl_course', 'getForms'],
      'eval' => [
        'mandatory' => true,
        'includeBlankOption' => true,
        'chosen' => true,
        'tl_class' => 'w50',
      ],
      'sql' => "int(10) unsigned NOT NULL default 0",
    ],
    'published' => [
      'label' => &$GLOBALS['TL_LANG']['tl_course']['published'],
      'inputType' => 'checkbox',
      'eval' => [
        'tl_class' => 'w50 m12',
      ],
      'sql' => "char(1) NOT NULL default ''",
    ],
  ],
];

class tl_course
{
  public function getForms(): array
  {
    $options = [];
    $forms = FormModel::findAll();

    if ($forms !== null) {
      while ($forms->next()) {
        $options[$forms->id] = $forms->title;
      }
    }

    return $options;
  }

  public function preventDeleteIfDatesExist(DataContainer $dc): void
  {
    if (!$dc->id) {
      return;
    }

    $count = Database::getInstance()
      ->prepare('SELECT COUNT(*) AS count FROM tl_course_date WHERE pid = ?')
      ->execute($dc->id)
      ->count;

    if ($count > 0) {
      throw new \Exception('Der Kurs kann nicht gelöscht werden, solange noch Kurstermine vorhanden sind.');
    }
  }
}
