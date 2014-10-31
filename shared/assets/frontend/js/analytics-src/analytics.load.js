/**
 * Init Inbound Analytics
 * - initializes analytics
 */

 var Inbound_Add_Filter_Example = function( array ) {
  console.log('filter ran');
  var map = array || [];
  map.push('tehdhshs');
  return map;
 };
 var Inbound_Add_Action_Example = function(){ console.log('callback triggered')};
 _inbound.hooks.addAction( 'namespace.identifier', Inbound_Add_Action_Example, 10 );

function DOIT(){
  alert('DO IT');
}
_inbound.hooks.addAction( 'inbound_form_submission', DOIT, 10 );


 _inbound.init(); // analytics init

 var InboundLeadData = _inbound.totalStorage('inbound_lead_data') || null;
 function setGlobalLeadVar(retString){
     InboundLeadData = retString;
 }

 _inbound.Utils.domReady(window, function(){

    /* Filter Example */
    _inbound.hooks.addFilter( 'inbound.form_map_before', Inbound_Add_Filter_Example, 10 );
    /* On Load Analytics Events */
    _inbound.DomLoaded();
    /* Action Example */
    _inbound.hooks.doAction( 'namespace.identifier');


    var utils = _inbound.Utils,
    wp_lead_uid = utils.readCookie("wp_lead_uid"),
    wp_lead_id = utils.readCookie("wp_lead_id"),
    expire_check = utils.readCookie("lead_session_expire"); // check for session

    if (expire_check === null) {
       console.log('expired vistor. Run Processes');
      //var data_to_lookup = global-localized-vars;
      if (typeof (wp_lead_id) !== "undefined" && wp_lead_id !== null && wp_lead_id !== "") {
          /* Get InboundLeadData */
          _inbound.LeadsAPI.getAllLeadData(expire_check);
          /* Lead list check */
          _inbound.LeadsAPI.getLeadLists();
        }
    }

  /* Set Session Timeout */
  utils.SetSessionTimeout();

});



 function action_a( value ) {
  window.actionValue += 'a';
  console.log('page view action')
 }
 function action_b( value ) {
  window.actionValue += 'b';
  //alert('priority 2')
 }
 function action_c( value ) {
  window.actionValue += 'c';
  //alert('priority 8')
 }
 window.actionValue = '';

_inbound.hooks.addAction( 'inbound.page_view', action_a );
//_inbound.hooks.addAction( 'test.action', action_c, 8 );
//_inbound.hooks.addAction( 'test.action', action_b, 2 );



  //_inbound.hooks.removeAction( 'test.action' );

jQuery(document).ready(function($) {
     console.log('doing action');
     _inbound.hooks.doAction( 'test.action' );
     console.log(window.actionValue);

 });