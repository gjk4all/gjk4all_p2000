<?php

namespace Drupal\gjk4all_p2000\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;

/**
 * Module settings form.
 */
class Gjk4allP2000Form extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gjk4all_p2000_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Form constructor.
    $form = parent::buildForm($form, $form_state);
    // Default settings.
    $config = $this->config('gjk4all_p2000.settings');
    $formats = array_keys(\Drupal::entityTypeManager()->getStorage('date_format')->loadMultiple());
    $formatter = \Drupal::service('date.formatter');

    // Page title field
    $form['page_title'] = [
      '#type' => 'textfield',
      '#title' => t('P2000 block title:'),
      '#default_value' => $config->get('gjk4all_p2000.page_title'),
      '#description' => $this->t('The title of the P2000 block.'),
    ];
    $form['capcodes'] = [
      '#type' => 'textfield',
      '#title' => t('P2000 monitored capcodes:'),
      '#default_value' => $config->get('gjk4all_p2000.capcodes'),
      '#description' => $this->t('The capcodes that will trigger P2000 block view.'),
    ];
    $form['max_age'] = [
      '#type' => 'number',
      '#title' => t('Max age:'),
      '#default_value' => $config->get('gjk4all_p2000.max_age'),
      '#description' => $this->t('The number of hours the message stays visible.'),
    ];
    $form['date_type'] = [
      '#type' => 'select',
      '#title' => t('Date format:'),
      '#options' => $formats,
      '#default_value' =>  $config->get('gjk4all_p2000.date_type'),
      '#description' => $this->t('The date format used in the block.'),
      '#field_suffix' => '<small id="edit-output" data-drupal-date-formatter="preview">Voorbeeld: ' . 
        $formatter->format(time(), $formats[ $config->get('gjk4all_p2000.date_type')], '', NULL, NULL) . 
        '</small>',
      '#ajax' => [
        'callback' => '::formAjaxCallback',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-output',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('page_title') == NULL) {
      $form_state->setErrorByName('page_title', t('Please enter a valid Page title.'));
    }
    if ($form_state->getValue('capcodes') == NULL) {
      $form_state->setErrorByName('source_text', t('Please enter at least one capcode.'));
    }
    if ($form_state->getValue('capcodes') == NULL) {
      $form_state->setErrorByName('source_text', t('Please enter at least one capcode.'));
    }
    if ($form_state->getValue('max_age') == NULL) {
      $form_state->setErrorByName('source_text', t('Please a maximum age in hours.'));
    }
    if ($form_state->getValue('date_type') == NULL) {
      $form_state->setErrorByName('source_text', t('Please enter valid PHP date format.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('gjk4all_p2000.settings');
    $config->set('gjk4all_p2000.page_title', $form_state->getValue('page_title'));
    $config->set('gjk4all_p2000.capcodes', $form_state->getValue('capcodes'));
    $config->set('gjk4all_p2000.max_age', $form_state->getValue('max_age'));
    $config->set('gjk4all_p2000.date_type', $form_state->getValue('date_type'));
    $config->save();
    return parent::submitForm($form, $form_state);
  }

  public function formAjaxCallback(array &$form, FormStateInterface $form_state) {
    $formats = array_keys(\Drupal::entityTypeManager()->getStorage('date_format')->loadMultiple());
    $formatter = \Drupal::service('date.formatter');
    $type = $form_state->getValue('date_type');

    $output = '<small id="edit-output" data-drupal-date-formatter="preview">Voorbeeld: ' . 
      $formatter->format(time(), $formats[$type], '', NULL, NULL) . 
      '</small>';

    return ['#markup' => $output];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gjk4all_p2000.settings',
    ];
  }
}
