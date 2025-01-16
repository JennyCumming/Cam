<?php
namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\Core\Site\Settings;
use Drupal\migrate\Row;
use Drupal\file\Plugin\migrate\source\d7\File;

/**
 * Drupal 7 file source from database.
 *
 * @MigrateSource(
 *   id = "cambridge_d7_file",
 *   source_module = "file"
 * )
 */
class CambridgeD7File extends File {

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = parent::fields();
    $args['@d7_source_base_path'] = Settings::get('d7_source_base_path', $this->configuration['constants']['source_base_path']);
    $fields['d7_source_base_path'] = $this->t('File location: @d7_source_base_path', $args);
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $d7_source_base_path = Settings::get('d7_source_base_path', $this->configuration['constants']['source_base_path']);
    $row->setSourceProperty('d7_source_base_path', $d7_source_base_path);

    // Compute the filepath property, which is a physical representation of
    // the URI relative to the Drupal root.
    $path = str_replace(['public:/', 'private:/'], ['', ''], $row->getSourceProperty('uri'));

    $row->setSourceProperty('filepath', $path);
    return parent::prepareRow($row);
  }

}