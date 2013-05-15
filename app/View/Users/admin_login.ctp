<?php
    echo $this->Form->create();
    echo $this->Form->input('email', array('type' => 'email', 'label' => 'E-mail'));
	echo $this->Form->input('password', array('type' => 'password', 'label' => 'Senha'));
    echo $this->Form->input('persist', array('type' => 'checkbox', 'label' => false, 'after' => 'Mantenha-me conectado'));
    echo $this->Form->end('Enviar');
    echo $this->Html->link('Esqueceu sua senha?', array('controller' => 'users', 'action' => 'admin_recover'))
?>