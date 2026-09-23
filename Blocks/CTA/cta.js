import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import { createSimpleEdit } from '../_shared/simple';

registerBlockType( metadata.name, { edit: createSimpleEdit( 'cta' ), save: () => null } );
