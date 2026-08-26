<?php
/**
 * Admin settings for Yandex SmartCaptcha.
 *
 * @package    local_yandexcaptcha
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/yandexcaptcha/classes/admin_setting_captchatext.php');
require_once($CFG->dirroot . '/local/yandexcaptcha/classes/admin_setting_captchasecret.php');

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_yandexcaptcha',
        get_string('pluginname', 'local_yandexcaptcha')
    );

    $settings->add(new admin_setting_configcheckbox(
        'local_yandexcaptcha/enabled',
        get_string('enabled', 'local_yandexcaptcha'),
        get_string('enabled_desc', 'local_yandexcaptcha'),
        0
    ));

    $settings->add(new local_yandexcaptcha\admin_setting_captchatext(
        'local_yandexcaptcha/sitekey',
        get_string('sitekey', 'local_yandexcaptcha'),
        get_string('sitekey_desc', 'local_yandexcaptcha'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new local_yandexcaptcha\admin_setting_captchasecret(
        'local_yandexcaptcha/secretkey',
        get_string('secretkey', 'local_yandexcaptcha'),
        get_string('secretkey_desc', 'local_yandexcaptcha'),
        ''
    ));

    $ADMIN->add('security', $settings);
}