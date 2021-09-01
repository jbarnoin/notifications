<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2021 Julien Barnoin <julien@barnoin.com>
 *
 * @license GNU AGPL version 3 or any later version
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Notifications\Controller;

use OCP\AppFramework\OCSController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends OCSController {
	/** @var \OCP\IConfig */
	protected $config;

	/** @var \OCP\IL10N */
	protected $l10n;

	/** @var string */
	protected $user;

	public function __construct(string $appName,
								IRequest $request,
								IConfig $config,
								IL10N $l10n,
								IUserSession $userSession) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->l10n = $l10n;
		$this->user = $userSession->getUser()->getUID();
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param int $notify_setting_batchtime
	 * @param bool $notifications_email_enabled
	 * @return DataResponse
	 */
	public function personal(
			int $notify_setting_batchtime = \OCA\Notifications\Settings\Personal::EMAIL_SEND_HOURLY,
			bool $notifications_email_enabled = false
	): DataResponse {
		$email_batch_time = 3600;
		if ($notify_setting_batchtime === \OCA\Notifications\Settings\Personal::EMAIL_SEND_DAILY) {
			$email_batch_time = 3600 * 24;
		} elseif ($notify_setting_batchtime === \OCA\Notifications\Settings\Personal::EMAIL_SEND_WEEKLY) {
			$email_batch_time = 3600 * 24 * 7;
		} elseif ($notify_setting_batchtime === \OCA\Notifications\Settings\Personal::EMAIL_SEND_ASAP) {
			$email_batch_time = 0;
		}

		$this->config->setUserValue(
			$this->user, 'notifications',
			'notify_setting_batchtime',
			(string) $email_batch_time
		);
		$this->config->setUserValue(
			$this->user, 'notifications',
			'notifications_email_enabled',
			$notifications_email_enabled ? '1' : '0'
		);

		return new DataResponse([
			'message' => $this->l10n->t('Your settings have been updated.'),
		]);
	}
}
