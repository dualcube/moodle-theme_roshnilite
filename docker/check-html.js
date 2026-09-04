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
 * Pattern-based sanity check against the live dev site (docker compose up
 * must already be running) for the bug classes moodle-plugin-ci's mustache
 * lint step catches: unquoted attribute values that swallow the next
 * attribute, and empty href/src values that are invalid HTML5.
 *
 * This is NOT the same validator CI uses (that's a Java HTML5 validator -
 * vnu.jar - run by moodle-plugin-ci against each template's own documented
 * "Example context", which requires the full moodle-plugin-ci toolchain).
 * This script instead renders real pages from the running dev site and
 * pattern-matches the actual output - a fast, good-enough local check, not
 * a replacement for the real CI step.
 */

const puppeteer = require('puppeteer-core');

const BASE_URL = process.env.MOODLE_WWWROOT || 'http://localhost:8080';
const ADMIN_USER = process.env.MOODLE_ADMIN_USER || 'admin';
const ADMIN_PASS = process.env.MOODLE_ADMIN_PASS || 'RoshniliteDev#123';

const CHECKS = [
    { pattern: /href=""/g, label: 'empty href=""' },
    { pattern: /src=""/g, label: 'empty src=""' },
    { pattern: /<img [^>]*src=[^"][^ >]*/g, label: 'unquoted <img src=...>' },
    { pattern: /<ul[^>]*>\s*<div/g, label: '<div> directly inside <ul>' },
];

/**
 * Fetch a page's rendered HTML and report any of CHECKS found in it.
 *
 * @param {import('puppeteer-core').Page} page Puppeteer page to reuse.
 * @param {string} url Absolute URL to load.
 * @param {string} label Human-readable name for this page, used in output.
 * @return {Promise<boolean>} true if no issues were found.
 */
async function checkPage(page, url, label) {
    await page.goto(url, { waitUntil: 'networkidle0', timeout: 60000 });
    const html = await page.content();
    let ok = true;
    for (const check of CHECKS) {
        const matches = html.match(check.pattern);
        if (matches) {
            ok = false;
            console.log(`FAIL: ${label} - ${check.label} (${matches.length}x)`);
        }
    }
    if (ok) {
        console.log(`OK: ${label}`);
    }
    return ok;
}

(async () => {
    const browser = await puppeteer.launch({
        executablePath: '/usr/bin/google-chrome',
        headless: 'new',
        args: ['--no-sandbox', '--disable-gpu'],
    });
    const page = await browser.newPage();

    let allok = true;

    allok = await checkPage(page, `${BASE_URL}/login/index.php`, 'login page (logged out)') && allok;
    allok = await checkPage(page, `${BASE_URL}/`, 'front page (logged out)') && allok;

    await page.goto(`${BASE_URL}/login/index.php`, { waitUntil: 'networkidle0', timeout: 60000 });
    await page.type('#username', ADMIN_USER);
    await page.type('#password', ADMIN_PASS);
    await Promise.all([
        page.click('#loginbtn'),
        page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 60000 }),
    ]);

    allok = await checkPage(page, `${BASE_URL}/my/`, 'dashboard (logged in)') && allok;
    allok = await checkPage(page, `${BASE_URL}/course/view.php?id=1`, 'front page (logged in)') && allok;

    await browser.close();

    if (!allok) {
        console.log('\nSome checks failed.');
        process.exit(1);
    }
    console.log('\nAll checks passed.');
})();
