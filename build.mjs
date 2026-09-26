/*
 * Floe build. Discovers every module by folder and compiles it in place:
 *
 *   blocks/<name>/ (or blocks/<parent>/<name>/) with a block.json
 *   components/<name>/
 *
 *   <name>.scss         -> assets/<name>.css
 *   <name>-editor.scss  -> assets/<name>-editor.css
 *   <name>.js           -> assets/<name>.js         (front end)
 *   <name>-editor.js    -> assets/<name>-editor.js  (editor)
 *
 * plus the global assets/scss/*.scss -> assets/css/*.css and the Geist fonts.
 * No module names are listed anywhere: adding a folder adds it to the build.
 * Folders starting with "_" are ignored.
 *
 *   npm run build   one-off production build
 *   npm run start   build, then watch, recompile on save and live-reload
 *                   floe.local through BrowserSync (FLOE_PROXY overrides the URL)
 */
import {
	existsSync,
	mkdirSync,
	readdirSync,
	copyFileSync,
	writeFileSync,
} from 'node:fs';
import { basename, dirname, join, relative, sep } from 'node:path';
import { fileURLToPath } from 'node:url';
import { createRequire } from 'node:module';
import * as sass from 'sass';

const require = createRequire( import.meta.url );
const root = dirname( fileURLToPath( import.meta.url ) );
const watch = process.argv.includes( '--watch' );
let only = 'all';
if ( process.argv.includes( '--css' ) ) {
	only = 'css';
} else if ( process.argv.includes( '--js' ) ) {
	only = 'js';
}

const log = ( ...args ) => console.log( '[floe]', ...args );
const rel = ( path ) => relative( root, path ).split( sep ).join( '/' );

// ---------------------------------------------------------------------------
// Discovery
// ---------------------------------------------------------------------------
const subfolders = ( dir ) =>
	existsSync( dir )
		? readdirSync( dir, { withFileTypes: true } )
				.filter(
					( entry ) =>
						entry.isDirectory() &&
						! entry.name.startsWith( '_' ) &&
						! entry.name.startsWith( '.' ) &&
						entry.name !== 'assets'
				)
				.map( ( entry ) => join( dir, entry.name ) )
		: [];

export function discoverModules() {
	const modules = [];
	for ( const dir of subfolders( join( root, 'blocks' ) ) ) {
		if ( existsSync( join( dir, 'block.json' ) ) ) {
			modules.push( { type: 'block', dir, name: basename( dir ) } );
		}
		for ( const child of subfolders( dir ) ) {
			if ( existsSync( join( child, 'block.json' ) ) ) {
				modules.push( {
					type: 'block',
					dir: child,
					name: basename( child ),
				} );
			}
		}
	}
	for ( const dir of subfolders( join( root, 'components' ) ) ) {
		modules.push( { type: 'component', dir, name: basename( dir ) } );
	}
	return modules;
}

// ---------------------------------------------------------------------------
// Sass
// ---------------------------------------------------------------------------
function compileScss( input, output ) {
	try {
		const result = sass.compile( input, {
			style: 'compressed',
			loadPaths: [ join( root, 'assets/scss' ) ],
			quietDeps: true,
		} );
		mkdirSync( dirname( output ), { recursive: true } );
		writeFileSync( output, result.css );
		log( 'css', rel( output ) );
		return true;
	} catch ( error ) {
		console.error(
			`[floe] Sass error in ${ rel( input ) }:\n${ error.message }`
		);
		return false;
	}
}

function compileModuleCss( module ) {
	let ok = true;
	for ( const suffix of [ '', '-editor' ] ) {
		const input = join( module.dir, `${ module.name }${ suffix }.scss` );
		if ( existsSync( input ) ) {
			ok =
				compileScss(
					input,
					join(
						module.dir,
						'assets',
						`${ module.name }${ suffix }.css`
					)
				) && ok;
		}
	}
	return ok;
}

