/**
 * Full-page screenshots of the preview site at Floe's four breakpoints, into
 * the git-ignored .screenshots/ folder.
 *
 *   npm run screenshots                       all preview pages
 *   npm run screenshots -- /pricing/ /about/  specific paths
 *
 * Uses Playwright (installed with @wordpress/scripts) and a local Chrome or
 * Chromium: set CHROME_PATH if it isn't found. FLOE_URL overrides the site
 * (default http://floe.local).
 */
import { existsSync, mkdirSync } from 'node:fs';
import { dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const root = resolve( dirname( fileURLToPath( import.meta.url ) ), '..' );
const site = ( process.env.FLOE_URL || 'http://floe.local' ).replace( /\/$/, '' );
const widths = [ 375, 600, 1024, 1440 ];
const paths = process.argv.slice( 2 ).length
	? process.argv.slice( 2 )
	: [ '/', '/platform/', '/pricing/', '/about/', '/contact/', '/journal/', '/a-considered-approach-to-growing-a-website/', '/block-preview/' ];
const executablePath = process.env.CHROME_PATH || [ '/usr/bin/chromium', '/usr/bin/google-chrome-stable', '/usr/bin/google-chrome', '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome' ].find( existsSync );

const out = join( root, '.screenshots' );
mkdirSync( out, { recursive: true } );

const browser = await chromium.launch( { executablePath } );
for ( const width of widths ) {
	const page = await browser.newPage( { viewport: { width, height: 900 } } );
	for ( const path of paths ) {
		await page.goto( site + path, { waitUntil: 'networkidle' } );
		await page.evaluate( () => document.fonts.ready );
		const name = ( path.replace( /^\/|\/$/g, '' ).replace( /\//g, '-' ) || 'home' ) + `-${ width }.png`;
		await page.screenshot( { path: join( out, name ), fullPage: true } );
		console.log( '.screenshots/' + name );
	}
	await page.close();
}
await browser.close();
