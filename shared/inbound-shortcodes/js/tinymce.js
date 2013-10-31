(function() {

// Check Inbound Active Plugins
var indexOf = function(needle) {
    if(typeof Array.prototype.indexOf === 'function') {
        indexOf = Array.prototype.indexOf;
    } else {
        indexOf = function(needle) {
            var i = -1, index = -1;

            for(i = 0; i < this.length; i++) {
                if(this[i] === needle) {
                    index = i;
                    break;
                }
            }

            return index;
        };
    }

    return indexOf.call(this, needle);
};
var active_plugins = inbound_load.inbound_plugins,
    cta_check = 'cta',
    cta_status = indexOf.call(active_plugins, cta_check),
    lp_check = 'landing-pages',
    lp_status = indexOf.call(active_plugins, lp_check),
    leads_check = 'leads',
    leads_status = indexOf.call(active_plugins, leads_check);
// End Check Active Plugins
if (leads_status > -1) {
	console.log("leads on");
}
if (lp_status > -1) {
	console.log("lp on");
}
if (cta_status > -1) {
	console.log("cta on");
}

	tinymce.create('tinymce.plugins.InboundShortcodes', {

		init: function(ed, url) {
			ed.addCommand('InboundShortcodesPopup', function(a, params) {
				var popup = params.identifier;
				tb_show( inbound_load.pop_title, inbound_load.image_dir + 'popup.php?popup=' + popup + '&width=' + 900);
			});
		},
		createControl: function(btn, e) {
			if (btn == 'InboundShortcodesButton') {
				var a = this;

				// adds the tinymce button
				btn = e.createSplitButton('InboundShortcodesButton', {
					title: 'Insert Shortcode',
					image: inbound_load.image_dir + 'shortcodes-blue.png',
					icons: true
				});

				// adds the dropdown to the button
				btn.onRenderMenu.add(function(c, b) {
					b.add({title : 'Inbound Form Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					a.addWithPopup( b, 'Inbound Form Builder', 'forms' );
					a.addWithPopup( b, 'Quick Form Insert', 'quick-forms' );

					if (cta_status > -1) {
					//b.add({title : 'Call to Action Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					//a.addWithPopup( b, 'Insert Call to Action', 'call-to-action' ); // to to CTA
					//a.addWithPopup( b, 'Insert Call to Action', 'button' ); // to to CTA
					}
					if (lp_status > -1) {
					//b.add({title : 'Landing Page Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					//a.addWithPopup( b, 'Insert Landing Page Lists', 'landing_pages' );
					}
					//a.addWithPopup( b, 'Insert Button Shortcode',  'button' );
					//a.addWithPopup( b, 'Alert', 'alert' );
					//a.addWithPopup( b, 'Call Out', 'callout' );
					//b.add({title : 'Layout Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					//a.addWithPopup( b, 'Insert Columns', 'columns' );
					//a.addWithPopup( b, 'Content Box', 'content_box' );
					//a.addWithPopup( b, 'Divider', 'divider' );
					//a.addWithPopup( b, 'Tabs', 'tabs' );

					// Need forking
					//a.addWithPopup( b, 'Heading', 'heading' );
					//a.addWithPopup( b, 'Icon', 'icon' );
					//a.addWithPopup( b, 'Intro', 'intro' );
					//a.addWithPopup( b, 'Lead Paragraph', 'leadp' );
					//a.addWithPopup( b, 'List Icons', 'list_icons' );
					//a.addWithPopup( b, 'Map', 'gmap' );

					//a.addWithPopup( b, 'Pricing', 'pricing' );
					//a.addWithPopup( b, 'Profile', 'profile' );
					//a.addWithPopup( b, 'Social Links', 'social_links' );

					//a.addWithPopup( b, 'Teaser', 'teaser' );

					//a.addWithPopup( b, 'Video', 'video' );
				});

				return btn;
			}

			return null;
		},

		addWithPopup: function(ed, title, id) {
			ed.add({
				title: title,
				icon: 'editor-icon-' + id,
				onclick: function() {
					tinyMCE.activeEditor.execCommand('InboundShortcodesPopup', false, {
						title: title,
						identifier: id
					});
				}
			});
		},

		addImmediate: function(ed, title, sc) {
			ed.add({
				title: title,

				onclick: function() {
					tinyMCE.activeEditor.execCommand('mceInsertContent', false, sc);
				}
			});
		},

		getInfo: function() {
			return {
				longname: 'Inbound Shortcodes',
				author: 'David Wells',
				authorurl: 'http://www.inboundnow.com/',
				infourl: 'http://www.inboundnow.com/',
				version: '1.0'
			};
		}

	});

	tinymce.PluginManager.add('InboundShortcodes', tinymce.plugins.InboundShortcodes);

})();