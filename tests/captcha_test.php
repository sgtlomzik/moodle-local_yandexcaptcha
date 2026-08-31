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
 * Unit tests for the SmartCaptcha verification class.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha;

/**
 * Tests for the token verification performed by the captcha class.
 *
 * The verification endpoint is never contacted from these tests: every case here
 * is one that is decided before the request is made, or one answered from the
 * per-request cache.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_yandexcaptcha\captcha
 */
final class captcha_test extends \advanced_testcase {
    /**
     * Clear the per-request cache, which static properties would otherwise carry
     * over into the tests that run after this one.
     */
    protected function tearDown(): void {
        self::write_cache(null, null);
        parent::tearDown();
    }

    /**
     * Write the class's per-request cache directly.
     *
     * @param string|null $token Token to present as already verified.
     * @param \stdClass|null $result Cached result for that token.
     */
    private static function write_cache(?string $token, ?\stdClass $result): void {
        $reflection = new \ReflectionClass(captcha::class);
        $reflection->getProperty('verifiedtoken')->setValue(null, $token);
        $reflection->getProperty('lastresult')->setValue(null, $result);
    }

    /**
     * Read the token currently held in the per-request cache.
     *
     * @return string|null
     */
    private static function read_cached_token(): ?string {
        $reflection = new \ReflectionClass(captcha::class);
        return $reflection->getProperty('verifiedtoken')->getValue();
    }

    /**
     * Verification fails, without a request, while no secret key is configured.
     *
     * @param string $secret The configured secret key.
     * @dataProvider missing_secret_provider
     */
    public function test_verify_fails_without_a_secret_key(string $secret): void {
        $this->resetAfterTest();

        set_config('secretkey', $secret, 'local_yandexcaptcha');

        $result = (new captcha())->verify('some-token');
        $this->assertDebuggingCalled('local_yandexcaptcha: Secret key is not configured');

        $this->assertFalse($result->success);
        $this->assertSame('Secret key is not configured', $result->message);

        // A refusal must not be cached: configuring the key has to take effect at once.
        $this->assertNull(self::read_cached_token());
    }

    /**
     * Data provider for {@see test_verify_fails_without_a_secret_key()}.
     *
     * @return array[] Secret key values that count as unconfigured.
     */
    public static function missing_secret_provider(): array {
        return [
            'never set' => [''],
            'whitespace only' => ["  \n "],
        ];
    }

    /**
     * A token verified earlier in the request is answered from the cache.
     */
    public function test_verify_reuses_the_result_for_the_same_token(): void {
        $this->resetAfterTest();

        // No secret key: reaching the verification code would fail and raise a
        // debugging message, so a clean success proves the cache answered.
        set_config('secretkey', '', 'local_yandexcaptcha');
        self::write_cache('single-use-token', (object)['success' => true, 'message' => 'OK']);

        $result = (new captcha())->verify('single-use-token');

        $this->assertTrue($result->success);
        $this->assertSame('OK', $result->message);
    }

    /**
     * A cached failure is reused as well, so one bad token is only spent once.
     */
    public function test_verify_reuses_a_cached_failure(): void {
        $this->resetAfterTest();

        set_config('secretkey', '', 'local_yandexcaptcha');
        self::write_cache('bad-token', (object)['success' => false, 'message' => 'Validation failed']);

        $result = (new captcha())->verify('bad-token');

        $this->assertFalse($result->success);
        $this->assertSame('Validation failed', $result->message);
    }

    /**
     * The cache is keyed by token: a different token is not answered from it.
     */
    public function test_verify_does_not_reuse_the_result_for_another_token(): void {
        $this->resetAfterTest();

        set_config('secretkey', '', 'local_yandexcaptcha');
        self::write_cache('first-token', (object)['success' => true, 'message' => 'OK']);

        $result = (new captcha())->verify('second-token');
        $this->assertDebuggingCalled('local_yandexcaptcha: Secret key is not configured');

        $this->assertFalse($result->success);
    }
}
