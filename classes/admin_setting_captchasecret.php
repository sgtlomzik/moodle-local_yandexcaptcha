<?php
/**
 * Custom admin setting for secret key with validation.
 *
 * @package    local_yandexcaptcha
 */

namespace local_yandexcaptcha;

defined('MOODLE_INTERNAL') || die();

class admin_setting_captchasecret extends \admin_setting_configpasswordunmask {

    /**
     * Validate the setting value.
     *
     * @param string $data The value to validate.
     * @return mixed True if valid, error string if not.
     */
    public function validate($data) {
        $enabled = get_config('local_yandexcaptcha', 'enabled');

        if ($enabled && empty(trim($data))) {
            return get_string('error_secretkey_required', 'local_yandexcaptcha');
        }

        return true;
    }
}