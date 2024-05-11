<?php

/**
 * @file
 * Contains Drupal\gjk4all_p2000\Service\Gjk4allP2000Service
 */

namespace Drupal\gjk4all_p2000\Service;

use Drupal\Component\Utility\Html;
use Drupal\Core\Database\Connection;

/**
 * Service layer for Lorem ipsum generation.
 */
class Gjk4allP2000Service {

    public function generate() {
        $config = \Drupal::config('gjk4all_p2000.settings');
        $page_title = $config->get('gjk4all_p2000.page_title');

        $element['#source_text'] = [];
        $element['#source_text'][] = Html::escape("Bla bla bla...");
        $element['#title'] = Html::escape($page_title);
        $element['#theme'] = 'gjk4all_p2000';

        return $element;
    }

    private function getP200Message() {
        $config = \Drupal::config('gjk4all_p2000.settings');
        $capcodes = $config->get('gjk4all_p2000.capcodes');
        $max_age = $config->get('gjk4all_p2000.max_age');

        $start_time = time() - ($max_age * 3600);
        
        foreach (explode(',', $capcodes) as $cap)
            $cap_array[] = trim($cap);

        $database = \Drupal::database();

        $query = $database->select('p2000', 'p');
        $query->fields('p', ['p2000_datumtijd', 'p2000_bericht']);
        $query->distinct();
        $query->condition('p2000_datumtijd', $start_time, '>=');
        $query->orderBy('p2000_datumtijd', 'desc');
        $query->range(0, 1);

        if (!empty($cap_array))
            $query->condition('p2000_capcode', $cap_array, 'IN');

        $result = $query->execute()->fetchAssoc();

        return $result;
    }
}
