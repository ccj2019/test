<?php
return array ( 0 => 'id', 1 => 'uid', 2 => 'bank_id', 3 => 'bank_num', 4 => 'bank_name', 5 => 'bank_name_cn', 6 => 'bank_address', 7 => 'withdraw_money', 8 => 'withdraw_status', 9 => 'withdraw_fee', 10 => 'add_time', 11 => 'add_ip', 12 => 'deal_time', 13 => 'deal_user', 14 => 'deal_info', 15 => 'second_fee', 16 => 'success_money', 17 => 'second_money', 18 => 'cfg_req_sn', '_autoinc' => true, '_pk' => 'id', '_type' => array ( 'id' => 'int(10) unsigned', 'uid' => 'int(10) unsigned', 'bank_id' => 'int(11)', 'bank_num' => 'varchar(30)', 'bank_name' => 'varchar(255)', 'bank_name_cn' => 'varchar(50)', 'bank_address' => 'varchar(255)', 'withdraw_money' => 'decimal(15,2)', 'withdraw_status' => 'tinyint(4)', 'withdraw_fee' => 'decimal(15,2)', 'add_time' => 'int(10) unsigned', 'add_ip' => 'varchar(16)', 'deal_time' => 'int(10) unsigned', 'deal_user' => 'varchar(50)', 'deal_info' => 'varchar(200)', 'second_fee' => 'decimal(15,2)', 'success_money' => 'decimal(15,2)', 'second_money' => 'decimal(15,2)', 'cfg_req_sn' => 'varchar(255)', ), ); ?>