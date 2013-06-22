<?php
App::uses('NotificationAppModel', 'Notification.Model');
/**
 * Notification Model
 *
 * @property User $User
 * @property Subject $Subject
 */
class Notification extends NotificationAppModel {

	/**
	 * Display field
	 *
	 * @var string
	 */
	public $displayField = 'type';

	/**
	 * Validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'read' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	// public $belongsTo = array(
	// 	'User' => array(
	// 		'className' => 'User',
	// 		'foreignKey' => 'user_id',
	// 	)
	// );


	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Subject' => array(
			'className'  => 'Notification.Subject',
			'foreignKey' => 'notification_id',
			'dependent'  => true,
		)
	);

	public function get($options = array()){
		$notifications = $this->find('all', $options);
		$ids = Set::classicExtract($notifications, '{n}.Notification.id');
		$subjects = $this->Subject->findAllByNotificationId($ids);
		foreach ($notifications as $k => $notification) {
			$s = Set::extract('/.[notification_id='.$notification['Notification']['id'].']', $subjects);
			foreach ($s as $t) {
				$notifications[$k][$t['model']] = $t[$t['model']];
			}
		}
		return $notifications;
	}

	public function getUnread($user_id, $limit = false){
		return $this->get(array(
			'conditions' => array(
				'Notification.user_id' => $user_id,
				'Notification.read' => false
			),
			'limit' => $limit,
		));
	}

}
