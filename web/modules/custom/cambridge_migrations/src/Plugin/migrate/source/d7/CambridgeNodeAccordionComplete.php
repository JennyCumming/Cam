<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\node\Plugin\migrate\source\d7\NodeComplete;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\Row;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Drupal 7 all node revisions source, including translation revisions.
 *
 * For available configuration keys, refer to the parent classes.
 *
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 * @see \Drupal\migrate\Plugin\migrate\source\SourcePluginBase
 *
 * @MigrateSource(
 *   id = "d7_cam_node_accordion_complete",
 *   source_module = "node"
 * )
 */
class CambridgeNodeAccordionComplete extends NodeComplete
{

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected $migrateLookup;

  /**
   * The list of paragraphs fields.
   *
   * @var array
   */
  protected $paragraphsFields = [];

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, ModuleHandlerInterface $module_handler, MigrateLookupInterface $migrate_lookup)
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_type_manager, $module_handler);
    $this->migrateLookup = $migrate_lookup;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL)
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
      $container->get('entity_type.manager'),
      $container->get('module_handler'),
      $container->get('migrate.lookup')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query()
  {
    $query = parent::query();
    // $query->condition('nr.vid', 26501);
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row)
  {
    $return = parent::prepareRow($row);

    $type = $row->getSourceProperty('type');

    $migration_look_up = 'upgrade_d7_field_collection_page_item';
    if ($type == 'questions_and_answers') {
      $migration_look_up = 'upgrade_d7_field_collection_questions_and_answers';
      $field_paragraph_values = $row->getSourceProperty('field_questions_and_answers');
      $row->setSourceProperty('field_page_item', $field_paragraph_values);
    }

    $field_paragraph_values = $row->getSourceProperty('field_page_item');
    $values = [];
    foreach ($field_paragraph_values as $item) {
      try {
        $lookup_result = $this->migrateLookup->lookup([$migration_look_up], [$item['value']]);
        if (!empty($lookup_result[0])) {
          $values[] = [
            'target_id' => $lookup_result[0]['id'],
            'target_revision_id' => $lookup_result[0]['revision_id'],
          ];
        }
      } catch (PluginNotFoundException $exception) {
      }
    }

    if (!empty($values)) {
      $row->setDestinationProperty('field_page_item', $values);
    }

    return $return;
  }

}
