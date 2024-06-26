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

    public function generateBlock() {
        $config = \Drupal::config('gjk4all_p2000.settings');
        $page_title = $config->get('gjk4all_p2000.page_title');
        $type = $config->get('gjk4all_p2000.date_type');
        $formats = array_keys(\Drupal::entityTypeManager()->getStorage('date_format')->loadMultiple());
        $formatter = \Drupal::service('date.formatter');

        $cid = 'gjk4all_p2000:' . \Drupal::languageManager()->getCurrentLanguage()->getId();

        $element = NULL;

        if ($cache = \Drupal::cache()->get($cid)) {
            $element = $cache->data;
        }
        if ($element == NULL) {
            $row = $this->getP2000Message();

            if ($row != false) {
                $element['#cache']['max-age'] = 60;
                $element['#source_text'] = [];
                $element['#source_text'][] = [
                    'class' => 'p2000_line1',
                    'text' => Html::escape(t("Message from time:") . " " .
                        $formatter->format($row['p2000_datumtijd'], "long")
                    ),
                ];
                $element['#source_text'][] = [
                    'class' => 'p2000_line2',
                    'text' => Html::escape($row['p2000_bericht']),
                ];
                $element['#title'] = Html::escape($page_title);
                $element['#theme'] = 'gjk4all_p2000';
            }

            \Drupal::cache()->set($cid, $element, time() + 60, ['block:P2000']); // 60 sec cache
        }

        return $element;
    }

    private function getP2000Message() {
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
