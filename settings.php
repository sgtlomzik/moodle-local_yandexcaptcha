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
 * Admin settings for the Yandex SmartCaptcha plugin.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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

    $settings->add(new \local_yandexcaptcha\admin_setting_captchatext(
        'local_yandexcaptcha/sitekey',
        get_string('sitekey', 'local_yandexcaptcha'),
        get_string('sitekey_desc', 'local_yandexcaptcha'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new \local_yandexcaptcha\admin_setting_captchasecret(
        'local_yandexcaptcha/secretkey',
        get_string('secretkey', 'local_yandexcaptcha'),
        get_string('secretkey_desc', 'local_yandexcaptcha'),
        ''
    ));

    $ADMIN->add('security', $settings);
}
