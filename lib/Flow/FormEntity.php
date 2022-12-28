<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2022 Ferdinand Thiessen <rpm@fthiessen.de>
 *
 * @author Ferdinand Thiessen <rpm@fthiessen.de>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Forms\Flow;

use OCA\Forms\Constants;
use OCA\Forms\Events\FormEvent;
use OCA\Forms\Events\SubmissionEvent;
use OCA\Forms\Service\FormsService;
use OCP\EventDispatcher\Event;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\WorkflowEngine\GenericEntityEvent;
use OCP\WorkflowEngine\IEntity;
use OCP\WorkflowEngine\IRuleMatcher;

class FormEntity implements IEntity {
	private const EVENT_NAMESPACE = '\OCA\Forms\Events::';

	private IL10N $l10n;
	private Event $event;
	private FormsService $service;
	private IURLGenerator $urlGenerator;
	
	public function __construct(IL10N $l10n, IURLGenerator $urlGenerator, FormsService $service) {
		$this->l10n = $l10n;
		$this->urlGenerator = $urlGenerator;
		$this->service = $service;
	}

	public function getName(): string {
		return $this->l10n->t('Form');
	}

	public function getIcon(): string {
		return $this->urlGenerator->imagePath('forms', 'forms-dark.svg');
	}

	public function getEvents(): array {
		return [
			new GenericEntityEvent($this->l10n->t('Form created'), self::EVENT_NAMESPACE . 'FormCreatedEvent'),
			new GenericEntityEvent($this->l10n->t('Form updated'), self::EVENT_NAMESPACE . 'FormUpdatedEvent'),
			new GenericEntityEvent($this->l10n->t('Form deleted'), self::EVENT_NAMESPACE . 'FormDeletedEvent'),
			//new GenericEntityEvent($this->l10n->t('Form expired'), self::EVENT_NAMESPACE . 'FormExpiredEvent'),
			//new GenericEntityEvent($this->l10n->t('Form answered'), self::EVENT_NAMESPACE . 'SubmissionEvent')
		];
	}

	public function prepareRuleMatcher(IRuleMatcher $ruleMatcher, string $eventName, Event $event): void {
		if ($event instanceof FormEvent) {
			$ruleMatcher->setEntitySubject($this, $event->getForm());
		} elseif ($event instanceof SubmissionEvent) {
			$ruleMatcher->setEntitySubject($this, $event->getSubmission());
		} else {
			return;
		}
		$this->event = $event;
	}

	public function isLegitimatedForUserId(string $userId): bool {
		if ($this->event instanceof FormEvent) {
			if ($this->event->getForm()->getOwnerId() === $userId) {
				return true;
			}
			if (in_array(Constants::PERMISSION_EDIT, $this->service->getPermissions($this->event->getForm()->getId(), $userId))) {
				return true;
			}
		}
		return false;
	}
}
