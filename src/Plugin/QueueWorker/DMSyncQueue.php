<?php
/**
 * @file
 * Contains \Drupal\dmsync\Plugin\QueueWorker\DMSyncQueue.
 */
namespace Drupal\dmsync\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes Tasks for Learning.
 *
 * @QueueWorker(
 *   id = "dmsync_queue",
 *   title = @Translation("DMSyncQueue"),
 *   cron = {"time" = 60}
 * )
 */
class DMSyncQueue extends QueueWorkerBase {
	/**
	* {@inheritdoc}
	*/
	public function processItem($data) {
		if (isset($data['type'])) {
			switch($data['type']) {
				case 'user':
					$this->processUser($data);
					break;
				case 'publication':
					$this->processPublication($data);
					break;
				case 'author':
					$this->processAuthor($data);
					break;
				default:
					/* unsupported type */
					break;
			}
		}
	}
	
	private function processUser($data) {
		$query = \Drupal::entityQuery('node')
					->condition('type', 'dm_user')
					->condition('field_dm_user_userid', $data['userid'], '=')
					->range(0, 1);
					
		$result = $query->execute();
		
		/* determine if user node exists */
		if (count($result)) {
			/* load node */
			$nid = array_values($result)[0];
			$node = \Drupal\node\Entity\Node::load($nid);
			
			/* determine if the data has changed */				
			$node->revision = TRUE;
			$node->changed = REQUEST_TIME;
			
			if ($node->title != $data['title']) $node->title = $data['title'];
			if ($node->field_dm_user_username != $data['username']) $node->field_dm_user_username = $data['username'];
			if ($node->field_dm_user_srank != $data['srank']) $node->field_dm_user_srank = $data['srank'];
			if ($node->field_dm_user_roomnum != $data['roomnum']) $node->field_dm_user_roomnum = $data['roomnum'];
			if ($node->field_dm_user_prefix != $data['prefix']) $node->field_dm_user_prefix = $data['prefix'];
			if ($node->field_dm_user_mname != $data['mname']) $node->field_dm_user_mname = $data['mname'];
			if ($node->field_dm_user_lname != $data['lname']) $node->field_dm_user_lname = $data['lname'];
			if ($node->field_dm_user_fname != $data['fname']) $node->field_dm_user_fname = $data['fname'];
			if ($node->field_dm_user_expertise != $data['expertise']) $node->field_dm_user_expertise = $data['expertise'];
			if ($node->field_dm_user_building != $data['building']) $node->field_dm_user_building = $data['building'];
			if ($node->field_dm_user_bio != $data['bio']) $node->field_dm_user_bio = $data['bio'];
			if ($node->field_dm_user_email != $data['email']) $node->field_dm_user_email = $data['email'];
			if ($node->field_dm_user_ophone != $data['ophone']) $node->field_dm_user_ophone = $data['ophone'];

			$node->save();
		} else {
			$node = \Drupal\node\Entity\Node::create([
				'type' => 'dm_user',
				'langcode' => 'en',
				'created' => REQUEST_TIME,
				'changed' => REQUEST_TIME,
				'uid' => 1,
				'title' => $data['title'],
				'field_dm_user_username' => $data['username'],
				'field_dm_user_userid' => $data['userid'],
				'field_dm_user_srank' => $data['srank'],
				'field_dm_user_roomnum' => $data['roomnum'],
				'field_dm_user_prefix' => $data['prefix'],
				'field_dm_user_mname' => $data['mname'],
				'field_dm_user_lname' => $data['lname'],
				'field_dm_user_fname' => $data['fname'],
				'field_dm_user_expertise' => $data['expertise'],
				'field_dm_user_building' => $data['building'],
				'field_dm_user_bio' => $data['bio'],
				'field_dm_user_email' => $data['email'],
				'field_dm_user_ophone' => $data['ophone'],
			]);
		
			$node->setPromoted(false);
		
			$node->save();
		}
	}
	
