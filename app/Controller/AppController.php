<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */

class AppController extends Controller {

/*
 * Load helpers
 */

	public $helpers = array('Html', 'Form', 'Session', 'Js');
	
/*
 * Include the necessary components
 */
	public $components = array('Cookie', 'Auth', 'Session');

/*
 * Runs beforeFilter
 */
	public function beforeFilter() {
		// Allow main homepage for all users
		$this->Auth->allow('display');
		// Auth default error
		$this->Auth->authError = "Você não possui autorização para acessar este recurso.";
		// Gets the autologin cookie
		$cookie = $this->Cookie->read('Auth.User');       
		if ($cookie && $this->Auth->loggedIn() == false) {
			$this->loadModel('User');
			$user = $this->User->find('first', array('conditions' => array('email' => $cookie['email'], 'password' => AuthComponent::password($cookie['token']))));
			$this->Auth->login($user['User']);
		}
		// Login / logout redirections
		$this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
		$this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
		$this->Auth->loginRedirect = array('controller' => 'admin', 'action' => 'index');
		// Let users access resources based on the Controller actions
		$this->Auth->authorize = array('Controller');
		// Checks if the user is indeed who she claims to be (handle login/logout)
		$this->Auth->authenticate = array(
			'all' => array(
				// email as username
				'fields' => array('username' => 'email'),
				// only active users can access the application
				'scope' => array('User.is_active' => 1)
			),
			'Form'
		);
	}

/*
 * isAuthorized function
 * Only an admin can access admin-related resources within the app
 *
 * @return bool
 */
	public function isAuthorized($user) {
		if (($this->params['prefix'] === 'admin') && ($user['is_admin'] != 1)) {
			return false;            
		} 
		return true;
	}

}

