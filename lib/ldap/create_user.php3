<?php
	$ds = @ldap_connect($config[ldap_server]);
	if ($ds){
		$r = @ldap_bind($ds,"$config[ldap_binddn]",$config[ldap_bindpw]);
		if ($r){
			list ($givenname,$sn) = split(' ',$cn,3);
			$dn = 'uid=' . $login . ',' . $config[ldap_default_new_entry_suffix];
			$new_user_entry["objectclass"][0]="top";
			$new_user_entry["objectclass"][1]="person";
			$new_user_entry["objectclass"][2]="organizationalPerson";
			$new_user_entry["objectclass"][3]="inetOrgPerson";
			$new_user_entry["objectclass"][4]="radiusprofile";
			$new_user_entry["cn"]="$cn";
			$new_user_entry["sn"]="$sn";
			$new_user_entry["givenname"]="$givenname";
			$new_user_entry["mail"]="$mail";
			$new_user_entry["telephonenumber"]="$telephonenumber";
			$new_user_entry["homephone"]="$homephone";
			$new_user_entry["mobile"]="$mobile";
			$new_user_entry["ou"]="$ou";
			$new_user_entry["uid"]="$login";
			if (is_file("../lib/crypt/$config[general_encryption_method].php3")){
				include("../lib/crypt/$config[general_encryption_method].php3");
				$passwd = da_encrypt($passwd);
		$new_user_entry[$attrmap['User-Password']] = '{' . "$config[general_encryption_method]" . '}' . $passwd;
			}
			else{
				echo "<b>Could not open encryption library file.Password will be clear text.</b><br>\n";
				$new_user_entry[$attrmap['User-Password']]="{clear}" . $passwd;
			}

			print_r($new_user_entry);

			@ldap_add($ds,$dn,$new_user_entry);

			foreach($show_attrs as $key => $attr){
				if ($attrmap["$key"] == 'none')
					continue;
//
//	if value is the same as the default and the corresponding attribute in ldap does not exist or
//	the value is the same as that in ldap then continue
//
	        		if ( $$attrmap["$key"] == $default_vals["$key"])
	                		continue;
				if ( $$attrmap["$key"] == '')
					continue;
				$mod[$attrmap["$key"]] = $$attrmap["$key"];

				@ldap_mod_add($ds,$dn,$mod);
			}
		}
		if (@ldap_error($ds) == 'Success')
			echo "<b>User was added in user database</b><br>\n";
		else
			echo "<b>LDAP ERROR: " . ldap_error($ds) . "</b><br>\n";
		@ldap_close($ds);
	}
?>