function compileGlobalCss() {
	let ok = true;
	const dir = join( root, 'assets/scss' );
	for ( const file of readdirSync( dir ) ) {
		if ( file.endsWith( '.scss' ) && ! file.startsWith( '_' ) ) {
			ok =
				compileScss(
					join( dir, file ),
					join(
						root,
						'assets/css',
						file.replace( /\.scss$/, '.css' )
					)
				) && ok;
		}
	}
	return ok;
}

function buildCss( modules ) {
	let ok = compileGlobalCss();
	for ( const module of modules ) {
		ok = compileModuleCss( module ) && ok;
	}
	return ok;
}

// ---------------------------------------------------------------------------
// Fonts (SIL OFL, from the geist npm package)
// ---------------------------------------------------------------------------
function copyFonts() {
	const fonts = join( root, 'node_modules/geist/dist/fonts' );
	const target = join( root, 'assets/fonts' );
	mkdirSync( target, { recursive: true } );
	const files = {
		'geist-sans/Geist-Variable.woff2': 'Geist-Variable.woff2',
		'geist-sans/Geist-Italic[wght].woff2': 'Geist-Italic-Variable.woff2',
		'geist-mono/GeistMono-Variable.woff2': 'GeistMono-Variable.woff2',
	};
	for ( const [ from, to ] of Object.entries( files ) ) {
		if ( existsSync( join( fonts, from ) ) ) {
			copyFileSync( join( fonts, from ), join( target, to ) );
		}
	}
	const licence = join( root, 'node_modules/geist/LICENSE.txt' );
	if ( existsSync( licence ) ) {
		copyFileSync( licence, join( target, 'OFL.txt' ) );
	}
}

// ---------------------------------------------------------------------------
// JavaScript (webpack, one self-contained bundle per file)
// ---------------------------------------------------------------------------
function jsEntries( modules ) {
	const entries = {};
	for ( const module of modules ) {
		// A component's -editor.js is a library that block editor scripts
		// import through @floe/components/<name>; it isn't a bundle of its own.
		const suffixes =
			module.type === 'component' ? [ '' ] : [ '', '-editor' ];
		for ( const suffix of suffixes ) {
			const file = join( module.dir, `${ module.name }${ suffix }.js` );
			if ( existsSync( file ) ) {
				entries[
					rel(
						join(
							module.dir,
							'assets',
							`${ module.name }${ suffix }`
						)
					)
				] = file;
			}
		}
	}
	const globalJs = join( root, 'assets/js' );
	if ( existsSync( globalJs ) ) {
		for ( const file of readdirSync( globalJs ) ) {
			if ( file.endsWith( '.js' ) && ! file.startsWith( '_' ) ) {
				entries[ `assets/js/build/${ file.replace( /\.js$/, '' ) }` ] =
					join( globalJs, file );
			}
		}
	}
	return entries;
}

function webpackConfig( modules ) {
	const webpack = require( 'webpack' );
	const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

	return {
		mode: 'production',
		context: root,
		entry: jsEntries( modules ),
		output: { path: root, filename: '[name].js', clean: false },
		devtool: false,
		resolve: {
			extensions: [ '.js', '.jsx' ],
			alias: { '@floe/editor': join( root, 'assets/js/editor' ) },
		},
		module: {
			rules: [
				{
					test: /\.jsx?$/,
					exclude: /node_modules/,
					use: {
						loader: require.resolve( 'babel-loader' ),
						options: {
							babelrc: false,
							configFile: false,
							presets: [
								require.resolve( '@wordpress/babel-preset-default' ),
							],
							cacheDirectory: true,
						},
					},
				},
			],
		},
		optimization: { splitChunks: false, runtimeChunk: false },
		performance: { hints: false },
		plugins: [
			// `import { Button } from '@floe/components/button'` loads
			// components/button/button-editor.js. A missing component fails the
			// build with a clear message instead of failing silently.
			new webpack.NormalModuleReplacementPlugin(
				/^@floe\/components\/[^/]+$/,
				( resource ) => {
					const name = resource.request.split( '/' ).pop();
					const file = join(
						root,
						'components',
						name,
						`${ name }-editor.js`
					);
					if ( ! existsSync( file ) ) {
						throw new Error(
							`Floe: "${ resource.request }" is imported by ${ rel( resource.contextInfo?.issuer || resource.context ) }, ` +
								`but components/${ name }/${ name }-editor.js does not exist. Restore the component or remove the import.`
						);
					}
					resource.request = file;
				}
			),
			new DependencyExtractionWebpackPlugin(),
		],
		stats: 'errors-warnings',
	};
}

