<?php

namespace Drupal\gjk4all_p2000\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\gjk4all_p2000\Service\Gjk4allP2000Service;

 /**
 * Provides a Lorem ipsum block with which you can generate dummy text anywhere
 *
 * @Block(
 *   id = "gjk4all_p2000_block",
 *   admin_label = @Translation("GJK4All P2000 block"),
 *   category = @Translation("P2000")
 * )
 */
class Gjk4allP2000Block extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $service = \Drupal::service('gjk4all_p2000.gjk4all_p2000_service');
    return $service->generate();
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'view p2000 messages');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $config = $this->getConfiguration();
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue(
      'gjk4all_p2000_block_settings',
      $form_state->getValue('gjk4all_p2000_block_settings')
    );
  } 

}
