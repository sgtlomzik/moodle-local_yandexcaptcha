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
 * Unit tests for the local_yandexcaptcha signup form callbacks.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/yandexcaptcha/lib.php');
require_once($CFG->libdir . '/formslib.php');

/**
 * Tests for the callbacks defined in lib.php.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     ::local_yandexcaptcha_is_active
 * @covers     ::local_yandexcaptcha_extend_signup_form
 * @covers     ::local_yandexcaptcha_validate_extend_signup_form
 */
final class lib_test extends \advanced_testcase {
    /**
     * Clear the per-request verification cache the captcha class keeps.
     *
     * The cache lives in static properties, which survive the database reset that
     * advanced_testcase does between tests, so it has to be cleared by hand.
     */
    protected function tearDown(): void {
        self::write_captcha_cache(null, null);
        parent::tearDown();
    }

    /**
     * Write the captcha class's per-request cache directly.
     *
     * @param string|null $token Token to present as already verified.
     * @param \stdClass|null $result Cached result for that token.
     */
    private static function write_captcha_cache(?string $token, ?\stdClass $result): void {
        $reflection = new \ReflectionClass(captcha::class);
        $reflection->getProperty('verifiedtoken')->setValue(null, $token);
        $reflection->getProperty('lastresult')->setValue(null, $result);
    }

    /**
     * Switch the plugin on with a usable site key.
     */
    private function enable_captcha(): void {
        set_config('enabled', 1, 'local_yandexcaptcha');
        set_config('sitekey', 'ysc1_sitekey', 'local_yandexcaptcha');
        set_config('secretkey', 'ysc2_secretkey', 'local_yandexcaptcha');
    }

    /**
     * The plugin is only active once it is both enabled and given a site key.
     *
     * @param int $enabled Value of the enabled setting.
     * @param string $sitekey Value of the site key setting.
     * @param bool $expected Whether the captcha is expected to be active.
     * @dataProvider is_active_provider
     */
    public function test_is_active(int $enabled, string $sitekey, bool $expected): void {
        $this->resetAfterTest();

        set_config('enabled', $enabled, 'local_yandexcaptcha');
        set_config('sitekey', $sitekey, 'local_yandexcaptcha');

        $this->assertSame($expected, local_yandexcaptcha_is_active());
    }

    /**
     * Data provider for {@see test_is_active()}.
     *
     * @return array[] Enabled flag, site key and expected activity.
     */
    public static function is_active_provider(): array {
        return [
            'disabled without a key' => [0, '', false],
            'disabled with a key' => [0, 'ysc1_sitekey', false],
            'enabled without a key' => [1, '', false],
            'enabled with a key' => [1, 'ysc1_sitekey', true],
        ];
    }

    /**
     * An unconfigured plugin leaves the signup form untouched.
     */
    public function test_extend_signup_form_adds_nothing_when_inactive(): void {
        $this->resetAfterTest();

        set_config('enabled', 0, 'local_yandexcaptcha');
        set_config('sitekey', '', 'local_yandexcaptcha');

        $mform = new \MoodleQuickForm('signup', 'post', '');
        local_yandexcaptcha_extend_signup_form($mform);

        $this->assertFalse($mform->elementExists('captcha_container'));
        $this->assertFalse($mform->elementExists('smartcaptcha_token'));
    }

    /**
     * An active plugin adds the widget container and the hidden token field.
     */
    public function test_extend_signup_form_adds_widget_and_token(): void {
        $this->resetAfterTest();
        $this->enable_captcha();

        $mform = new \MoodleQuickForm('signup', 'post', '');
        local_yandexcaptcha_extend_signup_form($mform);

        $this->assertTrue($mform->elementExists('captcha_container'));
        $this->assertTrue($mform->elementExists('smartcaptcha_token'));

        $container = $mform->getElement('captcha_container');
        $this->assertStringContainsString(LOCAL_YANDEXCAPTCHA_CONTAINER_ID, $container->toHtml());

        // The token is opaque and must never be cleaned into something else.
        $this->assertSame(PARAM_RAW_TRIMMED, $mform->getCleanType('smartcaptcha_token', ''));
    }

    /**
     * Validation is skipped entirely while the plugin is inactive.
     */
    public function test_validate_returns_no_errors_when_inactive(): void {
        $this->resetAfterTest();

        set_config('enabled', 0, 'local_yandexcaptcha');

        $this->assertSame([], local_yandexcaptcha_validate_extend_signup_form([]));
        $this->assertSame([], local_yandexcaptcha_validate_extend_signup_form(['smartcaptcha_token' => '']));
    }

    /**
     * A missing token is rejected without contacting the SmartCaptcha API.
     *
     * @param array $data Submitted signup form data.
     * @dataProvider missing_token_provider
     */
    public function test_validate_rejects_a_missing_token(array $data): void {
        $this->resetAfterTest();
        $this->enable_captcha();

        $errors = local_yandexcaptcha_validate_extend_signup_form($data);

        $this->assertArrayHasKey('captcha_container', $errors);
        $this->assertSame(get_string('error_nocaptcha', 'local_yandexcaptcha'), $errors['captcha_container']);
    }

    /**
     * Data provider for {@see test_validate_rejects_a_missing_token()}.
     *
     * @return array[] Submitted data sets that carry no token.
     */
    public static function missing_token_provider(): array {
        return [
            'no token element at all' => [[]],
            'empty token' => [['smartcaptcha_token' => '']],
        ];
    }

    /**
     * A token that cannot be verified is reported as a validation failure.
     */
    public function test_validate_rejects_an_unverifiable_token(): void {
        $this->resetAfterTest();

        // Enabled and keyed for the widget, but with no secret the verification
        // cannot succeed, so the form must not let the signup through.
        set_config('enabled', 1, 'local_yandexcaptcha');
        set_config('sitekey', 'ysc1_sitekey', 'local_yandexcaptcha');
        set_config('secretkey', '', 'local_yandexcaptcha');

        $errors = local_yandexcaptcha_validate_extend_signup_form(['smartcaptcha_token' => 'a-token']);
        $this->assertDebuggingCalled();

        $this->assertArrayHasKey('captcha_container', $errors);
        $this->assertSame(get_string('error_validation', 'local_yandexcaptcha'), $errors['captcha_container']);
    }

    /**
     * A verified token lets the signup through with no errors.
     */
    public function test_validate_accepts_a_verified_token(): void {
        $this->resetAfterTest();
        $this->enable_captcha();

        // Prime the per-request cache so verify() answers without a network call.
        self::write_captcha_cache('a-good-token', (object)['success' => true, 'message' => 'OK']);

        $this->assertSame([], local_yandexcaptcha_validate_extend_signup_form([
            'smartcaptcha_token' => 'a-good-token',
        ]));
    }
}
