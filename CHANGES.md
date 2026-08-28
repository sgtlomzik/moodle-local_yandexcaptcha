# Changelog

All notable changes to this plugin are documented in this file.

## [1.1.0] - 2026-08-28

### Added
- GPLv3 boilerplate, `@package`/`@copyright`/`@license` docblocks and a `COPYING` file.
- Privacy API provider declaring the data sent to the external Yandex SmartCaptcha service.
- `amd/src/captcha.js`: the AMD module source, which had never been committed alongside its
  build artefact.
- Moodle plugin CI workflow (PHP lint, code checker, PHPDoc checker, validate, grunt, PHPUnit).
- `$plugin->supported` declaration.

### Changed
- The SmartCaptcha library is now loaded and awaited by the AMD module itself instead of being
  injected with inline JavaScript and then polled for 5 seconds.
- Widget container id namespaced to `local_yandexcaptcha-container`.
- Validation failures now show a generic message to the visitor; the technical reason goes to the
  developer debug output instead.
- Minimum requirement raised to Moodle 4.5 (LTS); older releases are out of support upstream.

### Fixed
- cURL transport errors were treated as a validation failure with no diagnostic; they are now
  detected via `get_errno()` and reported through `debugging()`.
- A malformed (non-JSON) API response could raise a PHP notice; the response is now type-checked.
- `filelib.php` is explicitly required before `\curl` is used.

### Removed
- `db/access.php`, which defined an empty capability array.
