<?php

namespace Drupal\vegas\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\file\FileInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class FormVegasConfiguration extends ConfigFormBase {

	public function getFormId() {
	    return 'vegas_configuration_form';
	}

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'vegas.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
	  
	$config = $this->config('vegas.settings');	

	// Set up the vertical tabs.
	$form['settings'] = array(
	  '#type' => 'vertical_tabs',
	  '#weight' => 50,
	);

	// Set up the tabs.
	$form['configuration'] = array(
	  '#type' => 'details',
	  '#title' => t('Configuration'),
	  '#description'=> t('Provide general configuration for how the images are displayed.'),
	  '#group' => 'settings',
	);

	$form['images'] = array(
	  '#type' => 'details',
	  '#title' => t('Images'),
	  '#description'=> t('Configure which images should be presented as background images.'),
	  '#group' => 'settings',
	);

	// Images
	$count = 10;
	for ($i = 0; $i < $count; $i++) {
	  //$image = variable_get('vegas_images', '');
	  $image = array();
	  $form['images']['vegas_images_' . $i] = array(
	    '#type' => 'managed_file',
	    '#default_value' => $config->get('vegas_images_' . $i),
	    '#upload_location' => 'public://vegas/',
	    '#upload_validators' => array(
	      'file_validate_extensions' => array(
	        0 => 'png jpg gif jpeg',
	      ),
	    ),
	  );
	}

	// Overlay
	$form['configuration']['vegas_overlay'] = array(
	  '#type' => 'managed_file',
	  '#title' => t('Overlay'),
	  '#description' => t('The overlay will be placed on top of the image to give it a neat effect.'),
	  '#default_value' => $config->get('vegas_overlay'),
	  '#upload_location' => 'public://vegas/',
	  '#upload_validators' => array(
	    'file_validate_extensions' => array(
		  0 => 'png jpg gif jpeg',
		),
	  ),
	);

	// Fade
	$form['configuration']['vegas_fade'] = array(
	  '#title' => t('Fade'),
	  '#type' => 'select',
	  '#description' => t('Transition time between slides.'),
	  '#default_value' => $config->get('vegas_fade'),
	  '#options' => array(
	    0 => t('None'),
	    500 => t('Half a second'),
	    1000 => t('One second'),
	    2000 => t('Two seconds'),
	    3000 => t('Three seconds'),
	    4000 => t('Four seconds'),
	    5000 => t('Five seconds'),
	  ),
	);

	// Delay
	$form['configuration']['vegas_delay'] = array(
	  '#title' => t('Delay'),
	  '#type' => 'select',
	  '#description' => t('The time taken between two slides.'),
	  '#default_value' => $config->get('vegas_delay'),
	  '#options' => array(
		  500 => t('Half a second'),
		  1000 => t('One second'),
		  2000 => t('Two seconds'),
		  3000 => t('Three seconds'),
		  4000 => t('Four seconds'),
		  5000 => t('Five seconds'),
		  6000 => t('Six seconds'),
		  7000 => t('Seven seconds'),
		  8000 => t('Eight seconds'),
		  9000 => t('Nine seconds'),
		  10000 => t('Ten seconds'),
		  11000 => t('Eleven seconds'),
		  12000 => t('Twelve seconds'),
		  13000 => t('Thirteen seconds'),
		  14000 => t('Fourteen seconds'),
		  15000 => t('Fifteen seconds'),
		  16000 => t('Sixteen seconds'),
		  17000 => t('Seventeen seconds'),
		  18000 => t('Eighteen seconds'),
		  19000 => t('Nineteen seconds'),
		  20000 => t('Twenty seconds'),
	  ),
	);

	// Shuffle
	$form['configuration']['vegas_shuffle'] = array(
	  '#type' => 'checkbox',
	  '#title' => t('Shuffle'),
	  '#description' => t('Randomize the order of the images.'),
	  '#default_value' => $config->get('vegas_shuffle'),
	);

	  return parent::buildForm($form, $form_state);

	}

	public function submitForm(array &$form, FormStateInterface $form_state) {

	  $config = \Drupal::service('config.factory')->getEditable('vegas.settings');
      $config->set('vegas_fade', $form_state->getValue('vegas_fade'));
      $config->set('vegas_delay', $form_state->getValue('vegas_delay'));
      $config->set('vegas_shuffle', $form_state->getValue('vegas_shuffle'));

      drupal_set_message(t($config->get('message.vegas_config_saved')));

      //single items
      $count = 10;
	  for ($i = 0; $i < $count; $i++) {
	    // Load the file via file.fid.
	    $image = $form_state->getValue('vegas_images_' . $i);
	    $uri = !empty($image['destination']) ? $image['destination'] : NULL;
	    $file_object = file_save_data($image, $uri, FILE_EXISTS_REPLACE);

	    if (!empty($file_object)) {
	      $config->set('vegas_images_' . $i, $form_state->getValue('vegas_images_' . $i));
	  	}
	  	else {
	      drupal_set_message(t('Failed to save the managed file'), 'error');
	  	}
  	  }

	  //overlay		
	  $image = $form_state->getValue('vegas_overlay');
	  $uri = !empty($image['destination']) ? $image['destination'] : NULL;
	  $file_object = file_save_data($image, $uri, FILE_EXISTS_REPLACE);

	  if (!empty($file_object)) {
        $config->set('vegas_overlay', $form_state->getValue('vegas_overlay'));
  	  }
  	  else {
        drupal_set_message(t('Failed to save the managed file'), 'error');
  	  }
      
      $config->save();
	  parent::buildForm($form, $form_state);

	}
}
