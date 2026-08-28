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
 * English strings for local_yandexcaptcha.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enabled'] = 'Enable captcha';
$string['enabled_desc'] = 'Show Yandex SmartCaptcha on the new account (signup) form.';
$string['error_loadfailed'] = 'The captcha could not be loaded. Please refresh the page and try again.';
$string['error_nocaptcha'] = 'Please complete the verification.';
$string['error_secretkey_required'] = 'Secret Key is required when captcha is enabled.';
$string['error_sitekey_required'] = 'Site Key is required when captcha is enabled.';
$string['error_validation'] = 'Verification failed. Please refresh the page and try again.';
$string['pluginname'] = 'Yandex SmartCaptcha';
$string['privacy:metadata:yandexsmartcaptcha'] = 'To tell people apart from bots, the signup form sends verification data to the Yandex SmartCaptcha service. No Moodle user account data is sent.';
$string['privacy:metadata:yandexsmartcaptcha:ipaddress'] = 'The IP address of the person filling in the form is sent to Yandex SmartCaptcha.';
$string['privacy:metadata:yandexsmartcaptcha:token'] = 'The single-use captcha token is sent to Yandex SmartCaptcha for validation.';
$string['privacy:metadata:yandexsmartcaptcha:useragent'] = 'The browser user agent of the person filling in the form is sent to Yandex SmartCaptcha.';
$string['secretkey'] = 'Secret Key';
$string['secretkey_desc'] = 'The server-side secret key used to validate captcha tokens.';
$string['sitekey'] = 'Site Key';
$string['sitekey_desc'] = 'The client-side key used to render the captcha widget.';
