<?php
/**
 *
 * Quick Title Edition extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2023, Kailey Snay, https://www.snayhomelab.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace kaileymsnay\qte\tests\unit\event;

class main_listener_test extends \phpbb_test_case
{
	public function test_viewtopic_get_attribute_without_valid_hash_is_ignored()
	{
		global $user;
		$previous_user = $user ?? null;
		$user = new \stdClass();
		$user->data = ['user_form_salt' => 'test-salt'];

		$request = $this->createMock('\phpbb\request\request');
		$request->expects($this->exactly(2))
			->method('variable')
			->willReturnCallback(function($name, $default) {
				return $name === 'attr_id' ? 1 : 'invalid-hash';
			});

		$qte = $this->createMock('\kaileymsnay\qte\qte');
		$qte->expects($this->never())->method('attr_apply');

		$listener = new \kaileymsnay\qte\event\main_listener(
			$this->createMock('\phpbb\cache\driver\driver_interface'),
			$this->createMock('\phpbb\db\driver\driver_interface'),
			$this->createMock('\phpbb\language\language'),
			$this->createMock('\phpbb\log\log'),
			$request,
			$this->createMock('\phpbb\template\template'),
			$this->createMock('\phpbb\user'),
			'phpbb_',
			$qte,
			$this->createMock('\kaileymsnay\qte\search\fulltext_attribute')
		);

		try
		{
			$listener->viewtopic_add_quickmod_option_before(new \phpbb\event\data([]));
		}
		finally
		{
			$user = $previous_user;
		}
	}
}
