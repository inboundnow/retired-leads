/**
 * Leads API functions
 * @param  Object _inbound - Main JS object
 * @return Object - include event triggers
 */
var _inboundLeadsAPI = (function (_inbound) {
    var httpRequest;
    _inbound.LeadsAPI =  {
      init: function() {

      },
      getAllLeadData: function(expire_check) {
          var wp_lead_id = _inbound.Utils.readCookie("wp_lead_id"),
          old_data = _inbound.totalStorage('inbound_lead_data'),
          data = {
            action: 'inbound_get_all_lead_data',
            wp_lead_id: wp_lead_id,
          },
          success = function(returnData){
                    var obj = JSON.parse(returnData);
                    console.log('Got all the lead data check ');
                    setGlobalLeadVar(obj);
                    _inbound.totalStorage('inbound_lead_data', obj); // store lead data
          };

          if(!old_data) {
            console.log("No old data");
          }

          if (expire_check === 'true'){
            console.log("Session has not expired");
          }

          if(!old_data && expire_check === null) {
              _inbound.debug('Go to Database',function(){
                   console.log(expire_check);
                   console.log(old_data);
              });
              _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
              //_inbound.Utils.doAjax(data, success);
          } else {
              setGlobalLeadVar(old_data); // set global lead var with localstorage data
              var lead_data_expiration = _inbound.Utils.readCookie("lead_data_expiration");
              if (lead_data_expiration === null) {
                //_inbound.Utils.doAjax(data, success);
                _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
                console.log('localized data old. Pull new from DB');
              }
          }

      },
      getLeadLists: function() {
          var wp_lead_id = _inbound.Utils.readCookie("wp_lead_id");
          var data = {
                  action: 'wpl_check_lists',
                  wp_lead_id: wp_lead_id,
          };
          var success = function(user_id){
                    _inbound.Utils.createCookie("lead_session_list_check", true, { path: '/', expires: 1 });
                    console.log("Lists checked");
          };
          //_inbound.Utils.doAjax(data, success);
          _inbound.Utils.ajaxPost(inbound_settings.admin_url, data, success);
      }
    };

  return _inbound;

})(_inbound || {});