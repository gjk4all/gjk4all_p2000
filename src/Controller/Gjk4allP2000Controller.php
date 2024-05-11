<?php

/**
 * @file
 * Contains \Drupal\gjk4all_p2000\Controller\Gjk4allP2000Controller
 */

namespace Drupal\gjk4all_p2000\Controller;

use Drupal\Component\Utility\Html;
use Drupal\gjk4all_p2000\Service\Gjk4allP2000Service;

/**
 * Controller routines for igjk4all p2000 pages.
 */
class Gjk4allP2000Controller {

  /**
   * Constructs gjk4all p2000 text.
   * This callback is mapped to the path
   * 'loremipsum/generate/{lorem}/{ipsum}'.
   *
   * @var \Drupal\gjk4all_p2000\Service\Gjk4allP2000Service $Gjk4allP2000Service
   *   A call to the gjk4all p2000 service.
   */

  // The themeable element.
  protected $element = [];

  // The generate method which stores lorem ipsum text in a themeable element.
  public function generate() {
    $Gjk4allP2000Service = \Drupal::service('gjk4all_p2000.gjk4all_p2000_service');
    $element = $Gjk4allP2000Service->generate();

    return $element;
  }

}
