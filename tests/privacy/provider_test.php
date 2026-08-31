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
 * Unit tests for the local_yandexcaptcha privacy provider.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_yandexcaptcha\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\metadata\types\external_location;

/**
 * Tests that the plugin declares the data the widget sends to Yandex Cloud.
 *
 * @package    local_yandexcaptcha
 * @copyright  2026 SgtLomzik <lomzike@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_yandexcaptcha\privacy\provider
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * The metadata declares the SmartCaptcha service as an external location.
     */
    public function test_get_metadata_declares_the_external_location(): void {
        $this->resetAfterTest();

        $collection = provider::get_metadata(new collection('local_yandexcaptcha'));
        $items = $collection->get_collection();

        $this->assertCount(1, $items);

        $item = reset($items);
        $this->assertInstanceOf(external_location::class, $item);
        $this->assertSame('yandexsmartcaptcha', $item->get_name());
        $this->assertSame('privacy:metadata:yandexsmartcaptcha', $item->get_summary());
        $this->assertEqualsCanonicalizing(
            ['ipaddress', 'useragent', 'token'],
            array_keys($item->get_privacy_fields())
        );
    }

    /**
     * Every metadata string the provider names is actually defined.
     */
    public function test_metadata_strings_exist(): void {
        $this->resetAfterTest();

        $collection = provider::get_metadata(new collection('local_yandexcaptcha'));

        foreach ($collection->get_collection() as $item) {
            $this->assertTrue(
                get_string_manager()->string_exists($item->get_summary(), 'local_yandexcaptcha'),
                "Missing language string {$item->get_summary()}"
            );

            foreach ($item->get_privacy_fields() as $field => $identifier) {
                $this->assertTrue(
                    get_string_manager()->string_exists($identifier, 'local_yandexcaptcha'),
                    "Missing language string {$identifier} for field {$field}"
                );
            }
        }
    }
}
