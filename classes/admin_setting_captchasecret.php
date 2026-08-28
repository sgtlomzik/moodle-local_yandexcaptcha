<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin setting for the SmartCaptcha secret key.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha;

/**
 * Masked password setting that refuses to be left empty while the captcha is enabled.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_captchasecret extends \admin_setting_configpasswordunmask {
    /**
     * Validate the submitted value.
     *
     * @param string $data The value to validate.
     * @return bool|string True if valid, an error message otherwise.
     */
    public function validate($data) {
        $enabled = get_config('local_yandexcaptcha', 'enabled');

        if ($enabled && trim((string)$data) === '') {
            return get_string('error_secretkey_required', 'local_yandexcaptcha');
        }

        return parent::validate($data);
    }
}
