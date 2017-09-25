<?php

$query = "
        	SELECT u.ID as ID, u.display_name,u.user_email, 'manager' as role, s.trans_date, s.dash_price  
        	FROM wp_eot_users u 
        	LEFT JOIN wp_eot_usermeta um ON u.ID = um.user_id
                LEFT JOIN wp_subscriptions s ON u.ID = s.manager_id
        	WHERE um. meta_key = 'wp_eot_capabilities' 
        	AND um.meta_value LIKE '%\"manager\"%'
        ";

$managers = $wpdb->get_results($query, ARRAY_A);
d($managers);