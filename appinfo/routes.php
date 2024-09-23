<?php
/**
 * @copyright Copyright (c] 2017 Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 *
 * @author affan98 <affan98@gmail.com>
 * @author Christian Hartmann <chris-hartmann@gmx.de>
 * @author Ferdinand Thiessen <opensource@fthiessen.de>
 * @author John Molakvoæ (skjnldsv) <skjnldsv@protonmail.com>
 * @author Jonas Rittershofer <jotoeri@users.noreply.github.com>
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

$apiBase = '/api/{apiVersion}/';
$requirements_v3 = [
	'apiVersion' => 'v3',
	'formId' => '\d+',
	'questionId' => '\d+',
	'optionId' => '\d+',
	'shareId' => '\d+',
	'submissionId' => '\d+'
];

return [
	'routes' => [
		// Internal AppConfig routes
		['name' => 'config#getAppConfig', 'url' => '/config', 'verb' => 'GET'],
		['name' => 'config#updateAppConfig', 'url' => '/config/update', 'verb' => 'PATCH'],

		// Public Share Link
		['name' => 'page#public_link_view', 'url' => '/s/{hash}', 'verb' => 'GET'],

		// Embedded View
		['name' => 'page#embedded_form_view', 'url' => '/embed/{hash}', 'verb' => 'GET'],

		// Internal views
		['name' => 'page#views', 'url' => '/{hash}/{view}', 'verb' => 'GET'],
		// Internal Form Link
		['name' => 'page#internal_link_view', 'url' => '/{hash}', 'verb' => 'GET'],
		// App Root
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	],

	'ocs' => [
		// CORS Preflight
		['name' => 'api#preflightedCors', 'url' => $apiBase . '{path}', 'verb' => 'OPTIONS', 'requirements' => [
			'path' => '.+',
			'apiVersion' => 'v2(\.[1-5])?|v3'
		]],

		// API routes v3
		// Forms
		['name' => 'api#getForms', 'url' => $apiBase . 'forms', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#newForm', 'url' => $apiBase . 'forms', 'verb' => 'POST', 'requirements' => $requirements_v3],
		['name' => 'api#getForm', 'url' => $apiBase . 'forms/{formId}', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#updateForm', 'url' => $apiBase . 'forms/{formId}', 'verb' => 'PATCH', 'requirements' => $requirements_v3],
		['name' => 'api#deleteForm', 'url' => $apiBase . 'forms/{formId}', 'verb' => 'DELETE', 'requirements' => $requirements_v3],

		// Questions
		['name' => 'api#getQuestions', 'url' => $apiBase . 'forms/{formId}/questions', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#newQuestion', 'url' => $apiBase . 'forms/{formId}/questions', 'verb' => 'POST', 'requirements' => $requirements_v3],
		['name' => 'api#getQuestion', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#updateQuestion', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}', 'verb' => 'PATCH', 'requirements' => $requirements_v3],
		['name' => 'api#deleteQuestion', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}', 'verb' => 'DELETE', 'requirements' => $requirements_v3],
		['name' => 'api#reorderQuestions', 'url' => $apiBase . 'forms/{formId}/questions', 'verb' => 'PATCH', 'requirements' => $requirements_v3],

		// Options
		// ['name' => 'api#getOptions', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#newOption', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options', 'verb' => 'POST', 'requirements' => $requirements_v3],
		// ['name' => 'api#getOption', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options/{optionId}', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#updateOption', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options/{optionId}', 'verb' => 'PATCH', 'requirements' => $requirements_v3],
		['name' => 'api#deleteOption', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options/{optionId}', 'verb' => 'DELETE', 'requirements' => $requirements_v3],
		// ['name' => 'api#reorderOptions', 'url' => $apiBase . 'forms/{formId}/questions/{questionId}/options', 'verb' => 'PATCH', 'requirements' => $requirements_v3],

		// Shares
		// ['name' => 'shareApi#getUserShares', 'url' => $apiBase . 'shares', 'verb' => 'GET', 'requirements' => $requirements_v3],
		// ['name' => 'shareApi#getShares', 'url' => $apiBase . 'forms/{formId}/shares', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'shareApi#newShare', 'url' => $apiBase . 'forms/{formId}/shares', 'verb' => 'POST', 'requirements' => $requirements_v3],
		// ['name' => 'shareApi#getShare', 'url' => $apiBase . 'forms/{formId}/shares/{shareId}', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'shareApi#updateShare', 'url' => $apiBase . 'forms/{formId}/shares/{shareId}', 'verb' => 'PATCH', 'requirements' => $requirements_v3],
		['name' => 'shareApi#deleteShare', 'url' => $apiBase . 'forms/{formId}/shares/{shareId}', 'verb' => 'DELETE', 'requirements' => $requirements_v3],

		// Submissions
		['name' => 'api#getSubmissions', 'url' => $apiBase . 'forms/{formId}/submissions', 'verb' => 'GET', 'requirements' => $requirements_v3],
		['name' => 'api#newSubmission', 'url' => $apiBase . 'forms/{formId}/submissions', 'verb' => 'POST', 'requirements' => $requirements_v3],
		['name' => 'api#deleteAllSubmissions', 'url' => $apiBase . 'forms/{formId}/submissions', 'verb' => 'DELETE', 'requirements' => $requirements_v3],
		//['name' => 'api#getSubmission', 'url' => $apiBase . 'forms/{formId}/submissions/{submissionId}', 'verb' => 'GET', 'requirements' => $requirements_v3],
		//['name' => 'api#updateSubmission', 'url' => $apiBase . 'forms/{formId}/submissions/{submissionId}', 'verb' => 'PATCH', 'requirements' => $requirements_v3],
		['name' => 'api#deleteSubmission', 'url' => $apiBase . 'forms/{formId}/submissions/{submissionId}', 'verb' => 'DELETE', 'requirements' => $requirements_v3],
		['name' => 'api#exportSubmissionsToCloud', 'url' => $apiBase . 'forms/{formId}/submissions/export', 'verb' => 'POST', 'requirements' => $requirements_v3],
		['name' => 'api#uploadFiles', 'url' => $apiBase . 'forms/{formId}/submissions/files/{questionId}', 'verb' => 'POST', 'requirements' => $requirements_v3],

		// Legacy v2 routes (TODO: remove with Forms v5)
		// Forms
		[
			'name' => 'api#getForms',
			'url' => '/api/{apiVersion}/forms',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#newForm',
			'url' => '/api/{apiVersion}/form',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#getForm',
			'url' => '/api/{apiVersion}/form/{id}',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#cloneForm',
			'url' => '/api/{apiVersion}/form/clone/{id}',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		// TODO: Remove POST in next API release
		[
			'name' => 'api#updateForm',
			'url' => '/api/{apiVersion}/form/update',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#updateForm',
			'url' => '/api/{apiVersion}/form/update',
			'verb' => 'PATCH',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],
		[
			'name' => 'api#transferOwner',
			'url' => '/api/{apiVersion}/form/transfer',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],
		[
			'name' => 'api#deleteForm',
			'url' => '/api/{apiVersion}/form/{id}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#getPartialForm',
			'url' => '/api/{apiVersion}/partial_form/{hash}',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#getSharedForms',
			'url' => '/api/{apiVersion}/shared_forms',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],

		// Questions
		[
			'name' => 'api#newQuestion',
			'url' => '/api/{apiVersion}/question',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		// TODO: Remove POST in next API release
		[
			'name' => 'api#updateQuestion',
			'url' => '/api/{apiVersion}/question/update',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#updateQuestion',
			'url' => '/api/{apiVersion}/question/update',
			'verb' => 'PATCH',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],
		// TODO: Remove POST in next API release
		[
			'name' => 'api#reorderQuestions',
			'url' => '/api/{apiVersion}/question/reorder',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#reorderQuestions',
			'url' => '/api/{apiVersion}/question/reorder',
			'verb' => 'PUT',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],
		[
			'name' => 'api#deleteQuestion',
			'url' => '/api/{apiVersion}/question/{id}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#cloneQuestion',
			'url' => '/api/{apiVersion}/question/clone/{id}',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2\.[3-5]'
			]
		],

		// Options
		[
			'name' => 'api#newOption',
			'url' => '/api/{apiVersion}/option',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		// TODO: Remove POST in next API release
		[
			'name' => 'api#updateOption',
			'url' => '/api/{apiVersion}/option/update',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#updateOption',
			'url' => '/api/{apiVersion}/option/update',
			'verb' => 'PATCH',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],
		[
			'name' => 'api#deleteOption',
			'url' => '/api/{apiVersion}/option/{id}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],

		// Shares
		[
			'name' => 'shareApi#newShare',
			'url' => '/api/{apiVersion}/share',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'shareApi#deleteShare',
			'url' => '/api/{apiVersion}/share/{id}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		// TODO: Remove POST in next API release
		[
			'name' => 'shareApi#updateShare',
			'url' => '/api/{apiVersion}/share/update',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2\.[1-5]'
			]
		],
		[
			'name' => 'shareApi#updateShare',
			'url' => '/api/{apiVersion}/share/update',
			'verb' => 'PATCH',
			'requirements' => [
				'apiVersion' => 'v2\.[2-5]'
			]
		],

		// Submissions
		[
			'name' => 'api#getSubmissions',
			'url' => '/api/{apiVersion}/submissions/{hash}',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#exportSubmissions',
			'url' => '/api/{apiVersion}/submissions/export/{hash}',
			'verb' => 'GET',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#exportSubmissionsToCloud',
			'url' => '/api/{apiVersion}/submissions/export',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#deleteAllSubmissions',
			'url' => '/api/{apiVersion}/submissions/{formId}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#insertSubmission',
			'url' => '/api/{apiVersion}/submission/insert',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		[
			'name' => 'api#deleteSubmission',
			'url' => '/api/{apiVersion}/submission/{id}',
			'verb' => 'DELETE',
			'requirements' => [
				'apiVersion' => 'v2(\.[1-5])?'
			]
		],
		// Submissions linking with file in cloud
		[
			'name' => 'api#linkFile',
			'url' => '/api/{apiVersion}/form/link/{fileFormat}',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2.[4-5]',
				'fileFormat' => 'csv|ods|xlsx'
			]
		],
		[
			'name' => 'api#unlinkFile',
			'url' => '/api/{apiVersion}/form/unlink',
			'verb' => 'POST',
			'requirements' => [
				'apiVersion' => 'v2.[4-5]',
			]
		]
	]
];
