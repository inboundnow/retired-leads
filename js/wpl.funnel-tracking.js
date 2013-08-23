
function wpl_numKeys(obj)
{
    var count = 0;
    for(var key in obj)
    {
		if (obj.hasOwnProperty(key)) {
 
			for(var key_b in obj[key])
			{		
				//alert ('1');
				count++;
			}
		
		}
      
    }
    return count;
}
/* Count number of session visits */
function countProperties(obj) {
    var count = 0;

    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            ++count;
    }

    return count;
}
/* build tracking uid */
function generate_wp_leads_uid(length) {
	
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

    if (! length) {
        length = Math.floor(Math.random() * chars.length);
    }

    var str = '';
    for (var i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}

/* build tracking uid */
function generate_session_id(length) {
    var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');

    if (! length) {
        length = Math.floor(Math.random() * chars.length);
    }

    var str = '';
    for (var i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}


/* Function for adding minutes to current time */
function addMinutes(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
}
/* Query String for utm params 

// Query String Stuff
    var p  = jQuery("pre"),
        result = "",
        urlParams = {};

    (function () {
        var e,
            d = function (s) { return decodeURIComponent(s).replace(/\+/g, " "); },
            q = window.location.search.substring(1),
            r = /([^&=]+)=?([^&]*)/g;

        while (e = r.exec(q)) {
            if (e[1].indexOf("[") == "-1")
                urlParams[d(e[1])] = d(e[2]);
            else {
                var b1 = e[1].indexOf("["),
                    aN = e[1].slice(b1+1, e[1].indexOf("]", b1)),
                    pN = d(e[1].slice(0, b1));
              
                if (typeof urlParams[pN] != "object")
                    urlParams[d(pN)] = {},
                    urlParams[d(pN)].length = 0;
                
                if (aN)
                    urlParams[d(pN)][d(aN)] = d(e[2]);
                else
                    Array.prototype.push.call(urlParams[d(pN)], d(e[2]));

            }
        }
    })();

    if (JSON) {
        result = JSON.stringify(urlParams, null, 4);

          for (var k in urlParams) {
                if (typeof urlParams[k] == "object") {
                  for (var k2 in urlParams[k])
                    jQuery.cookie(k2, urlParams[k][k2], { expires: 365 });
					console.log(k2);
					console.log(urlParams[k][k2]);
                } 
                else {
                    jQuery.cookie(k, urlParams[k], { expires: 365 }); }
					console.log(k);
					console.log(urlParams[k]);
              }

    }
 */   

//alert(window.location);

// Unique WP Lead ID
var wp_lead_uid_val =  generate_wp_leads_uid(35);
//alert(wplft.ip_address);
if(jQuery.cookie("wp_lead_uid") === null) { 
    jQuery.cookie("wp_lead_uid", wp_lead_uid_val, { path: '/', expires: 365 });
}

/* define vars */
var referrer = document.referrer;
var current_page =  window.location.href;
var current_page_parts = current_page.split('#');
current_page = current_page_parts[0];

var parts = location.hostname.split('.');
var subdomain = parts.shift();
var upperleveldomain = parts.join('.'); 
var data_block = jQuery.parseJSON(jQuery.cookie('user_data_json'));
// Date Data
var date = new Date();
var year = date.getUTCFullYear(); 
var month = date.getMonth(); 
var day = date.getDay(); 
var hour = date.getHours(); 
var minute = date.getMinutes();
var second = date.getSeconds(); 
var datetime = year+"-"+month+"-"+day+" "+hour+":"+minute+":"+second;
var the_time_out = addMinutes(date, .1);

var lead_uid = jQuery.cookie("wp_lead_uid");
var lead_id = jQuery.cookie("wp_lead_id");
var lead_email = jQuery.cookie("wp_lead_email");



/* Start LocalStorage */
var trackObj = jQuery.totalStorage('cpath');

if (typeof trackObj =='object' && trackObj)
{
	session_count = countProperties(trackObj);
	// If session is past timout limit
	if(!jQuery.cookie( "lead_session_expire") ) {
		var session_id = generate_session_id(11);
        console.log("Start New Tracking Session");
   		// Start New Tracking Block
   		trackObj.push({ 
			 session: session_count + 1,
			 session_id: session_id,
			 pageviews: [{id: 1, 
								current_page: current_page,
								post_id : wplft.post_id,
								timestamp: datetime,  
								referrer: referrer,  
								original_referrer: referrer 
								}],
				last_activity: date, // Last movement	
					  timeout: the_time_out, // Thirty minute timeout
					 lead_uid: lead_uid,	  
					  lead_id: lead_id,
				   lead_email: lead_email,
				   lead_ip_address: wplft.ip_address
				});
     } else {
	    // If session still active, do this
	    session_count = countProperties(trackObj);
	    number = parseInt(session_count) - 1;	
		var new_count = trackObj[number].pageviews.length;
		console.log(new_count);
		if(jQuery.cookie('wp_lead_uid')){
		    trackObj[number].lead_uid = lead_uid;
		}
		if(jQuery.cookie('wp_lead_id')){
		    trackObj[number].lead_id = lead_id;
		}
		if(jQuery.cookie('wp_lead_email')){
		    trackObj[number].lead_email = lead_email;
		}
		trackObj[number].pageviews.push(
			{ id : new_count+1,  current_page: current_page, post_id : wplft.post_id, timestamp: datetime, referrer: referrer}
		)
	}
} 
else
{	
	// Create initial tracking block
	var trackObj = new Array();
	var session_id = generate_session_id(11);
	trackObj.push({ 
					session: 1,
					session_id: session_id,
					pageviews: [{id: 1, 
								current_page: current_page,
								post_id : wplft.post_id,
								timestamp: datetime,  
								referrer: referrer,  
								original_referrer: referrer 
								}],	
				last_activity: date, // Last movement	
					timeout: the_time_out, // Thirty minute timeout
					lead_uid: lead_uid,	  
					lead_id: lead_id,
					lead_email: lead_email,
				    lead_ip_address: wplft.ip_address
				}
			);
	
}
jQuery.totalStorage('cpath', trackObj);

// Page View Object: Sessionless and clears on form submittions
var pageviewObj = jQuery.totalStorage('page_views');
if (typeof pageviewObj =='object' && pageviewObj)
{
	    // If pageviewObj exists, do this
	    number = 0;	
		var new_count = pageviewObj[number].pageviews.length;
		console.log(new_count);
		if(jQuery.cookie('wp_lead_uid')){
		    pageviewObj[number].lead_uid = lead_uid;
		}
		if(jQuery.cookie('wp_lead_id')){
		    pageviewObj[number].lead_id = lead_id;
		}
		if(jQuery.cookie('wp_lead_email')){
		    pageviewObj[number].lead_email = lead_email;
		}
		pageviewObj[number].pageviews.push(
			{ id : new_count+1,  current_page: current_page, post_id : wplft.post_id, timestamp: datetime, referrer: referrer}
		);
} else {	
// Create initial pageviewObj
	var pageviewObj = new Array();
	pageviewObj.push({ 
						source: 'Organic',
						pageviews: [{id: 1, 
									current_page: current_page,
									post_id : wplft.post_id,
									timestamp: datetime,  
									referrer: referrer,  
									original_referrer: referrer 
									}],
						lead_uid: lead_uid,	  
						lead_id: lead_id,
						lead_email: lead_email,
					    lead_ip_address: wplft.ip_address
				}
			);
	
}
jQuery.totalStorage('page_views', pageviewObj);
// console.log(JSON.stringify(pageviewObj[0].pageviews)) // output the pages viewed


/* End local storage */

/* Start Legacy Cookie Storage */
if (typeof data_block =='object' && data_block)
{
	var count = wpl_numKeys(data_block);
	data_block.items.push(
		{ id : count+1,  current_page: current_page, timestamp: datetime, referrer: referrer}
	);
	
	jQuery.cookie('user_data_json', JSON.stringify(data_block),  { expires: 1, path: '/' });
} 
else
{
	data_block = {items: [
		{id: '1', current_page: current_page,timestamp: datetime,  referrer: referrer,  original_referrer: referrer},
	]};
	
	jQuery.cookie('user_data_json', JSON.stringify(data_block), { expires: 1, path: '/' });
}
/* End Legacy Cookie Storage */