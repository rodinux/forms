<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2022 Jonas Rittershofer <jotoeri@users.noreply.github.com>
 *
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
namespace OCA\Forms\Tests\Integration\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use OCA\Forms\Constants;
use OCA\Forms\Tests\Integration\IntegrationBase;

/**
 * @group DB
 */
class ApiV2Test extends IntegrationBase {
	/** @var GuzzleHttp\Client */
	private $http;

	protected array $users = [
		'test' => 'Test user',
	];

	/**
	 * Store Test Forms Array.
	 * Necessary as function due to object type-casting.
	 */
	private function setTestForms() {
		$this->testForms = [
			[
				'hash' => '0123456789abcdef',
				'title' => 'Title of a Form',
				'description' => 'Just a simple form.',
				'owner_id' => 'test',
				'access_enum' => 0,
				'created' => 12345,
				'expires' => 0,
				'state' => 0,
				'is_anonymous' => false,
				'submit_multiple' => false,
				'show_expiration' => false,
				'last_updated' => 123456789,
				'submission_message' => 'Back to website',
				'file_id' => null,
				'file_format' => null,
				'questions' => [
					[
						'type' => 'short',
						'text' => 'First Question?',
						'description' => 'Please answer this.',
						'isRequired' => true,
						'name' => '',
						'order' => 1,
						'options' => [],
						'accept' => [],
						'extraSettings' => []
					],
					[
						'type' => 'multiple_unique',
						'text' => 'Second Question?',
						'description' => '',
						'isRequired' => false,
						'name' => 'city',
						'order' => 2,
						'options' => [
							[
								'text' => 'Option 1'
							],
							[
								'text' => 'Option 2'
							],
							[
								'text' => ''
							]
						],
						'accept' => [],
						'extraSettings' => [
							'shuffleOptions' => true
						]
					],
					[
						'type' => 'file',
						'text' => 'File Question?',
						'description' => '',
						'isRequired' => false,
						'name' => 'file',
						'order' => 3,
						'options' => [],
						'accept' => ['.txt'],
						'extraSettings' => [
							'allowedFileExtensions' => ['txt'],
							'maxAllowedFilesCount' => 1,
							'maxFileSize' => 1024,
						],
					],
				],
				'shares' => [
					[
						'shareType' => 0,
						'shareWith' => 'user1',
						'permissions' => ['submit', 'results'],
					],
					[
						'shareType' => 3,
						'shareWith' => 'shareHash',
						'permissions' => ['submit'],
					],
				],
				'submissions' => [
					[
						'userId' => 'user1',
						'timestamp' => 123456,
						'answers' => [
							[
								'questionIndex' => 0,
								'text' => 'This is a short answer.'
							],
							[
								'questionIndex' => 1,
								'text' => 'Option 1'
							]
						]
					],
					[
						'userId' => 'user2',
						'timestamp' => 12345,
						'answers' => [
							[
								'questionIndex' => 0,
								'text' => 'This is another short answer.'
							],
							[
								'questionIndex' => 1,
								'text' => 'Option 2'
							]
						]
					],
					[
						'userId' => 'user3',
						'timestamp' => 1234,
						'answers' => [
							[
								'questionIndex' => 0,
								'text' => ''
							]
						]
					]
				]
			],
			[
				'hash' => 'abcdefghij123456',
				'title' => 'Title of a second Form',
				'description' => '',
				'owner_id' => 'someUser',
				'access_enum' => 2,
				'created' => 12345,
				'expires' => 0,
				'state' => 0,
				'is_anonymous' => false,
				'submit_multiple' => false,
				'show_expiration' => false,
				'last_updated' => 123456789,
				'submission_message' => '',
				'file_id' => null,
				'file_format' => null,
				'questions' => [
					[
						'type' => 'short',
						'text' => 'Third Question?',
						'description' => '',
						'isRequired' => false,
						'name' => '',
						'order' => 1,
						'options' => [],
						'accept' => [],
						'extraSettings' => []
					],
				],
				'shares' => [
					[
						'shareType' => 0,
						'shareWith' => 'user2',
					],
				],
				'submissions' => []
			],
			[
				'hash' => 'zyxwvutsrq654321',
				'title' => 'Title of a third Form',
				'description' => '',
				'owner_id' => 'test',
				'access_enum' => 2,
				'created' => 12345,
				'expires' => 0,
				'state' => 0,
				'is_anonymous' => false,
				'submit_multiple' => false,
				'show_expiration' => false,
				'last_updated' => 123456789,
				'submission_message' => '',
				'file_id' => 12,
				'file_format' => 'csv',
				'questions' => [
					[
						'type' => 'short',
						'text' => 'Third Question?',
						'description' => '',
						'isRequired' => false,
						'name' => '',
						'order' => 1,
						'options' => [],
						'accept' => [],
						'extraSettings' => []
					],
				],
				'shares' => [
					[
						'shareType' => 0,
						'shareWith' => 'user2',
					],
				],
				'submissions' => []
			],
		];
	}

