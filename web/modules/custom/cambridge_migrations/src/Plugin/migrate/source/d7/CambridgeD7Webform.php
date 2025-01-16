<?php
namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\webform_migrate\Plugin\migrate\source\d7\D7Webform;
use Drupal\migrate\Event\RollbackAwareInterface;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
use Drupal\webform\Entity\Webform;
use Drupal\node\Entity\Node;
use Drupal\webform\Utility\WebformYaml;
use Drupal\Component\Utility\Bytes;

/**
* Drupal 7 webform source from database.
*
* @MigrateSource(
*   id = "cambridge_d7_webform",
*   core = {7},
*   source_module = "webform",
*   destination_module = "webform"
* )
*/
class CambridgeD7Webform extends D7Webform {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $nid = $row->getSourceProperty('nid');
    $result = parent::prepareRow($row);
    $elements = WebformYaml::decode($row->getSourceProperty('elements'));

    foreach($elements as $key => $element) {

      // Add the start and end date from Drupal 7.
      if ($element['#type'] == 'date') {
        $query = $this->select('webform_component', 'wc');
        $query->addField('wc', 'extra');
        $query->range(0, 1);
        $extra_string = $query->condition('nid', $nid)->condition('form_key', $key)->execute()->fetchField();
        if (is_string($extra_string) && $extra = unserialize($extra_string)) {
          $elements[$key]['#date_date_min'] = isset($extra['start_date']) ? $extra['start_date'] : '-2 years';
          $elements[$key]['#date_date_max'] = isset($extra['end_date']) ? $extra['end_date'] : '+2 years';
        }
      }

      // Fix the option groups.
      if ($element['#type'] == 'select') {
        $new_options = [];
        $parent = '';
        foreach($element['#options'] as $value => $option) {
          if (empty($option)) {
            $parent = $value;
            $new_options[$value] = [];
          }
          elseif (!empty($parent)) {
            $new_options[$parent][$value] = $option;
          }
          else {
            $new_options[$value] = $option;
          }
        }
        // Overwrite the options only if you found an option group.
        if (!empty($parent)) {
          $elements[$key]['#options'] = $new_options;
        }
      }

    }

    $row->setSourceProperty('elements', WebformYaml::encode($elements));

    return $result;
  }

}