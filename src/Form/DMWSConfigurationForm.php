<?php
/**
 * @file
 * Contains \Drupal\dmsync\Form\R25ConfigurationForm.
 */
namespace Drupal\dmsync\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * DMWS Configuration Form.
 */
class DMWSConfigurationForm extends FormBase {
	/**
	* {@inheritdoc}
	*/
	public function getFormId() {
		return 'dmws_configuration_form';
	}
	/**
	* {@inheritdoc}
	*/
	public function buildForm(array $form, FormStateInterface $form_state) {
		$config = \Drupal::config('dmsync.configuration');
		
		$queue = \Drupal::queue('dmsync_queue');
		
		$form['help'] = array(
		  '#type' => 'markup',
		  '#markup' => $this->t('There are @number items remaing in the proccessing queue.', array('@number' => $queue->numberOfItems())),
		);
			
		$form['account_credentials'] = array(
		  '#type' => 'fieldset',
		  '#title' => $this->t('Account Credentials'),
		);
		$form['account_credentials']['username'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Username'),
			'#default_value' => $config->get('username'),
			'#size' => 32,
		);
		$form['account_credentials']['password'] = array(
			'#type' => 'password',
			'#title' => $this->t('Password'),
			'#size' => 32,
		);
		$form['submit'] = array(
			'#type' => 'submit',
			'#value' => $this->t('Save Configuration'),
		);
		/*$form['refresh'] = array(
			'#type' => 'submit',
			'#value' => $this->t('Refresh Data'),
		);*/
		
		return $form;
	}
	/**
	* {@inheritdoc}
	*/
	public function validateForm(array &$form, FormStateInterface $form_state) {
		
	}
	/**
	* {@inheritdoc}
	*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$values = $form_state->getValues();
		
		switch($values['op']) {
			case (string)$this->t('Save Configuration'):
				$config = \Drupal::service('config.factory')->getEditable('dmsync.configuration');
				
				foreach ($values as $key => $value) {
					if ($key != "submit" &&
						$key != "refresh" &&
						$key != "form_build_id" &&
						$key != "form_token" &&
						$key != "form_id" &&
						$key != "op")
							if (!empty($value))
								$config->set($key, $value);
				}
				
				$config->save();
				break;
			/*case (string)$this->t('Refresh Data'):
				if (r25sync_update()) {
					drupal_set_message($this->t("Data refreshed."));
				} else {
					drupal_set_message($this->t("Data refresh failed. Please check configuration."));
				}
				break;*/
			default:
		}
	}
}