	/**
	 * Set up test environment.
	 * Writing testforms into db, preparing http request
	 */
	public function setUp(): void {
		$this->setTestForms();
		$this->users = [
			'test' => 'Test Displayname',
			'user1' => 'User No. 1',
		];

		parent::setUp();

		// Set up http Client
		$this->http = new Client([
			'base_uri' => 'http://localhost:8080/ocs/v2.php/apps/forms/',
			'auth' => ['test', 'test'],
			'headers' => [
				'OCS-ApiRequest' => 'true',
				'Accept' => 'application/json'
			],
		]);
	}

	public function tearDown(): void {
		parent::tearDown();
	}

	// Small Wrapper for OCS-Response
	private function OcsResponse2Data($resp) {
		$arr = json_decode($resp->getBody()->getContents(), true);
		return $arr['ocs']['data'];
	}

	// Unset Id, as we can not control it on the tests.
	private function arrayUnsetId(array $arr): array {
		foreach ($arr as $index => $elem) {
			unset($arr[$index]['id']);
		}
		return $arr;
	}

	public function dataGetForms() {
		return [
			'getTestforms' => [
				'expected' => [
					[
						'hash' => '0123456789abcdef',
						'title' => 'Title of a Form',
						'expires' => 0,
						'state' => 0,
						'lastUpdated' => 123456789,
						'permissions' => Constants::PERMISSION_ALL,
						'partial' => true,
						'submissionCount' => 3,
					],
					[
						'hash' => 'zyxwvutsrq654321',
						'title' => 'Title of a third Form',
						'expires' => 0,
						'state' => 0,
						'lastUpdated' => 123456789,
						'permissions' => Constants::PERMISSION_ALL,
						'partial' => true,
						'submissionCount' => 0,
					]
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetForms
	 *
	 * @param array $expected
	 */
	public function testGetForms(array $expected): void {
		$resp = $this->http->request('GET', 'api/v2.4/forms');

		$data = $this->OcsResponse2Data($resp);
		$data = $this->arrayUnsetId($data);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataGetSharedForms() {
		return [
			'getTestforms' => [
				'expected' => [
					[
						'hash' => 'abcdefghij123456',
						'title' => 'Title of a second Form',
						'expires' => 0,
						'state' => 0,
						'lastUpdated' => 123456789,
						'permissions' => [
							'submit'
						],
						'partial' => true
					],
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetSharedForms
	 *
	 * @param array $expected
	 */
	public function testGetSharedForms(array $expected): void {
		$resp = $this->http->request('GET', 'api/v2.4/shared_forms');

		$data = $this->OcsResponse2Data($resp);
		$data = $this->arrayUnsetId($data);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataGetPartialForm() {
		return [
			'getPartialForm' => [
				'expected' => [
					'hash' => 'abcdefghij123456',
					'title' => 'Title of a second Form',
					'expires' => 0,
					'state' => 0,
					'lastUpdated' => 123456789,
					'permissions' => [
						'submit'
					],
					'partial' => true
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetPartialForm
	 *
	 * @param array $expected
	 */
	public function testGetPartialForm(array $expected): void {
		$resp = $this->http->request('GET', "api/v2.1/partial_form/{$this->testForms[1]['hash']}");

		$data = $this->OcsResponse2Data($resp);
		unset($data['id']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataGetNewForm() {
		return [
			'getNewForm' => [
				'expected' => [
					// 'hash' => Some random, cannot be checked.
					'title' => '',
					'description' => '',
					'ownerId' => 'test',
					// 'created' => time() can not be checked exactly
					'access' => [
						'permitAllUsers' => false,
						'showToAllUsers' => false
					],
					'expires' => 0,
					'state' => 0,
					'isAnonymous' => false,
					'submitMultiple' => false,
					'showExpiration' => false,
					// 'lastUpdated' => time() can not be checked exactly
					'canSubmit' => true,
					'permissions' => Constants::PERMISSION_ALL,
					'questions' => [],
					'shares' => [],
					'submissionCount' => 0,
					'submissionMessage' => null,
					'fileId' => null,
					'fileFormat' => null,
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetNewForm
	 *
	 * @param array $expected
	 */
	public function testGetNewForm(array $expected): void {
		$resp = $this->http->request('POST', 'api/v2.4/form');
		$data = $this->OcsResponse2Data($resp);

		// Store for deletion on tearDown
		$this->testForms[] = $data;

		// Cannot control id
		unset($data['id']);
		// Check general behaviour of hash
		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{16}$/', $data['hash']);
		unset($data['hash']);
		// Check general behaviour of created (Created in the last 10 seconds)
		$this->assertEqualsWithDelta(time(), $data['created'], 10);
		unset($data['created']);
		// Check general behaviour of lastUpdated (Last update in the last 10 seconds)
		$this->assertEqualsWithDelta(time(), $data['lastUpdated'], 10);
		unset($data['lastUpdated']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataGetFullForm() {
		return [
			'getFullForm' => [
				'expected' => [
					'hash' => '0123456789abcdef',
					'title' => 'Title of a Form',
					'description' => 'Just a simple form.',
					'ownerId' => 'test',
					'created' => 12345,
					'access' => [
						'permitAllUsers' => false,
						'showToAllUsers' => false
					],
					'expires' => 0,
					'state' => 0,
					'isAnonymous' => false,
					'submitMultiple' => false,
					'showExpiration' => false,
					'lastUpdated' => 123456789,
					'canSubmit' => true,
					'permissions' => Constants::PERMISSION_ALL,
					'submissionMessage' => 'Back to website',
					'questions' => [
						[
							'type' => 'short',
							'text' => 'First Question?',
							'isRequired' => true,
							'name' => '',
							'order' => 1,
							'options' => [],
							'accept' => [],
							'description' => 'Please answer this.',
							'extraSettings' => []
						],
						[
							'type' => 'multiple_unique',
							'text' => 'Second Question?',
							'isRequired' => false,
							'name' => 'city',
							'order' => 2,
							'options' => [
								[
									'text' => 'Option 1',
									'order' => null,
								],
								[
									'text' => 'Option 2',
									'order' => null,
								],
								[
									'text' => '',
									'order' => null,
								]
							],
							'accept' => [],
							'description' => '',
							'extraSettings' => [
								'shuffleOptions' => true,
							]
						],
						[
							'type' => 'file',
							'text' => 'File Question?',
							'isRequired' => false,
							'name' => 'file',
							'order' => 3,
							'options' => [],
							'accept' => ['.txt'],
							'description' => '',
							'extraSettings' => [
								'allowedFileExtensions' => ['txt'],
								'maxAllowedFilesCount' => 1,
								'maxFileSize' => 1024,
							],
						],
					],
					'shares' => [
						[
							'shareType' => 0,
							'shareWith' => 'user1',
							'permissions' => ['submit', 'results'],
							'displayName' => 'User No. 1'
						],
						[
							'shareType' => 3,
							'shareWith' => 'shareHash',
							'permissions' => ['submit'],
							'displayName' => ''
						],
					],
					'submissionCount' => 3,
					'fileId' => null,
					'fileFormat' => null,
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetFullForm
	 *
	 * @param array $expected
	 */
	public function testGetFullForm(array $expected): void {
		$resp = $this->http->request('GET', "api/v2.1/form/{$this->testForms[0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		// Cannot control ids, but check general consistency.
		foreach ($data['questions'] as $qIndex => $question) {
			$this->assertEquals($data['id'], $question['formId']);
			unset($data['questions'][$qIndex]['formId']);

			foreach ($question['options'] as $oIndex => $option) {
				$this->assertEquals($question['id'], $option['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['id']);
			}
			unset($data['questions'][$qIndex]['id']);
		}
		foreach ($data['shares'] as $sIndex => $share) {
			$this->assertEquals($data['id'], $share['formId']);
			unset($data['shares'][$sIndex]['formId']);
			unset($data['shares'][$sIndex]['id']);
		}
		unset($data['id']);

		// Allow a 10 second diff for lastUpdated between expectation and data
		$this->assertEqualsWithDelta($expected['lastUpdated'], $data['lastUpdated'], 10);
		unset($data['lastUpdated']);
		unset($expected['lastUpdated']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataCloneForm() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		// Compared to full form expected, update changed properties
		$fullFormExpected['title'] = 'Title of a Form - Copy';
		$fullFormExpected['shares'] = [];
		$fullFormExpected['submissionCount'] = 0;
		// Compared to full form expected, unset unpredictable properties. These will be checked logically.
		unset($fullFormExpected['id']);
		unset($fullFormExpected['hash']);
		unset($fullFormExpected['created']);
		unset($fullFormExpected['lastUpdated']);
		foreach ($fullFormExpected['questions'] as $qIndex => $question) {
			unset($fullFormExpected['questions'][$qIndex]['formId']);
		}

		return [
			'updateFormProps' => [
				'expected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataCloneForm
	 *
	 * @param array $expected
	 */
	public function testCloneForm(array $expected): void {
		$resp = $this->http->request('POST', "api/v2.1/form/clone/{$this->testForms[0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		// Store for deletion on tearDown
		$this->testForms[] = $data;

		// Cannot control ids, but check general consistency.
		foreach ($data['questions'] as $qIndex => $question) {
			$this->assertEquals($data['id'], $question['formId']);
			unset($data['questions'][$qIndex]['formId']);

			foreach ($question['options'] as $oIndex => $option) {
				$this->assertEquals($question['id'], $option['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['id']);
			}
			unset($data['questions'][$qIndex]['id']);
		}
		foreach ($data['shares'] as $sIndex => $share) {
			$this->assertEquals($data['id'], $share['formId']);
			unset($data['shares'][$sIndex]['formId']);
			unset($data['shares'][$sIndex]['id']);
		}
		// Check not just returning source-form (id must differ).
		$this->assertGreaterThan($this->testForms[0]['id'], $data['id']);
		unset($data['id']);

		// Check general behaviour of hash
		$this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{16}$/', $data['hash']);
		unset($data['hash']);
		// Check general behaviour of created (Created in the last 10 seconds)
		$this->assertTrue(time() - $data['created'] < 10);
		unset($data['created']);
		// Check general behaviour of lastUpdated (Last update in the last 10 seconds)
		$this->assertTrue(time() - $data['lastUpdated'] < 10);
		unset($data['lastUpdated']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataUpdateFormProperties() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		$fullFormExpected['title'] = 'This is my NEW Title!';
		$fullFormExpected['access'] = [
			'permitAllUsers' => true,
			'showToAllUsers' => true
		];
		return [
			'updateFormProps' => [
				'expected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataUpdateFormProperties
	 *
	 * @param array $expected
	 */
	public function testUpdateFormProperties(array $expected): void {
		$resp = $this->http->request('PATCH', 'api/v2.4/form/update', [
			'json' => [
				'id' => $this->testForms[0]['id'],
				'keyValuePairs' => [
					'title' => 'This is my NEW Title!',
					'access' => [
						'permitAllUsers' => true,
						'showToAllUsers' => true
					]
				]
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['id'], $data);

		$expected['lastUpdated'] = time();

		// Check if form equals updated form.
		$this->testGetFullForm($expected);
	}

	public function testDeleteForm() {
		$resp = $this->http->request('DELETE', "api/v2.1/form/{$this->testForms[0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['id'], $data);

		// Check if not existent anymore.
		try {
			$this->http->request('GET', "api/v2.1/form/{$this->testForms[0]['id']}");
		} catch (ClientException $e) {
			$resp = $e->getResponse();
		}
		$this->assertEquals(400, $resp->getStatusCode());
	}

	public function dataCreateNewQuestion() {
		return [
			'newQuestion' => [
				'expected' => [
					// 'formId' => 3, // Checked during test
					// 'order' => 3, // Checked during test
					'type' => 'short',
					'isRequired' => false,
					'text' => 'Already some Question?',
					'name' => '',
					'options' => [],
					'accept' => [],
					'description' => '',
					'extraSettings' => [],
				]
			],
			'emptyQuestion' => [
				'expected' => [
					// 'formId' => 3, // Checked during test
					// 'order' => 3, // Checked during test
					'type' => 'short',
					'isRequired' => false,
					'text' => '',
					'name' => '',
					'options' => [],
					'accept' => [],
					'description' => '',
					'extraSettings' => [],
				]
			]
		];
	}
	/**
	 * @dataProvider dataCreateNewQuestion
	 *
	 * @param array $expected
	 */
	public function testCreateNewQuestion(array $expected): void {
		$resp = $this->http->request('POST', 'api/v2.4/question', [
			'json' => [
				'formId' => $this->testForms[0]['id'],
				'type' => 'short',
				'text' => $expected['text']
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		// Store for deletion on tearDown
		$this->testForms[0]['questions'][] = $data;

		// Check formId & order
		$this->assertEquals($this->testForms[0]['id'], $data['formId']);
		unset($data['formId']);
		$this->assertEquals(sizeof($this->testForms[0]['questions']), $data['order']);
		unset($data['order']);
		unset($data['id']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataUpdateQuestionProperties() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		$fullFormExpected['questions'][0]['text'] = 'Still first Question!';
		$fullFormExpected['questions'][0]['isRequired'] = false;

		return [
			'updateQuestionProps' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataUpdateQuestionProperties
	 *
	 * @param array $fullFormExpected
	 */
	public function testUpdateQuestionProperties(array $fullFormExpected): void {
		$resp = $this->http->request('PATCH', 'api/v2.4/question/update', [
			'json' => [
				'id' => $this->testForms[0]['questions'][0]['id'],
				'keyValuePairs' => [
					'isRequired' => false,
					'text' => 'Still first Question!'
				]
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['questions'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		// Check if form equals updated form.
		$this->testGetFullForm($fullFormExpected);
	}

	public function dataReorderQuestions() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		$fullFormExpected['questions'][0]['order'] = 2;
		$fullFormExpected['questions'][1]['order'] = 1;

		// Exchange questions, as they will be returned in new order.
		$tmp = $fullFormExpected['questions'][0];
		$fullFormExpected['questions'][0] = $fullFormExpected['questions'][1];
		$fullFormExpected['questions'][1] = $tmp;

		return [
			'updateQuestionProps' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataReorderQuestions
	 *
	 * @param array $fullFormExpected
	 */
	public function testReorderQuestions(array $fullFormExpected): void {
		$resp = $this->http->request('PUT', 'api/v2.4/question/reorder', [
			'json' => [
				'formId' => $this->testForms[0]['id'],
				'newOrder' => [
					$this->testForms[0]['questions'][1]['id'],
					$this->testForms[0]['questions'][0]['id'],
					$this->testForms[0]['questions'][2]['id'],
				]
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals([
			$this->testForms[0]['questions'][0]['id'] => [ 'order' => 2 ],
			$this->testForms[0]['questions'][1]['id'] => [ 'order' => 1 ],
			$this->testForms[0]['questions'][2]['id'] => [ 'order' => 3 ],
		], $data);

		$fullFormExpected['lastUpdated'] = time();

		// Check if form equals updated form.
		$this->testGetFullForm($fullFormExpected);
	}

	public function dataDeleteQuestion() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		array_splice($fullFormExpected['questions'], 0, 1);
		$fullFormExpected['questions'][0]['order'] = 1;
		$fullFormExpected['questions'][1]['order'] = 2;

		return [
			'deleteQuestion' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataDeleteQuestion
	 *
	 * @param array $fullFormExpected
	 */
	public function testDeleteQuestion(array $fullFormExpected) {
		$resp = $this->http->request('DELETE', "api/v2.1/question/{$this->testForms[0]['questions'][0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['questions'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		$this->testGetFullForm($fullFormExpected);
	}

	public function testCloneQuestion() {
		$resp = $this->http->request('POST', 'api/v2.4/question/clone/' . $this->testForms[0]['questions'][0]['id']);
		$data = $this->OcsResponse2Data($resp);
		$this->testForms[0]['questions'][] = $data;

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertNotEquals($data['id'], $this->testForms[0]['questions'][0]['id']);

		$copy = $this->testForms[0]['questions'][0];
		unset($copy['id']);
		unset($copy['order']);
		foreach ($copy as $key => $value) {
			$this->assertEquals($value, $data[$key]);
		}
	}

	public function dataCreateNewOption() {
		return [
			'newOption' => [
				'expected' => [
					// 'questionId' => Done dynamically below.
					'text' => 'A new Option.',
					'order' => null,
				]
			]
		];
	}
	/**
	 * @dataProvider dataCreateNewOption
	 *
	 * @param array $expected
	 */
	public function testCreateNewOption(array $expected): void {
		$resp = $this->http->request('POST', 'api/v2.4/option', [
			'json' => [
				'questionId' => $this->testForms[0]['questions'][1]['id'],
				'text' => 'A new Option.'
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		// Store for deletion on tearDown
		$this->testForms[0]['questions'][1]['options'][] = $data;

		// Check questionId
		$this->assertEquals($this->testForms[0]['questions'][1]['id'], $data['questionId']);
		unset($data['questionId']);
		unset($data['id']);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataUpdateOptionProperties() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		$fullFormExpected['questions'][1]['options'][0]['text'] = 'New option Text.';

		return [
			'updateOptionProps' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataUpdateOptionProperties
	 *
	 * @param array $fullFormExpected
	 */
	public function testUpdateOptionProperties(array $fullFormExpected): void {
		$resp = $this->http->request('PATCH', 'api/v2.4/option/update', [
			'json' => [
				'id' => $this->testForms[0]['questions'][1]['options'][0]['id'],
				'keyValuePairs' => [
					'text' => 'New option Text.'
				]
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['questions'][1]['options'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		// Check if form equals updated form.
		$this->testGetFullForm($fullFormExpected);
	}

	public function dataDeleteOption() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		array_splice($fullFormExpected['questions'][1]['options'], 0, 1);

		// Now the other options are reordered
		$fullFormExpected['questions'][1]['options'][0]['order'] = 0;
		$fullFormExpected['questions'][1]['options'][1]['order'] = 1;

		return [
			'deleteOption' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataDeleteOption
	 *
	 * @param array $fullFormExpected
	 */
	public function testDeleteOption(array $fullFormExpected) {
		$resp = $this->http->request('DELETE', "api/v2.1/option/{$this->testForms[0]['questions'][1]['options'][0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['questions'][1]['options'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		$this->testGetFullForm($fullFormExpected);
	}

	public function dataAddShare() {
		return [
			'addAShare' => [
				'expected' => [
					// 'formId' => Checked dynamically
					'shareType' => 0,
					'shareWith' => 'test',
					'permissions' => ['submit'],
					'displayName' => 'Test Displayname'
				]
			]
		];
	}
	/**
	 * @dataProvider dataAddShare
	 *
	 * @param array $expected
	 */
	public function testAddShare(array $expected) {
		$resp = $this->http->request('POST', 'api/v2.4/share', [
			'json' => [
				'formId' => $this->testForms[0]['id'],
				'shareType' => 0,
				'shareWith' => 'test',
				'permissions' => ['submit']
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		// Store for cleanup
		$this->testForms[0]['shares'][] = $data;

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['id'], $data['formId']);
		unset($data['formId']);
		unset($data['id']);
		$this->assertEquals($expected, $data);
	}

	public function dataUpdateShare() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		$fullFormExpected['shares'][0]['permissions'] = [ Constants::PERMISSION_SUBMIT ];

		return [
			'deleteShare' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataUpdateShare
	 *
	 * @param array $fullFormExpected
	 */
	public function testUpdateShare(array $fullFormExpected) {
		$resp = $this->http->request('PATCH', 'api/v2.4/share/update', [
			'json' => [
				'id' => $this->testForms[0]['shares'][0]['id'],
				'keyValuePairs' => [
					'permissions' => [ Constants::PERMISSION_SUBMIT ],
				],
			],
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['shares'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		$this->testGetFullForm($fullFormExpected);
	}

	public function dataDeleteShare() {
		$fullFormExpected = $this->dataGetFullForm()['getFullForm']['expected'];
		array_splice($fullFormExpected['shares'], 0, 1);

		return [
			'deleteShare' => [
				'fullFormExpected' => $fullFormExpected
			]
		];
	}
	/**
	 * @dataProvider dataDeleteShare
	 *
	 * @param array $fullFormExpected
	 */
	public function testDeleteShare(array $fullFormExpected) {
		$resp = $this->http->request('DELETE', "api/v2.1/share/{$this->testForms[0]['shares'][0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['shares'][0]['id'], $data);

		$fullFormExpected['lastUpdated'] = time();

		$this->testGetFullForm($fullFormExpected);
	}

	public function dataGetSubmissions() {
		return [
			'getSubmissions' => [
				'expected' => [
					'submissions' => [
						[
							// 'formId' => Checked dynamically
							'userId' => 'user1',
							'userDisplayName' => 'User No. 1',
							'timestamp' => 123456,
							'answers' => [
								[
									// 'submissionId' => Checked dynamically
									// 'questionId' => Checked dynamically
									'text' => 'This is a short answer.',
									'fileId' => null,
								],
								[
									// 'submissionId' => Checked dynamically
									// 'questionId' => Checked dynamically
									'text' => 'Option 1',
									'fileId' => null,
								]
							]
						],
						[
							// 'formId' => Checked dynamically
							'userId' => 'user2',
							'userDisplayName' => 'user2',
							'timestamp' => 12345,
							'answers' => [
								[
									// 'submissionId' => Checked dynamically
									// 'questionId' => Checked dynamically
									'text' => 'This is another short answer.',
									'fileId' => null,
								],
								[
									// 'submissionId' => Checked dynamically
									// 'questionId' => Checked dynamically
									'text' => 'Option 2',
									'fileId' => null,
								]
							]
						],
						[
							// 'formId' => Checked dynamically
							'userId' => 'user3',
							'userDisplayName' => 'user3',
							'timestamp' => 1234,
							'answers' => [
								[
									// 'submissionId' => Checked dynamically
									// 'questionId' => Checked dynamically
									'text' => '',
									'fileId' => null,
								]
							]
						]
					],
					'questions' => $this->dataGetFullForm()['getFullForm']['expected']['questions']
				]
			]
		];
	}
	/**
	 * @dataProvider dataGetSubmissions
	 *
	 * @param array $expected
	 */
	public function testGetSubmissions(array $expected) {
		$resp = $this->http->request('GET', "api/v2.1/submissions/{$this->testForms[0]['hash']}");
		$data = $this->OcsResponse2Data($resp);

		// Cannot control ids, but check general consistency.
		foreach ($data['submissions'] as $sIndex => $submission) {
			$this->assertEquals($this->testForms[0]['id'], $submission['formId']);
			unset($data['submissions'][$sIndex]['formId']);

			foreach ($submission['answers'] as $aIndex => $answer) {
				$this->assertEquals($submission['id'], $answer['submissionId']);
				$this->assertEquals($this->testForms[0]['questions'][
					$this->testForms[0]['submissions'][$sIndex]['answers'][$aIndex]['questionIndex']
				]['id'], $answer['questionId']);
				unset($data['submissions'][$sIndex]['answers'][$aIndex]['submissionId']);
				unset($data['submissions'][$sIndex]['answers'][$aIndex]['questionId']);
				unset($data['submissions'][$sIndex]['answers'][$aIndex]['id']);
			}
			unset($data['submissions'][$sIndex]['id']);
		}
		foreach ($data['questions'] as $qIndex => $question) {
			$this->assertEquals($this->testForms[0]['id'], $question['formId']);
			unset($data['questions'][$qIndex]['formId']);

			foreach ($question['options'] as $oIndex => $option) {
				$this->assertEquals($question['id'], $option['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['questionId']);
				unset($data['questions'][$qIndex]['options'][$oIndex]['id']);
			}
			unset($data['questions'][$qIndex]['id']);
		}

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($expected, $data);
	}

	public function dataExportSubmissions() {
		return [
			'exportSubmissions' => [
				'expected' => <<<'CSV'
					"User ID","User display name","Timestamp","First Question?","Second Question?","File Question?"
					"","Anonymous user","1970-01-01T00:20:34+00:00","","",""
					"","Anonymous user","1970-01-01T03:25:45+00:00","This is another short answer.","Option 2",""
					"user1","User No. 1","1970-01-02T10:17:36+00:00","This is a short answer.","Option 1",""
CSV
			]
		];
	}
	/**
	 * @dataProvider dataExportSubmissions
	 *
	 * @param array $expected
	 */
	public function testExportSubmissions(string $expected) {
		$resp = $this->http->request('GET', "api/v2.4/submissions/export/{$this->testForms[0]['hash']}");
		$data = substr($resp->getBody()->getContents(), 3); // Some strange Character removed at the beginning

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals('attachment; filename="Title of a Form (responses).csv"', $resp->getHeaders()['Content-Disposition'][0]);
		$this->assertEquals('text/csv;charset=UTF-8', $resp->getHeaders()['Content-type'][0]);
		$arr_txt_expected = preg_split('/,/', str_replace(["\t", "\n"], '', $expected));
		$arr_txt_data = preg_split('/,/', str_replace(["\t", "\n"], '', $data));
		$this->assertEquals($arr_txt_expected, $arr_txt_data);
	}

	public function testLinkFile() {
		$resp = $this->http->request('POST', 'api/v2.4/form/link/csv', [
			'json' => [
				'hash' => $this->testForms[0]['hash'],
				'path' => ''
			]]
		);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals('csv', $data['fileFormat']);
	}

	public function testUnlinkFile() {
		$resp = $this->http->request('POST', 'api/v2.4/form/unlink', [
			'json' => [
				'hash' => $this->testForms[2]['hash'],
				'path' => ''
			]]
		);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[2]['hash'], $data);
	}

	public function testExportToCloud() {
		$resp = $this->http->request('POST', 'api/v2.4/submissions/export', [
			'json' => [
				'hash' => $this->testForms[0]['hash'],
				'path' => ''
			]]
		);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals('Title of a Form (responses).csv', $data);
	}

	public function dataDeleteSubmissions() {
		$submissionsExpected = $this->dataGetSubmissions()['getSubmissions']['expected'];
		$submissionsExpected['submissions'] = [];

		return [
			'deleteSubmissions' => [
				'submissionsExpected' => $submissionsExpected
			]
		];
	}
	/**
	 * @dataProvider dataDeleteSubmissions
	 *
	 * @param array $submissionsExpected
	 */
	public function testDeleteSubmissions(array $submissionsExpected) {
		$resp = $this->http->request('DELETE', "api/v2.1/submissions/{$this->testForms[0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['id'], $data);

		$this->testGetSubmissions($submissionsExpected);
	}

	public function dataInsertSubmission() {
		$submissionsExpected = $this->dataGetSubmissions()['getSubmissions']['expected'];
		$submissionsExpected['submissions'][] = [
			'userId' => 'test'
		];

		return [
			'insertSubmission' => [
				'submissionsExpected' => $submissionsExpected
			]
		];
	}
	/**
	 * @dataProvider dataInsertSubmission
	 *
	 * @param array $submissionsExpected
	 */
	public function testInsertSubmission(array $submissionsExpected) {

		$uploadedFileResponse = $this->http->request('POST',
			'api/v2.5/uploadFiles/' . $this->testForms[0]['id'] . '/' . $this->testForms[0]['questions'][2]['id'],
			[
				'multipart' => [
					[
						'name' => 'files[]',
						'contents' => 'hello world',
						'filename' => 'test.txt'
					]
				]
			]);

		$data = $this->OcsResponse2Data($uploadedFileResponse);
		$uploadedFileId = $data[0]['uploadedFileId'];

		$resp = $this->http->request('POST', 'api/v2.4/submission/insert', [
			'json' => [
				'formId' => $this->testForms[0]['id'],
				'answers' => [
					$this->testForms[0]['questions'][0]['id'] => ['ShortAnswer!'],
					$this->testForms[0]['questions'][1]['id'] => [
						$this->testForms[0]['questions'][1]['options'][0]['id']
					],
					$this->testForms[0]['questions'][2]['id'] => [['uploadedFileId' => $uploadedFileId]]
				]
			]
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());

		// Check stored submissions
		$resp = $this->http->request('GET', "api/v2.1/submissions/{$this->testForms[0]['hash']}");
		$data = $this->OcsResponse2Data($resp);

		// Store for deletion
		$this->testForms[0]['submissions'][] = $data['submissions'][0];

		// Check Ids
		foreach ($data['submissions'][0]['answers'] as $aIndex => $answer) {
			$this->assertEquals($data['submissions'][0]['id'], $answer['submissionId']);
			unset($data['submissions'][0]['answers'][$aIndex]['id']);
			unset($data['submissions'][0]['answers'][$aIndex]['submissionId']);
		}
		unset($data['submissions'][0]['id']);
		// Check general behaviour of timestamp (Insert in the last 10 seconds)
		$this->assertTrue(time() - $data['submissions'][0]['timestamp'] < 10);
		unset($data['submissions'][0]['timestamp']);

		$this->assertEquals([
			'userId' => 'test',
			'userDisplayName' => 'Test Displayname',
			'formId' => $this->testForms[0]['id'],
			'answers' => [
				[
					'questionId' => $this->testForms[0]['questions'][0]['id'],
					'text' => 'ShortAnswer!',
					'fileId' => null,
				],
				[
					'questionId' => $this->testForms[0]['questions'][1]['id'],
					'text' => 'Option 1',
					'fileId' => null,
				],
				[
					'questionId' => $this->testForms[0]['questions'][2]['id'],
					'text' => 'test.txt',
					'fileId' => 28,
				],
			]
		], $data['submissions'][0]);
	}

	public function dataDeleteSingleSubmission() {
		$submissionsExpected = $this->dataGetSubmissions()['getSubmissions']['expected'];
		array_splice($submissionsExpected['submissions'], 0, 1);

		return [
			'deleteSingleSubmission' => [
				'submissionsExpected' => $submissionsExpected
			]
		];
	}
	/**
	 * @dataProvider dataDeleteSingleSubmission
	 *
	 * @param array $submissionsExpected
	 */
	public function testDeleteSingleSubmission(array $submissionsExpected) {
		$resp = $this->http->request('DELETE', "api/v2.1/submission/{$this->testForms[0]['submissions'][0]['id']}");
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals($this->testForms[0]['submissions'][0]['id'], $data);

		$this->testGetSubmissions($submissionsExpected);
	}

	/**
	 * Test transfer owner endpoint for form
	 *
	 * Keep this test at the end as it might break other tests
	 */
	public function testTransferOwner() {
		$resp = $this->http->request('POST', 'api/v2.4/form/transfer', [
			'json' => [
				'formId' => $this->testForms[0]['id'],
				'uid' => 'user1'
			],
		]);
		$data = $this->OcsResponse2Data($resp);

		$this->assertEquals(200, $resp->getStatusCode());
		$this->assertEquals('user1', $data);
	}
};
