<p>Preencha o seu e-mail no campo abaixo para recuperar a senha:</p>
<?php
	echo $this->Form->create();
	echo $this->Form->input('email', array('type' => 'email', 'label' => 'E-mail'));
	echo $this->Form->end('Enviar');
?>