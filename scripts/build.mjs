import { execFileSync } from 'node:child_process';
import { existsSync, mkdirSync, readdirSync, readFileSync } from 'node:fs';
import { join } from 'node:path';

const root = process.cwd();
const blocks = join(root, 'Blocks');
const cssOnly = process.argv.includes('--css');
const runJs = !cssOnly;
// wp-scripts clears each block's Assets directory, so every JS build must
// restore its CSS afterwards. --css remains a CSS-only shortcut.
const runCss = true;
const binary = (name) => join(root, 'node_modules', '.bin', name);

const blockFolders = readdirSync(blocks, { withFileTypes: true }).filter((folder) => {
	return folder.isDirectory() && !folder.name.startsWith('_') && existsSync(join(blocks, folder.name, 'block.json'));
});

for (const folder of blockFolders) {
	const dir = join(blocks, folder.name);
	const metadataPath = join(dir, 'block.json');
	const metadata = JSON.parse(readFileSync(metadataPath, 'utf8'));
	const slug = metadata.name.split('/')[1];
	const output = join(dir, 'Assets');
	mkdirSync(output, { recursive: true });
	if (runJs && existsSync(join(dir, slug + '.js'))) {
		console.log('Building JS:', slug);
		execFileSync(binary('wp-scripts'), ['build', join('Blocks', folder.name, slug + '.js'), '--output-path=' + join('Blocks', folder.name, 'Assets')], { stdio: 'inherit', cwd: root });
	}
}

for (const folder of blockFolders) {
	const dir = join(blocks, folder.name);
	const metadata = JSON.parse(readFileSync(join(dir, 'block.json'), 'utf8'));
	const slug = metadata.name.split('/')[1];
	const output = join(dir, 'Assets');
	mkdirSync(output, { recursive: true });
	if (runCss && existsSync(join(dir, slug + '.scss'))) {
		console.log('Building CSS:', slug);
		execFileSync(binary('sass'), ['--no-source-map', '--no-charset', '--style=compressed', join(dir, slug + '.scss'), join(output, slug + '.css')], { stdio: 'inherit', cwd: root });
	}
}

if (runCss) {
	const output = join(blocks, '_shared', 'Assets');
	mkdirSync(output, { recursive: true });
	execFileSync(binary('sass'), ['--no-source-map', '--no-charset', '--style=compressed', join(blocks, '_shared', 'sections.scss'), join(output, 'sections.css')], { stdio: 'inherit', cwd: root });
}
