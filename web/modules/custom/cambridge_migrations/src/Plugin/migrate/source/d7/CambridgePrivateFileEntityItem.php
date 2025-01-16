<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\media_migration\Plugin\migrate\source\d7\FileEntityItem;

/**
 * File Entity Item source plugin.
 *
 * Available configuration keys:
 * - type: (optional) If supplied, this will only return fields
 *   of that particular type.
 *
 * @MigrateSource(
 *   id = "cambridge_d7_private_file_entity_item",
 *   source_module = "file_entity",
 * )
 */
class CambridgePrivateFileEntityItem extends FileEntityItem {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $result = parent::prepareRow($row);

    $row->setSourceProperty('bundle', 'private_document');

    return $result;
  }

}
