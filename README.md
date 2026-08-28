# Yandex SmartCaptcha for Moodle

[![Moodle plugin CI](https://github.com/sgtlomzik/moodle-local_yandexcaptcha/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/sgtlomzik/moodle-local_yandexcaptcha/actions/workflows/moodle-ci.yml)

Protects the Moodle self-registration form with [Yandex SmartCaptcha](https://yandex.cloud/en/services/smartcaptcha),
as an alternative to Google reCAPTCHA for sites that cannot or do not want to rely on Google services.

The plugin adds the SmartCaptcha widget to the "New account" signup form and verifies the
resulting token server-side before the account is created. Nothing is stored in the Moodle
database: the plugin only holds two settings and validates one token per submitted form.

## Requirements

- Moodle 4.5 (LTS) or later.
- A Yandex Cloud SmartCaptcha resource, which gives you a **client key** (site key) and a
  **server key** (secret key). See the [SmartCaptcha quickstart](https://yandex.cloud/en/docs/smartcaptcha/quickstart).
- Outbound HTTPS access from the Moodle server to `smartcaptcha.yandexcloud.net`.

## Installation

### From the ZIP file

1. Download the ZIP of this repository.
2. Go to **Site administration → Plugins → Install plugins** and upload the ZIP.
3. Follow the on-screen upgrade steps.

### From Git

```bash
cd /path/to/moodle
git clone https://github.com/sgtlomzik/moodle-local_yandexcaptcha.git local/yandexcaptcha
```

Then visit **Site administration → Notifications** (or run `php admin/cli/upgrade.php`) to
complete the installation.

## Configuration

Settings live at **Site administration → Security → Yandex SmartCaptcha**:

| Setting | Description |
| --- | --- |
| Enable captcha | Turns the widget on for the signup form. Off by default. |
| Site Key | The SmartCaptcha *client* key. Required once the captcha is enabled. |
| Secret Key | The SmartCaptcha *server* key. Stored masked; required once the captcha is enabled. |

Email-based self-registration also has to be enabled in Moodle itself
(**Site administration → Plugins → Authentication → Manage authentication → Self registration**),
otherwise there is no signup form to protect.

If the keys are missing or the captcha is disabled, the plugin stays completely out of the way —
the signup form renders and behaves exactly as it does without the plugin installed.

## How it works

1. `local_yandexcaptcha_extend_signup_form()` adds a container element and a hidden token field
   to the signup form, and loads the `local_yandexcaptcha/captcha` AMD module.
2. The AMD module loads the SmartCaptcha client library, renders the widget, and writes the
   issued token into the hidden field.
3. `local_yandexcaptcha_validate_extend_signup_form()` posts the token and the secret key to
   `https://smartcaptcha.yandexcloud.net/validate`. A non-`ok` status blocks the registration
   with a generic error message; the technical reason is written to the Moodle debug output only.

## Privacy

The plugin stores no personal data in Moodle. Rendering and validating the captcha does send the
visitor's IP address, browser user agent and the single-use captcha token to Yandex Cloud — this is
declared through the Privacy API and shown in the site's data registry. No Moodle account data is
sent. See the [SmartCaptcha documentation](https://yandex.cloud/en/docs/smartcaptcha/) for how
Yandex processes that data.

## Development

The JavaScript source lives in `amd/src/`. After changing it, regenerate the compiled module and
commit both files:

```bash
cd /path/to/moodle
npx grunt amd --root=local/yandexcaptcha
```

Coding style and static analysis are run in CI via
[moodle-plugin-ci](https://moodlehq.github.io/moodle-plugin-ci/).

## Bug tracker

Please report issues at
<https://github.com/sgtlomzik/moodle-local_yandexcaptcha/issues>.

## License

2026 SgtLomzik <lomzike@gmail.com>

This program is free software: you can redistribute it and/or modify it under the terms of the
GNU General Public License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not,
see <https://www.gnu.org/licenses/>.
