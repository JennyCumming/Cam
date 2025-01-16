<?php
namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\migrate\Row;
use Drupal\media_migration\Plugin\migrate\source\d7\FileEntityItem;

/**
 * File Entity Item source plugin.
 *
 * @MigrateSource(
 *   id = "cambridge_d7_file_entity_item",
 *   source_module = "file_entity",
 * )
 */
class CambridgeD7FileEntityItem extends FileEntityItem {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $result = parent::prepareRow($row);

    $bundle = $row->getSourceProperty('bundle');
    $description = $row->getSourceProperty('description');
    if ($bundle == 'document' && empty($description)) {
      $fid = $row->getSourceProperty('fid');

      $query = $this->select('file_usage', 'fu');
      $query->join('node', 'n', 'n.nid = fu.id');
      $record = $query->fields('n', ['title'])
        ->condition('fu.fid', $fid)
        ->condition('n.type', 'file')
        ->execute()
        ->fetchAssoc();

      if (!empty($record['title'])) {
        $row->setSourceProperty('description', $record['title']);
      }

    }

    return $result;
  }

}