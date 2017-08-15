/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define([
            "jquery",
            "prototype"
        ], factory);
    } else {
        factory(jQuery);
    }
}(function (jQuery) {

window.Ccavenuepay = Class.create();
//Ccavenuepay = Class.create();
Ccavenuepay.prototype = {
	initialize: function(Ccavenue_token, license_key, module_version, module_name,checkmoduleupload_ajax_url,newmoduleupdatenow_ajax_url){
		this.Ccavenue_token 			= Ccavenue_token;
        this.license_key 				= license_key;
        this.module_version 			= module_version;
        this.module_name 				= module_name;
		this.checkmoduleupload_ajax_url = checkmoduleupload_ajax_url;
		this.newmoduleupdatenow_ajax_url= newmoduleupdatenow_ajax_url;
		$("update_module_button").setAttribute("onclick", "ccavenuepayobj.update_newmodule()");
	},
	check_update: function () {
		var status, response, result;
		var postdata		= 'task=check_module_upload&token='+this.Ccavenue_token+'&license_key='+this.license_key+'&module_version='+this.module_version+'&module_name='+this.module_name;
		response 			= this.postHttp(this.checkmoduleupload_ajax_url,postdata);
		var mydata 			= JSON.parse(response);	
		if(mydata.flage == 1)
		{
			$("new_module_version").insert(mydata.new_version);
			$("new_cat_ver").update(mydata.new_cat_ver);
			$("new_cms_ver").update(mydata.new_cms_ver);
			document.getElementById("ccavenue-panel-2").style.display = "block";
			document.getElementById("ccavenue_module_update_panel").style.display = "block";
			$(".ccavenue-panel-2").show();
		}
		else
		{
			$("ccavenue_module_update_panel").hide();
			$(".ccavenue-panel-2").hide();
		}
    },
	postHttp: function (url,data) {
        var response;
        new Ajax.Request(
            url,
            {
                method:    "post",
				parameters :data,
                onComplete:   function(transport) {response = transport.responseText;},
                asynchronous: false
            });
        return response;
    },
	update_newmodule: function ()
	{
		var status, response, result;
		var newmodule_version 	= $("new_module_version").innerHTML;
		var new_cms_ver			= $("new_cms_ver").innerHTML;
		var new_cat_ver			= $("new_cat_ver").innerHTML;
		var license_key		= $("ccavenue_module_lic_key").innerHTML;
		var module_version 		= $("ccavenue_module_ver").innerHTML;
		var postdata			= 'task=newmoduleupdate_now&token='+this.Ccavenue_token+'&license_key='+this.license_key+'&module_version='+this.module_version+'&module_name='+this.module_name+'&newmodule_version='+newmodule_version+'&new_cms_ver='+new_cms_ver+'&new_cat_ver='+new_cat_ver;
		response 				= this.postHttp(this.newmoduleupdatenow_ajax_url,postdata);
		var mydata 				= JSON.parse(response);
		if(mydata.status == true)
		{
			var file_path = mydata.file_path;
			document.getElementById("ccavenue_file_download_panel").style.display = "block";
			$("Download_file").setAttribute("href", file_path);
			alert("Status :"+mydata.massage);
		}
		else
		{
			alert("Status : Error -"+mydata.massage);
		}
	}
}
 

}));
