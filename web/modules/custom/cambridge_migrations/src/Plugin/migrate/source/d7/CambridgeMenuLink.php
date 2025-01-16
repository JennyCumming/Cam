<?php

namespace Drupal\cambridge_migrations\Plugin\migrate\source\d7;

use Drupal\Component\Utility\Unicode;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;
use Drupal\migrate\Row;
use Drupal\menu_link_content\Plugin\migrate\source\MenuLink;

/**
 * Drupal 6/7 menu link source from database.
 *
 * Available configuration keys:
 * - menu_name: (optional) The menu name(s) to filter menu links from the source
 *   can be a string or an array. If not declared then menu links of all menus
 *   are retrieved.
 *
 * Examples:
 *
 * @code
 * source:
 *   plugin: cambridge_menu_link
 *   menu_name: main-menu
 * @endcode
 *
 * In this example menu links of main-menu are retrieved from the source
 * database.
 *
 * @code
 * source:
 *   plugin: cambridge_menu_link
 *   menu_name: [main-menu, navigation]
 * @endcode
 *
 * In this example menu links of main-menu and navigation menus are retrieved
 * from the source database.
 *
 * For additional configuration keys, refer to the parent classes:
 * @see \Drupal\migrate\Plugin\migrate\source\SqlBase
 * @see \Drupal\migrate\Plugin\migrate\source\SourcePluginBase
 *
 * @MigrateSource(
 *   id = "cambridge_menu_link",
 *   source_module = "menu"
 * )
 */
class CambridgeMenuLink extends MenuLink {

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // $row->setSourceProperty('options', unserialize($row->getSourceProperty('options')));

    $router_path = $row->getSourceProperty('router_path');
    $link_path = $row->getSourceProperty('link_path');
    if ($router_path == '<firstchild>' || $link_path == '<firstchild>') {
      $row->setSourceProperty('router_path', '<none>');
      $row->setSourceProperty('link_path', 'route:<none>');
      $options = unserialize($row->getSourceProperty('options'));
      $options['menu_firstchild'] = ['enabled' => 1];
      $row->setSourceProperty('options', serialize($options));
    }

    return parent::prepareRow($row);
  }

}
