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
 * Renders the Yandex SmartCaptcha widget and keeps its token in a hidden input.
 *
 * @module     local_yandexcaptcha/captcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';

/** @type {string} URL of the SmartCaptcha client library. */
const SCRIPT_URL = 'https://smartcaptcha.yandexcloud.net/captcha.js';

/** @type {string[]} Interface languages supported by SmartCaptcha. */
const SUPPORTED_LANGUAGES = ['ru', 'en', 'be', 'kk', 'tt', 'uk', 'uz', 'tr'];

/** @type {string} Name of the hidden input holding the captcha token. */
const TOKEN_INPUT_NAME = 'smartcaptcha_token';

/** @type {Promise|null} Pending or resolved load of the SmartCaptcha library. */
let scriptPromise = null;

/**
 * Load the SmartCaptcha client library once per page.
 *
 * @returns {Promise} Resolved once window.smartCaptcha is available.
 */
const loadLibrary = () => {
    if (scriptPromise) {
        return scriptPromise;
    }

    scriptPromise = new Promise((resolve, reject) => {
        if (typeof window.smartCaptcha !== 'undefined') {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.src = SCRIPT_URL;
        script.defer = true;
        script.addEventListener('load', () => resolve());
        script.addEventListener('error', () => reject(new Error('Unable to load ' + SCRIPT_URL)));
        document.head.appendChild(script);
    });

    return scriptPromise;
};

/**
 * Work out which interface language the widget should use.
 *
 * @returns {string} A language code supported by SmartCaptcha.
 */
const getLanguage = () => {
    const lang = (document.documentElement.lang || 'en').split('-')[0].toLowerCase();

    return SUPPORTED_LANGUAGES.indexOf(lang) === -1 ? 'en' : lang;
};

/**
 * Write a token into the hidden form input.
 *
 * @param {string} token The token, or an empty string to clear it.
 */
const setToken = (token) => {
    const input = document.querySelector('input[name="' + TOKEN_INPUT_NAME + '"]');

    if (input) {
        input.value = token;
    }
};

/**
 * Render the widget into the given container.
 *
 * @param {string} sitekey The SmartCaptcha client key.
 * @param {string} containerId The id of the element to render into.
 */
const renderWidget = (sitekey, containerId) => {
    const container = document.getElementById(containerId);

    if (!container || typeof window.smartCaptcha === 'undefined') {
        return;
    }

    const widgetId = window.smartCaptcha.render(container, {
        sitekey: sitekey,
        hl: getLanguage(),
        callback: setToken,
        'expired-callback': () => {
            setToken('');
            window.smartCaptcha.reset(widgetId);
        },
    });
};

/**
 * Initialise the captcha for a form.
 *
 * @param {string} sitekey The SmartCaptcha client key.
 * @param {string} containerId The id of the element to render the widget into.
 * @param {string} loaderror Message to show when the widget cannot be loaded.
 */
export const init = (sitekey, containerId, loaderror) => {
    loadLibrary()
        .then(() => renderWidget(sitekey, containerId))
        .catch(() => Notification.addNotification({message: loaderror, type: 'error'}));
};
