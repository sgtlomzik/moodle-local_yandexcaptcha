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
 * Yandex SmartCaptcha verification.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

/**
 * Verifies Yandex SmartCaptcha tokens against the SmartCaptcha validation API.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class captcha {

    /** @var string Yandex SmartCaptcha validation API endpoint. */
    private const API_URL = 'https://smartcaptcha.yandexcloud.net/validate';

    /** @var int Request timeout in seconds. */
    private const TIMEOUT = 10;

    /** @var string|null Token that was already verified during the current request. */
    private static $verifiedtoken = null;

    /** @var \stdClass|null Cached result for the token in $verifiedtoken. */
    private static $lastresult = null;

    /**
     * Verify a captcha token with the Yandex SmartCaptcha API.
     *
     * The result is cached per request so that a form which is validated more than
     * once does not spend a second single-use token.
     *
     * @param string $token The captcha token supplied by the client.
     * @return \stdClass Object with 'success' (bool) and 'message' (string) properties.
     */
    public function verify(string $token): \stdClass {
        if (self::$verifiedtoken === $token && self::$lastresult !== null) {
            return self::$lastresult;
        }

        $secretkey = trim((string)get_config('local_yandexcaptcha', 'secretkey'));

        if ($secretkey === '') {
            return self::fail('Secret key is not configured');
        }

        $postdata = 'secret=' . urlencode($secretkey) . '&token=' . urlencode($token);

        try {
            $curl = new \curl();
            $curl->setopt([
                'CURLOPT_TIMEOUT' => self::TIMEOUT,
                'CURLOPT_CONNECTTIMEOUT' => self::TIMEOUT,
            ]);

            $response = $curl->post(self::API_URL, $postdata);

            if ($curl->get_errno()) {
                return self::fail('Request to the SmartCaptcha API failed: ' . $curl->error);
            }

            $data = json_decode($response);

            if (!is_object($data)) {
                return self::fail('Unexpected response from the SmartCaptcha API');
            }

            $success = isset($data->status) && $data->status === 'ok';

            $result = (object)[
                'success' => $success,
                'message' => $success ? 'OK' : (string)($data->message ?? 'Validation failed'),
            ];

            self::$verifiedtoken = $token;
            self::$lastresult = $result;

            return $result;
        } catch (\Exception $e) {
            return self::fail($e->getMessage());
        }
    }

    /**
     * Build a failed verification result and record the reason for administrators.
     *
     * The reason is never shown to the person filling in the form; they get a
     * generic message instead so that configuration details are not disclosed.
     *
     * @param string $message Technical reason for the failure.
     * @return \stdClass
     */
    private static function fail(string $message): \stdClass {
        debugging('local_yandexcaptcha: ' . $message, DEBUG_DEVELOPER);

        return (object)[
            'success' => false,
            'message' => $message,
        ];
    }
}
