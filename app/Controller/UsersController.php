<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

/**
 * Runs automatically before the controller action is called
 *
 * @return void
 */

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array('admin_recover', 'admin_reset'));
	}

/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * admin_view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}

/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		}
	}

/**
 * admin_edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}

/**
 * admin_delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->onlyAllow('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

/**
 * Authorization methods
 *
 * Login / logout functions and allowed actions
 */

	public function admin_login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				if ($this->request->data['User']['persist'] == '1') {
					$cookie = array();
					$cookie['email'] = $this->data['User']['email'];
					$cookie['token'] = $this->data['User']['password'];
					$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');
				}
				$this->Session->setFlash('Você foi conectado com sucesso.');
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash('Usuário ou senha incorretos. Por favor, tente novamente.');
			}
		} else {
			$user = $this->Auth->user();
			if (empty($user)) {
				$cookie = $this->Cookie->read('Auth.User');
				// debug($cookie);
				if (!is_null($cookie)) {
					$user = $this->User->find('first', array('conditions' => array('email' => $cookie['email'], 'password' => AuthComponent::password($cookie['token']))));
					if ($this->Auth->login($user['User'])) {
						$this->Session->delete('Message.auth');
						$this->redirect($this->Auth->redirect());
					} else {
						$this->Cookie->delete('Auth.User');
					}
				}
			} else {
				$this->redirect($this->Auth->redirect());
			}
		}
	}

	public function admin_logout() {
		$this->Session->setFlash('Você foi desconectado com sucesso.');
		$this->Cookie->delete('Auth.User');
		$this->redirect($this->Auth->logout());
		$this->Session->destroy();
	}

/**
 * Recover password method
 *
 * Allows the user to email themselves a password redemption token
 *
 */
	public function admin_recover($email = null) {
		if ($this->request->is('post')) {
			$email = $this->request->data['User']['email'];
			$Token = ClassRegistry::init('Token');
			$user = $this->User->findByEmail($email);
			// If user not found, throws an alert and redirect for add action
			if (empty($user)) {
				$this->Session->setFlash('E-mail não encontrado. Por favor, faça o cadastro.');
				// $this->redirect(array('action' => 'add'));
			} else {
				// Generate a new token to user
				$token = $Token->generate(array('User' => $user['User']));
				$this->set('user', $user);
				$this->set('token', $token);
				// Sends a confirmation email to user
				$email = new CakeEmail('default');
				$email->from(array('alexcolucci@gmail.com' => 'CakeEmail'))
					->template('recover')
					->emailFormat('html')
					->to($user['User']['email'])
					->subject('Recuperação de senha')
					->viewVars(compact('user', 'token'))
					->send();
				// Set the successful message to user
				$this->Session->setFlash('Um e-mail foi enviado para a sua conta, por favor, siga as instruções deste e-mail.');
			}
		}		
	}

/**
 * Reset password method
 *
 * Accepts a valid token and resets the users password
 *
 */

	public function admin_reset($token = null) {
		if ($this->request->is('post')) {
			$token = $this->request->data['User']['token'];
		}
		// Inits Token model
		$Token = ClassRegistry::init('Token');
		// Recover token information
		$result = $Token->get($token);
		if ($result) {
			// Finds the user
			$user = $this->User->findByEmail($result['User']['email']);
			$this->Auth->login($user['User']);
			$this->redirect(array('action' => 'admin_change'));
		} else {
			$this->Session->setFlash('Chave de ativação inválida. A chave expirou, ou o link não foi copiado de seu cliente de e-mail corretamente.');
		}
	}

/**
 * change password method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_change() {
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				// Gets useful user information
				$name = $this->Auth->user('name');
				// Sends a confirmation e-mail to user
				$email = new CakeEmail('default');
				$email->from(array('alexcolucci@gmail.com' => 'CakeEmail'))
					->template('change')
					->emailFormat('html')
					->to($this->Auth->user('email'))
					->subject('Sua senha foi alterada')
					->viewVars(compact('name'))
					->send();
				// Success alert and redirect to user profile
				$this->Session->setFlash('Sua senha foi atualizada.');
				$this->redirect(array('controller' => 'admin', 'action' => 'index'));
			} else {
				$this->Session->setFlash('Sua senha não pôde ser salva. Por favor, tente novamente.');
			}
		}
		$user = $this->User->findById($this->Auth->user('id'));
		$this->set('user', $user);
	}


}
