<?php

$object = array(
  'dataset' => array(
    'name' => 'test_cz_2010',
    'label' => 'Test CZ 2010',
    'description' => 'Test CZ 2010',
    'currency' => 'CZK',
    'unique_keys' =>array('transaction_id'),
  ),
  'mapping' => array (
    'date' => array (
      'type' => 'value',
      'description' => 'Year',
      'label' => 'Date',
      'datetype' => 'float',
      'default_value' => '',
      'column' => 'date',
    ),
    'from' => array (
      'type' => 'entity',
      'description' => 'Organization',
      'label' => 'Organization',
      'datetype' => 'string',
      'default_value' => '',
      'column' => 'paid_by',
    ),
    'to' => array (
      'type' => 'entity',
      'description' => 'Public',
      'label' => 'Public',
      'datetype' => 'string',
      'default_value' => '',
      'column' => 'paid_to',
    ),
    'amount' => array (
      'type' => 'value',
      'description' => 'Amount',
      'label' => 'Amount',
      'datetype' => 'float',
      'default_value' => '',
      'column' => 'amount',
    ),
    'id' => array (
      'type' => 'classifier',
      'description' => 'ID',
      'label' => 'ID',
      'datetype' => 'float',
      'default_value' => '',
      'column' => 'transaction_id',
    ),
    'chapter' => array (
      'type' => 'classifier',
      'description' => 'Chapter',
      'label' => 'Chapter',
      'datetype' => 'float',
      'default_value' => '',
      'column' => 'chapter',
    ),
  ),
  'views' => array(
    array(
      'name' => 'default',
      'entity' => 'classifier',
      'label' => 'My View',
      'dimension' => 'chapter',
      'breakdown' => 'paid_by',
      'filters' => array('taxonomy' => 'os1.csv')
    ),
  ),
);

echo json_encode($object);

?>
