<?php

namespace Drupal\cam_twigextension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Provides the cam_twigextension.filter twig extension.
 */
class TwigFilters extends AbstractExtension {

  /**
   * Get the service name.
   *
   * @return string
   *   The service name.
   */
  public function getName() {
    return 'cam_twigextension.filter';
  }

  /**
   * Returns array of Filters that can be used in Twig.
   *
   * @return array
   *   Array of Twig Filters
   */
  public function getFilters() {
    $filters = [
      new TwigFilter('strip_all', [$this, 'stripAll']),
    ];
    return $filters;
  }

  /**
   * Strips all markup, spaces, new-lines, tabs and returns from a string.
   *
   * Sample twig usage: {{ '\m\r<p>test content</p>' | stripAll }}
   *
   * @param string $text
   *   String to convert.
   *
   * @return string
   *   The converted string.
   */
  public static function stripAll($text) {
    if (!empty($text)) {
      $new_text = trim(strip_tags($text));
      $new_text = preg_replace('/\s+/', '', $new_text);
      $string = htmlentities($new_text, ENT_NOQUOTES, 'utf-8');
      $content = str_replace("&nbsp;", "", $string);
      $content = trim(html_entity_decode($content));
      return $content;
    }
    return '';
  }

}
