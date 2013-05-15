<?php
App::uses('AuthComponent', 'Controller/Component');
/**
 * User Model
 *
 */
class User extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nome é obrigatório',
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'E-mail inválido',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'E-mail é obrigatório',
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Senha é obrigatório',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'A senha deve ter pelo menos 6 caracteres'
			),
		),
	);

/**
 * Before save methods
 *
 */

	public function beforeSave($options = array()) {
		// Hash passwords
		$this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
		return true;
	}


}
