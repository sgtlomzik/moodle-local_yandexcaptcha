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
 * Signup form callbacks for the Yandex SmartCaptcha plugin.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Id of the element the captcha widget is rendered into.
 */
const LOCAL_YANDEXCAPTCHA_CONTAINER_ID = 'local_yandexcaptcha-container';

/**
 * Check whether the captcha is switched on and fully configured.
 *
 * @return bool True when the widget should be shown and validated.
 */
function local_yandexcaptcha_is_active(): bool {
    $enabled = get_config('local_yandexcaptcha', 'enabled');
    $sitekey = get_config('local_yandexcaptcha', 'sitekey');

    return !empty($enabled) && !empty($sitekey);
}

/**
 * Add the Yandex SmartCaptcha widget to the signup form.
 *
 * @param MoodleQuickForm $mform The signup form.
 */
function local_yandexcaptcha_extend_signup_form($mform) {
    global $PAGE;

    if (!local_yandexcaptcha_is_active()) {
        return;
    }

    $sitekey = get_config('local_yandexcaptcha', 'sitekey');

    $mform->addElement(
        'static',
        'captcha_container',
        '',
        html_writer::div('', '', ['id' => LOCAL_YANDEXCAPTCHA_CONTAINER_ID])
    );

    $mform->addElement('hidden', 'smartcaptcha_token', '');
    // The token is an opaque value produced by SmartCaptcha. It is never printed
    // back to the page, only URL-encoded and posted to the validation API.
    $mform->setType('smartcaptcha_token', PARAM_RAW_TRIMMED);

    $PAGE->requires->js_call_amd('local_yandexcaptcha/captcha', 'init', [
        $sitekey,
        LOCAL_YANDEXCAPTCHA_CONTAINER_ID,
        get_string('error_loadfailed', 'local_yandexcaptcha'),
    ]);
}

/**
 * Validate the captcha token submitted with the signup form.
 *
 * @param array $data The submitted form data.
 * @return array Validation errors, keyed by form element name.
 */
function local_yandexcaptcha_validate_extend_signup_form($data) {
    $errors = [];

    if (!local_yandexcaptcha_is_active()) {
        return $errors;
    }

    $token = $data['smartcaptcha_token'] ?? '';

    if ($token === '') {
        $errors['captcha_container'] = get_string('error_nocaptcha', 'local_yandexcaptcha');
        return $errors;
    }

    $captcha = new \local_yandexcaptcha\captcha();
    $result = $captcha->verify($token);

    if (!$result->success) {
        $errors['captcha_container'] = get_string('error_validation', 'local_yandexcaptcha');
    }

    return $errors;
}
