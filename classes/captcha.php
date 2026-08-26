<?php
/**
 * Yandex SmartCaptcha verification class.
 *
 * @package    local_yandexcaptcha
 */

namespace local_yandexcaptcha;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for verifying Yandex SmartCaptcha tokens.
 */
class captcha {

    /** @var string Yandex SmartCaptcha validation API endpoint. */
    private const API_URL = 'https://smartcaptcha.yandexcloud.net/validate';

    /** @var string|null Token that was already verified in current request. */
    private static $verifiedtoken = null;

    /** @var object|null Cached result for verified token. */
    private static $lastresult = null;

    /**
     * Verify captcha token with Yandex SmartCaptcha API.
     *
     * @param string $token The captcha token from client.
     * @return object Object with 'success' (bool) and 'message' (string) properties.
     */
    public function verify(string $token): object {
        if (self::$verifiedtoken === $token && self::$lastresult !== null) {
            return self::$lastresult;
        }

        $secretkey = trim(get_config('local_yandexcaptcha', 'secretkey'));

        if (empty($secretkey)) {
            return (object)[
                'success' => false,
                'message' => 'Secret key is not configured'
            ];
        }

        $postdata = 'secret=' . urlencode($secretkey) . '&token=' . urlencode($token);

        try {
            $curl = new \curl();
            $curl->setopt(['CURLOPT_TIMEOUT' => 10]);

            $response = $curl->post(self::API_URL, $postdata);
            $data = json_decode($response);

            $success = isset($data->status) && $data->status === 'ok';

            $result = (object)[
                'success' => $success,
                'message' => $success ? 'OK' : ($data->message ?? 'Validation failed')
            ];

            self::$verifiedtoken = $token;
            self::$lastresult = $result;

            return $result;

        } catch (\Exception $e) {
            return (object)[
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}