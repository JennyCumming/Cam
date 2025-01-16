<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\paragraphs\Plugin\migrate\source\d7\FieldCollectionItem;
use Drupal\migrate\Row;

/**
 * Field Collection Item source plugin.
 *
 * Available configuration keys:
 * - field_name: (optional) If supplied, this will only return field collections
 *   of that particular type.
 *
 * @MigrateSource(
 *   id = "d7_cam_field_collection_item",
 *   source_module = "field_collection",
 * )
 */
class CambridgeFieldCollectionItem extends FieldCollectionItem
{

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $row->setDestinationProperty('parent_field_name', 'field_page_item');
    $row->setDestinationProperty('parent_type', 'node');
    $row->setDestinationProperty('type', 'page_item');
    $row->setSourceProperty('bundle', 'page_item');

    return parent::prepareRow($row);
  }

}