function runWebpack( modules ) {
	const config = webpackConfig( modules );
	if ( ! Object.keys( config.entry ).length ) {
		return Promise.resolve( true );
	}
	const webpack = require( 'webpack' );
	return new Promise( ( resolvePromise ) => {
		const compiler = webpack( config );
		const report = ( error, stats ) => {
			if ( error ) {
				console.error( '[floe]', error.message );
				return resolvePromise( false );
			}
			const info = stats.toString( {
				colors: true,
				all: false,
				errors: true,
				warnings: true,
				errorDetails: true,
			} );
			if ( info.trim() ) {
				console.log( info );
			}
			log(
				`js ${ Object.keys( config.entry ).length } bundles ${ stats.hasErrors() ? 'FAILED' : 'built' }`
			);
			resolvePromise( ! stats.hasErrors() );
		};
		if ( watch ) {
			compiler.watch(
				{
					ignored: [
						'**/node_modules/*',
						'**/assets/*',
						'**/assets/js/build/**',
					],
				},
				( error, stats ) => {
					report( error, stats );
					reload();
				}
			);
		} else {
			compiler.run( ( error, stats ) =>
				compiler.close( () => report( error, stats ) )
			);
		}
	} );
}

// ---------------------------------------------------------------------------
// Watch + BrowserSync
// ---------------------------------------------------------------------------
let browserSync;
function reload( files ) {
	if ( browserSync ) {
		browserSync.reload( files );
	}
}

function moduleFor( file, modules ) {
	return modules
		.filter( ( module ) => file.startsWith( module.dir + sep ) )
		.sort( ( a, b ) => b.dir.length - a.dir.length )[ 0 ];
}

async function startWatch( modules ) {
	const chokidar = require( 'chokidar' );
	const proxy = process.env.FLOE_PROXY || 'http://floe.local';

	browserSync = require( 'browser-sync' ).create();
	browserSync.init( {
		proxy,
		open: false,
		notify: false,
		ui: false,
		logLevel: 'info',
	} );

	chokidar
		.watch( [ 'Blocks', 'Components', 'assets/scss' ], {
			cwd: root,
			ignoreInitial: true,
			ignored: /(^|[/\\])(assets|node_modules)([/\\]|$)/,
		} )
		.on( 'all', ( event, path ) => {
			const file = join( root, path );
			if ( file.endsWith( '.scss' ) ) {
				const module = moduleFor( file, modules );
				if ( module ) {
					compileModuleCss( module );
				} else {
					buildCss( modules );
				}
				reload( '*.css' );
			} else if (
				file.endsWith( '.php' ) ||
				file.endsWith( 'block.json' )
			) {
				reload();
			}
			if ( event === 'addDir' || event === 'unlinkDir' ) {
				log(
					'A folder was added or removed. Restart `npm run start` to pick up new modules.'
				);
			}
		} );

	chokidar
		.watch( [ '*.php', 'includes', 'theme.json' ], {
			cwd: root,
			ignoreInitial: true,
		} )
		.on( 'change', () => reload() );

	log( `watching, live reload via BrowserSync proxying ${ proxy }` );
}

// ---------------------------------------------------------------------------
const modules = discoverModules();
log(
	`${ modules.filter( ( m ) => m.type === 'block' ).length } blocks, ${ modules.filter( ( m ) => m.type === 'component' ).length } components`
);

let ok = true;
if ( only !== 'js' ) {
	copyFonts();
	ok = buildCss( modules ) && ok;
}
if ( watch ) {
	await startWatch( modules );
	runWebpack( modules );
} else {
	if ( only !== 'css' ) {
		ok = ( await runWebpack( modules ) ) && ok;
	}
	process.exitCode = ok ? 0 : 1;
}
