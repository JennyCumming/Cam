<?php

namespace Drupal\cam_twigextension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides the cam_twigextension.function twig extension.
 */
class TwigFunctions extends AbstractExtension {

  /**
   * {@inheritdoc}
   *
   * @return string
   *   Name of the function.
   */
  public function getName() {
    return 'cam_twigextension.function';
  }

  /**
   * Declare your custom twig extension here.
   *
   * @return array|\Twig\TwigFunction[]
   *   An array of twig extensions.
   */
  public function getFunctions() {
    return [
      new TwigFunction('get_themecolor', [$this, 'getThemeColor']),
    ];
  }

  /**
   * Function to return the current chosen site theme colour.
   *
   * @return string
   *   Site color chosen in the site theme configuration settings as a string
   */
  public static function getThemeColor() {
    return \Drupal::config('cambridge_tailwind.settings')->get('color');
  }

}
