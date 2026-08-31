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
 * Unit tests for the plugin's admin setting classes.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/adminlib.php');

/**
 * Tests that the key settings refuse to be emptied while the captcha is enabled.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_yandexcaptcha\admin_setting_captchatext
 * @covers     \local_yandexcaptcha\admin_setting_captchasecret
 */
final class admin_setting_test extends \advanced_testcase {
    /**
     * Build the site key setting as settings.php does.
     *
     * @return admin_setting_captchatext
     */
    private function make_sitekey_setting(): admin_setting_captchatext {
        return new admin_setting_captchatext(
            'local_yandexcaptcha/sitekey',
            'Site key',
            '',
            '',
            PARAM_TEXT
        );
    }

    /**
     * Build the secret key setting as settings.php does.
     *
     * @return admin_setting_captchasecret
     */
    private function make_secretkey_setting(): admin_setting_captchasecret {
        return new admin_setting_captchasecret(
            'local_yandexcaptcha/secretkey',
            'Secret key',
            '',
            ''
        );
    }

    /**
     * An enabled captcha needs a site key.
     *
     * @param string $value The submitted site key.
     * @dataProvider empty_value_provider
     */
    public function test_sitekey_is_required_while_enabled(string $value): void {
        $this->resetAfterTest();

        set_config('enabled', 1, 'local_yandexcaptcha');

        $this->assertSame(
            get_string('error_sitekey_required', 'local_yandexcaptcha'),
            $this->make_sitekey_setting()->validate($value)
        );
    }

    /**
     * An enabled captcha needs a secret key.
     *
     * @param string $value The submitted secret key.
     * @dataProvider empty_value_provider
     */
    public function test_secretkey_is_required_while_enabled(string $value): void {
        $this->resetAfterTest();

        set_config('enabled', 1, 'local_yandexcaptcha');

        $this->assertSame(
            get_string('error_secretkey_required', 'local_yandexcaptcha'),
            $this->make_secretkey_setting()->validate($value)
        );
    }

    /**
     * Data provider listing the values that count as an empty key.
     *
     * @return array[] Submitted values.
     */
    public static function empty_value_provider(): array {
        return [
            'empty string' => [''],
            'whitespace only' => ["  \t "],
        ];
    }

    /**
     * A non-empty key is accepted while the captcha is enabled.
     */
    public function test_keys_are_accepted_while_enabled(): void {
        $this->resetAfterTest();

        set_config('enabled', 1, 'local_yandexcaptcha');

        $this->assertTrue($this->make_sitekey_setting()->validate('ysc1_sitekey'));
        $this->assertTrue($this->make_secretkey_setting()->validate('ysc2_secretkey'));
    }

    /**
     * The keys may be left empty while the captcha is switched off.
     */
    public function test_keys_may_be_empty_while_disabled(): void {
        $this->resetAfterTest();

        set_config('enabled', 0, 'local_yandexcaptcha');

        $this->assertTrue($this->make_sitekey_setting()->validate(''));
        $this->assertTrue($this->make_secretkey_setting()->validate(''));
    }
}
