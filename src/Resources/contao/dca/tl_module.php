<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['course_list'] = '{title_legend},name,headline,type;{config_legend},course_order,jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['course_reader'] = '{title_legend},name,headline,type;{config_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['fields']['course_order'] = [
  'label' => &$GLOBALS['TL_LANG']['tl_module']['course_order'],
  'exclude' => true,
  'inputType' => 'select',
  'options' => [
    'asc',
    'desc',
  ],
  'reference' => &$GLOBALS['TL_LANG']['tl_module']['course_order_options'],
  'eval' => [
    'tl_class' => 'w50',
    'includeBlankOption' => false,
  ],
  'sql' => "varchar(4) NOT NULL default 'asc'",
];
