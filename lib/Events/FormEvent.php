<?php

namespace OCA\Forms\Events;

use OCA\Forms\Db\Form;
use OCP\EventDispatcher\Event;

abstract class FormEvent extends Event {
	private Form $form;

	public function __construct(Form $form) {
		parent::__construct();
		$this->form = $form;
	}

	public function getForm(): Form {
		return $this->form;
	}
}
