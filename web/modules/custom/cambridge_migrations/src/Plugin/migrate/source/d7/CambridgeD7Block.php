<?php
namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\block\Plugin\migrate\source\Block;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\State\StateInterface;
use Drupal\migrate\MigrateLookupInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\views\Views;

// cspell:ignore whois

/**
 * Drupal 6/7 block source from database.
 *
 * For available configuration keys, refer to the parent classes.
 *
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 * @see \Drupal\migrate\Plugin\migrate\source\SourcePluginBase
 *
 * @MigrateSource(
 *   id = "cambridge_d7_block",
 *   source_module = "block"
 * )
 */
class CambridgeD7Block extends Block {

  /**
   * The list of all views.
   *
   * @var array
   */
  protected $allViews;

  /**
   * The migrate lookup service.
   *
   * @var \Drupal\migrate\MigrateLookupInterface
   */
  protected $migrateLookup;

  /**
   * The block_content entity storage handler.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|null
   */
  protected $blockContentStorage;

  /**
   * Constructs a CambridgeD7Block object.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\migrate\MigrateLookupInterface $migrate_lookup
   *   The migrate lookup service.
   * @param \Drupal\Core\Entity\EntityStorageInterface|null $storage
   *   The block content storage object. NULL if the block_content module is
   *   not installed.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, StateInterface $state, EntityTypeManagerInterface $entity_type_manager, MigrateLookupInterface $migrate_lookup, ?EntityStorageInterface $storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state, $entity_type_manager);
    $this->migrateLookup = $migrate_lookup;
    $this->blockContentStorage = $storage;

    if (isset($configuration['skip_php'])) {
      $this->skipPHP = $configuration['skip_php'];
    }

    $this->allViews = Views::getViewsAsOptions();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration = NULL) {
    $entity_type_manager = $container->get('entity_type.manager');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('state'),
      $container->get('entity_type.manager'),
      $container->get('migrate.lookup'),
      $entity_type_manager->hasDefinition('block_content') ? $entity_type_manager->getStorage('block_content') : NULL
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    parent::query();
    return $this->select($this->blockTable, 'b')->fields('b')
      ->condition('theme', 'cambridge_theme')
      ->condition('status', 1)
      ->condition('module', ['bean', 'block', 'views'], 'IN');
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $parent_fields = parent::fields();

    $parent_fields['block_name_id'] = $this->t('Unique string to identify the block');
    $parent_fields['block_settings'] = $this->t('The block settigs required by Drupal 10');
    $parent_fields['block_visibility'] = $this->t('The block visibility required by Drupal 10');
    $parent_fields['block_plugin'] = $this->t('The block plugin required by Drupal 10');

    return $parent_fields;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    $parent_result = parent::prepareRow($row);

    // Build the unique block id.
    $theme_name =  $row->getSourceProperty('theme');
    $module_name =  $row->getSourceProperty('module');
    $delta =  $row->getSourceProperty('delta');
    $block_region =  $row->getSourceProperty('region');
    $block_title =  trim($row->getSourceProperty('title'));
    $label_display = 'visible';
    if (empty($block_title || $block_title == '<none>')) {
      $label_display = '0';
    }

    $block_id = '';
    // This Plugin imports custom blocks from D7 belonging to the cambridge_theme.
    if ($module_name == 'block' && $theme_name == 'cambridge_theme') {
      $lookup_result = $this->migrateLookup->lookup(['upgrade_d7_custom_block'], [$delta]);
      if ($lookup_result) {
        $block_id = $lookup_result[0]['id'];
      }
    }
    // This Plugin imports bean blocks from D7 belonging to the cambridge_theme.
    elseif ($module_name == 'bean' && $theme_name == 'cambridge_theme') {
      $query = $this->select('bean', 'b');
      $record = $query->fields('b', ['bid', 'vid'])
        ->condition('b.delta', $delta)
        ->orderBy('b.vid', 'DESC')
        ->execute()
        ->fetchAssoc();

      if (!empty($record['bid']) && !empty($record['vid'])) {
        $lookup_result = $this->migrateLookup->lookup(['upgrade_bean_download_block'], [$record['bid'],$record['vid']]);
        if ($lookup_result) {
          $block_id = $lookup_result[0]['id'];
        }
      }
    }
    if (!empty($block_id)) {
      /** @var \Drupal\block_content\Entity\BlockContent $block */
      $block = $this->blockContentStorage->load($block_id);
    }
    if (!empty($block)) {
      $block_uid = $block->uuid();
      if ($label_display == '0') {
        $block_title = $block->label();
      }

      // Build the block settings.
      $block_settings = [
        'id' => 'block_content:' . $block_uid,
        'label' => $block_title,
        'label_display' => $block_title == '<none>' ? '0' : $label_display,
        'provider' => 'block_content',
        'status' => boolval($row->getSourceProperty('status')),
        'info' => '',
        'view_mode' => 'full'
      ];
      $row->setSourceProperty('block_settings', $block_settings);
      // If the lookup fails, then the block_plugin is empty and the row is skipped.
      $row->setSourceProperty('block_plugin', 'block_content:' . $block_uid);
      $block_name_id = $this->generayeMachineName($theme_name . '_' . $module_name . '_' . $delta);
      $row->setSourceProperty('block_name_id', $block_name_id);
    }

    // This Plugin imports view blocks from D7 belonging to the cambridge_theme.
    $view_key = str_replace('-', ":", $delta);
    $view_exists = FALSE;
    if ($module_name == 'views' && $theme_name == 'cambridge_theme') {
      // Check if the view block exists in D10.
      $view_exists = !empty($this->allViews[$view_key]);
      // Sometimes block id is encoded.
      if (!$view_exists && $delta = $this->decodeBlockDelta($delta)) {
        $view_key = str_replace('-', ":", $delta);
        // Check if the view block exists in D10.
        $view_exists = !empty($this->allViews[$view_key]);
      }
    }

    if ($view_exists) {

      $block_label = $block_title;
      if ($block_title == '<none>') {
        /** @var \Drupal\Core\StringTranslation\TranslatableMarkup $my_view */
        $my_view = $this->allViews[$view_key];
        $my_view_args = $my_view->getArguments();
        $block_label = str_replace('_', ' ', $my_view_args['@view'] . ' ' . $my_view_args['@display']);
      }

      // Build the block settings.
      $block_settings = [
        'id' => 'views_block:' . $delta,
        'label' => $block_label,
        'label_display' => $block_title == '<none>' ? '0' : 'visible',
        'provider' => 'views',
        'views_label' => $block_title == '<none>' ? '' : $block_label,
        'items_per_page' => 'none',
        'exposed' => '',
      ];
      $row->setSourceProperty('block_settings', $block_settings);
      // If the lookup fails, then the block_plugin is empty and the row is skipped.
      $row->setSourceProperty('block_plugin', 'views_block:' . $delta);

      $block_name_id = $this->generayeMachineName($theme_name . '_views_block__' . $delta);
      $row->setSourceProperty('block_name_id', $block_name_id);
    }
    elseif ($module_name == 'views' && $theme_name == 'cambridge_theme') {
      $delta =  $row->getSourceProperty('delta');
      $variables = [
        '@block_name' => $delta,
        '@block_region' => $block_region,
      ];
      \Drupal::logger('cambridge_migrations')->notice('@block_name (@block_region) does not exists in Drupal 10, please check the view and rename the block ID', $variables);
    }

    $roles = $row->getSourceProperty('roles');
    $old_visibility = $row->getSourceProperty('visibility');
    $pages = $row->getSourceProperty('pages');

    $visibility = [];

    // If the block is assigned to specific roles, add the user_role condition.
    if ($roles) {
      $visibility['user_role'] = [
        'id' => 'user_role',
        'roles' => [],
        'context_mapping' => [
          'user' => '@user.current_user_context:current_user',
        ],
        'negate' => FALSE,
      ];

      foreach ($roles as $key => $role_id) {
        $lookup_result = $this->migrateLookup->lookup(['upgrade_d7_user_role'], [$role_id]);
        if ($lookup_result) {
          $roles[$key] = $lookup_result[0]['id'];
        }
      }
      $visibility['user_role']['roles'] = array_combine($roles, $roles);
    }

    if ($pages) {
      // 2 == BLOCK_VISIBILITY_PHP in Drupal 6 and 7.
      if ($old_visibility == 2) {
        // If the PHP module is present, migrate the visibility code unaltered.
        if ($this->moduleHandler->moduleExists('php')) {
          $visibility['php'] = [
            'id' => 'php',
            // PHP code visibility could not be negated in Drupal 6 or 7.
            'negate' => FALSE,
            'php' => $pages,
          ];
        }
        // Skip the row if we're configured to. If not, we don't need to do
        // anything else -- the block will simply have no PHP or request_path
        // visibility configuration.
        elseif ($this->skipPHP) {
          throw new MigrateSkipRowException(sprintf("The block with bid '%d' from module '%s' will have no PHP or request_path visibility configuration.", $row->getSourceProperty('bid'), $row->getSourceProperty('module')));
        }
      }
      else {
        $paths = preg_split("(\r\n?|\n)", $pages);
        foreach ($paths as $key => $path) {
          $paths[$key] = $path === '<front>' ? $path : '/' . ltrim($path, '/');
        }
        $visibility['request_path'] = [
          'id' => 'request_path',
          'negate' => !$old_visibility,
          'pages' => implode("\n", $paths),
        ];
      }
    }

    // Some special blocks need an extra visibility setting.
    switch ($delta) {
      case 'sidebar_items-block':
        $visibility['entity_bundle:node'] = [
          'id' => 'entity_bundle:node',
          'negate' => false,
          'context_mapping' => ['node' => '@node.node_route_context:node'],
          'bundles' => [
            'news_article' => 'news_article',
            'page' => 'page',
            ],
        ];
        $visibility['cambridge_sidebar_items'] = [
          'id' => 'cambridge_sidebar_items',
          'show' => 1,
          'negate' => false,
        ];
        break;
    }

    $row->setSourceProperty('block_visibility', $visibility);

    // Disable block if viibility is empty unless is a footer block.
    if (empty($visibility) && !in_array($block_region, ['footer_1', 'footer_2', 'footer_3', 'footer_4'])) {
      $row->setSourceProperty('status', 0);
    }

    return $parent_result;
  }

  /**
   * Generate a valid block name ID.
   *
   * @param string $key
   *   The original block name ID.
   *
   * @return string
   *   The processed block name ID.
   */
  private function generayeMachineName($original_block_name_id) {
    $new_value = strtolower($original_block_name_id);
    return preg_replace('/[^a-z0-9_]+/', '_', $new_value);
  }

  /**
   * Generate a valid block delta for D10.
   *
   * @param string $block_delta
   *   The original block delta.
   *
   * @return string
   *   The processed block delta.
   */
  private function decodeBlockDelta($block_delta) {
    $query = $this->select('variable', 'v')
      ->fields('v', ['value'])
      ->condition('name', 'views_block_hashes');
    $value = $query->execute()
      ->fetchField(0);

    if ($value) {
      $views_block_hashes = unserialize($value);
      if (isset($views_block_hashes[$block_delta])) {
        return $views_block_hashes[$block_delta];
      }
    }

    return false;
  }

}