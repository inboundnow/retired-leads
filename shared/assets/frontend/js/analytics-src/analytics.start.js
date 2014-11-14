/**
 * # Start
 *
 * Runs init functions and runs the domReady functions
 *
 * @author David Wells <david@inboundnow.com>
 * @version 0.0.1
 */

/* Initialize _inbound */
 _inbound.init();

/* Set Global Lead Data */
InboundLeadData = _inbound.totalStorage('inbound_lead_data') || null;

_inbound.Utils.domReady(window, function(){
    /* On Load Analytics Events */
    _inbound.DomLoaded();

});