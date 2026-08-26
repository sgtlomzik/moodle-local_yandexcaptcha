<?php
/**
 * Library functions for Yandex SmartCaptcha plugin.
 *
 * @package    local_yandexcaptcha
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extends the signup form with Yandex SmartCaptcha widget.
 *
 * @param MoodleQuickForm $mform The form object.
 */
function local_yandexcaptcha_extend_signup_form($mform) {
    global $PAGE;

    $enabled = get_config('local_yandexcaptcha', 'enabled');
    $sitekey = get_config('local_yandexcaptcha', 'sitekey');

    if (empty($enabled) || empty($sitekey)) {
        return;
    }

    $mform->addElement('static', 'captcha_container', '', '<div id="captcha-container"></div>');

    $mform->addElement('hidden', 'smartcaptcha_token', '');
    $mform->setType('smartcaptcha_token', PARAM_RAW);

    $jsloader = "
        var script = document.createElement('script');
        script.src = 'https://smartcaptcha.yandexcloud.net/captcha.js';
        script.defer = true;
        document.head.appendChild(script);
    ";
    $PAGE->requires->js_init_code($jsloader);

    $PAGE->requires->js_call_amd('local_yandexcaptcha/captcha', 'init', [$sitekey, 'captcha-container']);
}

/**
 * Validates the captcha token from signup form.
 *
 * @param array $data The submitted form data.
 * @return array Validation errors.
 */
function local_yandexcaptcha_validate_extend_signup_form($data) {
    $errors = [];

    $enabled = get_config('local_yandexcaptcha', 'enabled');
    $sitekey = get_config('local_yandexcaptcha', 'sitekey');

    if (empty($enabled) || empty($sitekey)) {
        return $errors;
    }

    $token = $data['smartcaptcha_token'] ?? '';

    if (empty($token)) {
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