	private function processPublication($data) { 
		$query = \Drupal::entityQuery('node')
					->condition('type', 'dm_publication')
					->condition('field_dm_publication_id', $data['id'], '=')
					->range(0, 1);
					
		$result = $query->execute();
		
		/* determine if publication node exists */
		if (count($result)) {
			/* load node */
			$nid = array_values($result)[0];
			$node = \Drupal\node\Entity\Node::load($nid);
			
			/* determine if the data has changed */
			$node->revision = TRUE;
			$node->changed = REQUEST_TIME;
			
			if ($node->title != $data['title'])	$node->title = $data['title'];
			if ($node->field_dm_publication_abstract != $data['abstract']) $node->field_dm_publication_abstract = $data['abstract'];
			if ($node->field_dm_publication_class != $data['class']) $node->field_dm_publication_class = $data['class'];
			if ($node->field_dm_publication_contype != $data['contype']) $node->field_dm_publication_contype = $data['contype'];
			if ($node->field_dm_publication_isbn != $data['isbn']) $node->field_dm_publication_isbn = $data['isbn'];
			if ($node->field_dm_publication_issue != $data['issue']) $node->field_dm_publication_issue = $data['pagenum'];
			if ($node->field_dm_publication_pagenum != $data['pagenum']) $node->field_dm_publication_pagenum = $data['pub_end'];
			if ($node->field_dm_publication_pub_end != $data['pub_end']) $node->field_dm_publication_pub_end = $data['publicavail'];
			if ($node->field_dm_publication_publicavail != $data['publicavail']) $node->field_dm_publication_publicavail = $data['publicavail'];
			if ($node->field_dm_publication_publisher != $data['publisher']) $node->field_dm_publication_publisher = $data['publisher'];
			if ($node->field_dm_publication_refereed != $data['refereed']) $node->field_dm_publication_refereed = $data['refereed'];
			if ($node->field_dm_publication_status != $data['status']) $node->field_dm_publication_status = $data['status'];
			if ($node->field_dm_publication_volume != $data['volume']) $node->field_dm_publication_volume = $data['volume'];

			$node->save();		
		} else {
			/* create new node */
			$node = \Drupal\node\Entity\Node::create([
				'type' => 'dm_publication',
				'langcode' => 'en',
				'created' => REQUEST_TIME,
				'changed' => REQUEST_TIME,
				'uid' => 1,
				'title' => $data['title'],
				'field_dm_publication_abstract' => $data['abstract'],
				'field_dm_publication_class' => $data['class'],
				'field_dm_publication_contype' => $data['contype'],
				'field_dm_publication_id' => $data['id'],
				'field_dm_publication_isbn' => $data['isbn'],
				'field_dm_publication_issue' => $data['issue'],
				'field_dm_publication_pagenum' => $data['pagenum'],
				'field_dm_publication_pub_end' => $data['pub_end'],
				'field_dm_publication_publicavail' => $data['publicavail'],
				'field_dm_publication_publisher' => $data['publisher'],
				'field_dm_publication_refereed' => $data['refereed'],
				'field_dm_publication_status' => $data['status'],
				'field_dm_publication_volume' => $data['volume'],
			]);
			
			$node->setPromoted(false);
			$node->save();
		}
	}
	
	private function processAuthor($data) {
		/* get user id */
		$query = \Drupal::entityQuery('node')
				->condition('type', 'dm_user')
				->condition('field_dm_user_userid', $data['user'], '=')
				->range(0, 1);
				
		$result = $query->execute();
		
		if (!count($result)) return;	/* user does not exist */
		$user_nid = array_values($result)[0];
		
		/* get publication id */
		$query = \Drupal::entityQuery('node')
				->condition('type', 'dm_publication')
				->condition('field_dm_publication_id', $data['publication'], '=')
				->range(0, 1);
				
		$result = $query->execute();
		if (!count($result)) return;
		$publication_nid = array_values($result)[0];
		
		$query = \Drupal::entityQuery('node')
				->condition('type', 'dm_author')
				->condition('title', $data['title'], '=')
				->range(0, 1);
			
		$result = $query->execute();
		
		/* determine if author node exists */
		if (count($result)) {
			/* load node */
			$nid = array_values($result)[0];
			$node = \Drupal\node\Entity\Node::load($nid);
			
			/* determine if the data has changed */
			$node->revision = TRUE;
			$node->changed = REQUEST_TIME;
			
			if ($node->title != $data['title']) $node->title = $data['title'];
			if ($node->field_dm_author_publication != $publication_nid) $node->field_dm_author_publication = $publication_nid;
			if ($node->field_dm_author_user != $user_nid) $node->field_dm_author_user = $user_nid;
			if ($node->field_dm_author_role != $data['role']) $node->field_dm_author_role = $data['role'];
			
			$node->save();		
		} else {
			/* create new node */
			$node = \Drupal\node\Entity\Node::create([
				'type' => 'dm_author',
				'langcode' => 'en',
				'created' => REQUEST_TIME,
				'changed' => REQUEST_TIME,
				'uid' => 1,
				'title' => $data['title'],
				'field_dm_author_publication' => $publication_nid,
				'field_dm_author_user' => $user_nid,
				'field_dm_author_role' => $data['role'],
			]);
			
			$node->setPromoted(false);
			$node->save();
		}
	}
}