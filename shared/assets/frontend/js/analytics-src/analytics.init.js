/**
 * Init Inbound Analytics
 * - initializes analytics
 */

 var Lead_Globals = jQuery.totalStorage('inbound_lead_data') || null;
 function setGlobalLeadVar(retString){
     Lead_Globals = retString;
 }

 InboundAnalytics.init(); // run analytics

 /* run on ready */
 jQuery(document).ready(function($) {
   //record non conversion status
   var in_u = InboundAnalytics.Utils,
   wp_lead_uid = in_u.readCookie("wp_lead_uid"),
   wp_lead_id = in_u.readCookie("wp_lead_id"),
   expire_check = in_u.readCookie("lead_session_expire"); // check for session

   if (expire_check === null) {
      console.log('expired vistor. Run Processes');
     //var data_to_lookup = global-localized-vars;
     if (typeof (wp_lead_id) != "undefined" && wp_lead_id != null && wp_lead_id != "") {
         /* Get Lead_Globals */
         InboundAnalytics.LeadsAPI.getAllLeadData(expire_check);
         /* Lead list check */
         InboundAnalytics.LeadsAPI.getLeadLists();
       }
   }

 //window.addEventListener('load',function(){
 //    InboundAnalytics.LeadsAPI.attachSubmitEvent(window,InboundAnalytics.LeadsAPI.formSubmit);
 //}, false);

 in_u.contentLoaded(window, InboundAnalytics.LeadsAPI.attachFormSubmitEvent);

 /* Set Session Timeout */
 in_u.SetSessionTimeout();

 });