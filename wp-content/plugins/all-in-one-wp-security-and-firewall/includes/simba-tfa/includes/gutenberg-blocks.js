var registerBlockType = wp.blocks.registerBlockType;
var createElement = wp.element.createElement;
var serverSideRender = wp.serverSideRender;

registerBlockType('twofactor/user-settings', {
	title: tfa_trans.block_title,
	icon: 'lock',
	category: 'widgets',
	apiVersion: 3,
	edit: function (props) {
		return createElement(
			'div',
			null,
			createElement(serverSideRender, {
				block: 'twofactor/user-settings',
				attributes: props.attributes,
			} )
		);
	},
} );