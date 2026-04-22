<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['course_list'] = '{title_legend},name,headline,type;{config_legend},course_order,jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['course_reader'] = '{title_legend},name,headline,type;{config_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['course_order'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_module']['course_order'],
  'exclude' => true,
  'inputType' => 'select',
  'options' => ['asc', 'desc'],
  'reference' => &$GLOBALS['TL_LANG']['tl_module']['course_order_options'],
  'eval' => [
    'tl_class' => 'w50',
    'mandatory' => true,
  ],
  'sql' => "varchar(4) NOT NULL default 'asc'",
];


$GLOBALS['TL_DCA']['tl_module']['palettes']['course_reader'] = '{title_legend},name,headline,type;{config_legend},registration_jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['registration_jumpTo'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_module']['registration_jumpTo'],
  'exclude' => true,
  'inputType' => 'pageTree',
  'eval' => [
    'fieldType' => 'radio',
    'mandatory' => true,
    'tl_class' => 'clr',
  ],
  'foreignKey' => 'tl_page.title',
  'relation' => [
    'type' => 'hasOne',
    'load' => 'lazy',
  ],
  'sql' => "int(10) unsigned NOT NULL default 0",
];
