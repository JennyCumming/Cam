<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\Core\Site\Settings;
use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Drupal 7 css_injector_rule source from database.
 *
 * @MigrateSource(
 *   id = "d7_css_injector",
 *   source_module = "css_injector"
 * )
 */
class CssInjector extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('css_injector_rule', 'c')->fields('c', [
      'crid',
      'title',
      'rule_type',
      'rule_conditions',
      'rule_themes',
      'media',
      'preprocess',
      'enabled',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $d7_source_base_path = Settings::get('d7_source_base_path', $this->configuration['constants']['source_base_path']);
    $css_path = $d7_source_base_path . '/' . $this->variableGet('file_public_path', 'sites/default/files');
    $css_path = $css_path . '/css_injector/css_injector_i.css';

    $fields = array(
      'crid' => $this->t('The primary identifier for the CSS injection rule'),
      'file_name_id' => $this->t('Unique string to identify the row'),
      'title' => $this->t('Title'),
      'code' => $this->t('Loaded from these files: ' . $css_path),
      'conditions' => $this->t('rule_type, rule_conditions and rule_themes are combined into 1 settins field.'),
      'media' => $this->t('Media type'),
      'preprocess' => $this->t('If 1 is included by the CSS preprocessor'),
      'enabled' => $this->t('Whether the rules should be enabled')
    );
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $crid = $row->getSourceProperty('crid');

    $record = $this->select('css_injector_rule', 'cr')
      ->fields('cr', ['media', 'rule_type', 'rule_conditions', 'rule_themes'])
      ->condition('cr.crid', $crid)
      ->execute()
      ->fetchAssoc();

    // Check if media is not allowed in D10 then set it to "all".
    $media = $record['media'];
    if (!in_array($media, ['all', 'screen', 'print'])) {
      $row->setSourceProperty('media', 'all');
    }

    $conditions = [];

    $rule_themes = !empty($record['rule_themes']) ? unserialize($record['rule_themes']) : [];
    if (!empty($rule_themes)) {
      $conditions['current_theme'] =
        [
          'id' => 'current_theme',
          'negate' => false,
          'theme' => reset($rule_themes),
        ];
    }

    $rule_conditions = !empty($record['rule_conditions']) ? $record['rule_conditions'] : '';
    if (!empty($rule_conditions)) {
      $negate = boolval($record['rule_type']) == false;
      $fixed_path = [];
      $paths = array_map('trim', explode("\n", $rule_conditions));
      foreach ($paths as $path) {
        if ($path === '<front>') {
          $fixed_path[] = $path;
        } elseif (!empty($path) && !str_starts_with($path, '/')) {
          $fixed_path[] = '/' . $path;
        }
      }

      $conditions['request_path'] =
        [
          'id' => 'request_path',
          'negate' => $negate,
          'pages' => implode("\n", $fixed_path),
        ];
    }

    $row->setSourceProperty('conditions', $conditions);

    // File name id.
    $file_name_id = 'css_injector_' . $crid;
    $row->setSourceProperty('file_name_id', $file_name_id);

    // Load the CSS code from the original D7 file.
    $d7_source_base_path = Settings::get('d7_source_base_path', $this->configuration['constants']['source_base_path']);
    $css_path = $d7_source_base_path . '/' . $this->variableGet('file_public_path', 'sites/default/files');
    $css_path = $css_path . '/css_injector/css_injector_' . $crid . '.css';
    if (file_exists($css_path)) {
      $css_code = file_get_contents($css_path);
    }
    else {
      $css_code = '';
    }
    $row->setSourceProperty('code', $css_code);

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['crid']['type'] = 'integer';
    return $ids;
  }

}
