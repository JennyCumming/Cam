<?php

namespace Drupal\cambridge_migrations\Plugin\media_migration\file_entity;

use Drupal\media_migration\Plugin\media_migration\file_entity\FileBase;

/**
 * Document media migration plugin for local document media entities.
 *
 * @FileEntityDealer(
 *   id = "private_document",
 *   types = {"private_document"},
 *   destination_media_type_id_base = "private_document",
 *   destination_media_source_plugin_id = "file"
 * )
 */
class PrivateDocument extends FileBase {

  /**
   * {@inheritdoc}
   */
  public function getDestinationMediaSourceFieldName() {
    return 'field_media_file';
  }

}
