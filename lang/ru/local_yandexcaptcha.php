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
 * Russian strings for local_yandexcaptcha.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['enabled'] = 'Включить капчу';
$string['enabled_desc'] = 'Показывать Yandex SmartCaptcha на форме регистрации.';
$string['error_loadfailed'] = 'Не удалось загрузить капчу. Обновите страницу и попробуйте снова.';
$string['error_nocaptcha'] = 'Пожалуйста, подтвердите, что вы не робот.';
$string['error_secretkey_required'] = 'Ключ сервера обязателен при включённой капче.';
$string['error_sitekey_required'] = 'Ключ клиента обязателен при включённой капче.';
$string['error_validation'] = 'Проверка не пройдена. Попробуйте обновить страницу.';
$string['pluginname'] = 'Yandex SmartCaptcha';
$string['privacy:metadata:yandexsmartcaptcha'] = 'Чтобы отличить человека от робота, форма регистрации передаёт данные проверки в сервис Yandex SmartCaptcha. Данные учётных записей Moodle не передаются.';
$string['privacy:metadata:yandexsmartcaptcha:ipaddress'] = 'IP-адрес заполняющего форму передаётся в Yandex SmartCaptcha.';
$string['privacy:metadata:yandexsmartcaptcha:token'] = 'Одноразовый токен капчи передаётся в Yandex SmartCaptcha для проверки.';
$string['privacy:metadata:yandexsmartcaptcha:useragent'] = 'User-agent браузера заполняющего форму передаётся в Yandex SmartCaptcha.';
$string['secretkey'] = 'Ключ сервера';
$string['secretkey_desc'] = 'Секретный ключ (Backend) для проверки токенов капчи.';
$string['sitekey'] = 'Ключ клиента';
$string['sitekey_desc'] = 'Ключ виджета (Frontend) для отображения капчи.';
