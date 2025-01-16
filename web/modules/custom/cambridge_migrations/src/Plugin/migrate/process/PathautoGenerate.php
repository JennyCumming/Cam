<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\process;

/**
 * @file
 * Contains \Drupal\cambridge_migrations\Plugin\migrate\process\PathautoGenerate.
 */

use Drupal\Core\Database\Database;
use Drupal\migrate\Annotation\MigrateProcessPlugin;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Process plugin to get the D7 pathauto setting to auto generate url.
 *
 * @MigrateProcessPlugin(
 *   id = "cam_pathauto_generate",
 * )
 */
class PathautoGenerate extends ProcessPluginBase
{

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property)
  {
    return 0;

    switch ($row->get('plugin')) {
      case 'd7_taxonomy_term':
        $entity_type = 'taxonomy_term';
        break;
      default:
        $entity_type = 'node';
    }

    // Query the db to get the pathauto_persist value.
    $db = Database::getConnection('default', 'legacy');
    $query = $db->select('pathauto_state', 'p')
      ->fields('p', ['pathauto']);
    $query->condition('p.entity_id', $value)
      ->condition('p.entity_type', $entity_type);
    $data = $query->execute()->fetch();

    // Return boolean to indicate if url will be generated automatically or not.
    if (is_object($data)) {
      return $data->pathauto;
    }
    return 1;
  }

}
