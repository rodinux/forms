<?php

namespace OCA\Forms\Events;

use OCA\Forms\Db\Submission;
use OCP\EventDispatcher\Event;

class SubmissionEvent extends Event {
	public const CREATED = 1;
	public const DELETED = 2;

	private int $type;
	private Submission $submission;

	public function __construct(int $type, Submission $submission) {
		parent::__construct();
		$this->type = $type;
		$this->submission = $submission;
	}

	public function getType(): int {
		return $this->type;
	}

	public function getSubmission(): Submission {
		return $this->submission;
	}
}
