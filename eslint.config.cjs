/**
 * Lint config: WordPress's own rules, plus Floe's build aliases.
 * @floe/editor and @floe/components/* are resolved by scripts/build.mjs, and
 * @wordpress/* packages are provided by WordPress at runtime.
 */
const wpConfig = require( '@wordpress/scripts/config/eslint.config.cjs' );
const globals = require( 'globals' );

module.exports = [
	...wpConfig,
	{
		ignores: [ '**/assets/**', 'Assets/js/build/**', '.screenshots/**' ],
	},
	{
		languageOptions: {
			globals: { ...globals.browser },
		},
		rules: {
			'import/no-unresolved': [ 'error', { ignore: [ '^@floe/', '^@wordpress/' ] } ],
			'import/no-extraneous-dependencies': 'off',
		},
	},
	{
		files: [ 'scripts/**/*.mjs' ],
		languageOptions: {
			globals: { ...globals.node },
		},
		rules: {
			'no-console': 'off',
		},
	},
];
