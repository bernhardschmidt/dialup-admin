<?php
if ($config[ldap_default_dn] != ''){
	include('../lib/ldap/attrmap.php3');
	$regular_profile_attr = $config[ldap_regular_profile_attr];
	$ds=@ldap_connect("$config[ldap_server]");  // must be a valid ldap server!
	if ($ds) {
       		$r=@ldap_bind($ds,"$config[ldap_binddn]",$config[ldap_bindpw]);
       		$sr=@ldap_search($ds,"$config[ldap_default_dn]", 'objectclass=*');
       		if ($info = @ldap_get_entries($ds, $sr)){
       			$dn = $info[0]['dn'];
       			if ($dn != ''){
               			foreach($attrmap as $key => $val){
					if ($info[0]["$val"][0] != '')
	                       			$default_vals["$key"] = $info[0]["$val"][0];
				}
			}
		}
		if ($regular_profile_attr != ''){
			$get_attrs = array("$regular_profile_attr");
			$sr=@ldap_search($ds,"$config[ldap_base]","uid=" . $login,$get_attrs);
			if ($info = @ldap_get_entries($ds,$sr)){
				$dn2 = $info[0][$regular_profile_attr][0];
				if ($dn2 != ''){
					$sr2=@ldap_search($ds,"$dn2",'objectclass=*');
					if ($info2 = @ldap_get_entries($ds,$sr2)){
						$dn3 = $info2[0]['dn'];
						if ($dn3 != ''){
							foreach($attrmap as $key => $val){
								if ($info2[0]["$val"][0] != '')
									$default_vals["$key"] = $info2[0]["$val"][0];
							}
						}
					}
				}
			}
		}
		@ldap_close($ds);
	}
}

?>
