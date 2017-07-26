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
		$form['purge'] = array(
			'#type' => 'submit',
			'#value' => $this->t('Purge Data'),
		);
		
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
			case (string)$this->t('Purge Data'):
				$content_types = array('dm_user', 'dm_publication', 'dm_author', 'dm_education', 'dm_research', 'dm_award');
				
				foreach ($content_types as $type) {
					/* delete node data */
					$nodes = \Drupal::entityTypeManager()
							->getStorage('node')
							->loadByProperties(array('type' => $type));
					
					foreach ($nodes as $node) {
						$node->delete();
					}
				}
				break;
			default:
		}
	}
}