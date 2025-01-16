<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\EntityConfigBase;

/**
 * Destination for entity css injector.
 *
 * @MigrateDestination(
 *   id = "entity:asset_injector_css"
 * )
 */
class CssInjector extends EntityConfigBase {

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $id_key = $this->getKey('id');
    $ids[$id_key]['type'] = 'string';
    return $ids;
  }

}
