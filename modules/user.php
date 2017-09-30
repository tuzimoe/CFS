<?php
//user.php 用户信息module

//user/userInfo 返回用户的基本信息
function user_userInfo() {
	$exp = [0, 6, 12, 20, 30, 43, 59, 79, 103, 131, 165, 204, 250, 302, 362, 430, 506, 591, 685, 789, 904, 1029, 1166, 1315, 1477, 1651, 1839, 2042, 2259, 2491, 2738, 3002, 3283, 3581, 3891, 4218, 4563, 4925, 5304, 5700, 6113, 6544, 6992, 7457, 7940, 8440, 8957, 9491, 10042, 10611, 11196, 11799, 12419, 13057, 13711, 14383, 15072, 15779, 16502, 17243, 18001, 18776, 19569, 20379, 21206, 22050, 22911, 23789, 24685, 25598, 26528, 27475, 28440, 29422, 30421, 31437, 32470, 33521, 34589, 35674, 36776, 37896, 39033, 40187, 41358, 42547, 43753, 44976, 46216, 47473, 48748, 50040, 51349, 52675, 54018, 55379, 56757, 58152, 59565, 60995, 63889, 66818, 69781, 72779, 75811, 78877, 81978, 85113, 88283, 91487, 94726, 97999, 101306, 104647, 108023, 111433, 114879, 118359, 121873, 125421, 129004, 132622, 136274, 139961, 143682, 147438, 151228, 155053, 158911, 162804, 166732, 170694, 174690, 178721, 182786, 186886, 191020, 195189, 199392, 203629, 207901, 212207, 216548, 220923, 225332, 229777, 234256, 238770, 243318, 247900, 252516, 257167, 261853, 266573, 271327, 276116, 280939, 285797, 290689, 295616, 300577, 305573, 310603, 315667, 320766, 325900, 331068, 336270, 341507, 346778, 352084, 357424, 362799, 368208, 373652, 379130, 384642, 390189, 395770, 401386, 407036, 412721, 418440, 424194, 429982, 435804, 441661, 447552, 453478, 459438, 465432, 471461, 477524, 483622, 489754, 495921, 502122, 508358, 514628, 520933, 527272, 533645, 540053, 546495, 552972, 559483, 566029, 572609, 579223, 585873, 592557, 599275, 606027, 612814, 619635, 626491, 633381, 640306, 647265, 654259, 661288, 668350, 675447, 682579, 689745, 696946, 704181, 711451, 718755, 726094, 733467, 740874, 748315, 755791, 763302, 770847, 778427, 786041, 793689, 801372, 809089, 816841, 824627, 832447, 840302, 848192, 856116, 864075, 872068, 880096, 888158, 896255, 904386, 912551, 920751, 928985, 937253, 945556, 953893, 962264, 970670, 979110, 987585, 996095, 1004639, 1013218, 1021831, 1030478, 1039160, 1047876, 1056627, 1065413, 1074233, 1083088, 1091977, 1100900, 1109858, 1118850, 1127876, 1136937, 1146032, 1155161, 1164325, 1173523, 1182756, 1192023, 1201325, 1210661, 1220032, 1229438, 1238878, 1248352, 1257861, 1267404, 1276982, 1286594, 1296240, 1305921, 1315636, 1325386, 1335171, 1345024, 1354877, 1364764, 1374686, 1384642, 1394633, 1404658, 1414718, 1424812, 1434941, 1445105, 1455302, 1465534, 1475801, 1486102, 1496438, 1506808, 1517213, 1527652, 1538126, 1548634, 1559176, 1569752, 1580364, 1591010, 1601690, 1612405, 1623154, 1633938, 1644756, 1655609, 1666496, 1677417, 1688373, 1699363, 1710388, 1721447, 1732541, 1743669, 1754832, 1766029, 1777261, 1788527, 1799827, 1811162, 1822531, 1833935, 1845373, 1856845, 1868352, 1879893, 1891469, 1903079, 1914724, 1926403, 1938117, 1949865, 1961647, 1973464, 1985315];
	global $user, $params, $uid;
	$ret = ['user' => [
		'user_id' => $uid,
		'invite_code' => (string)$uid,
		'name' => $user['name'],
		'introduction' => $user['introduction'],
		'level' => (int)$user['level'],
		'exp' => (int)$user['exp'],
		'previous_exp' => $exp[$user['level'] - 1],
		'next_exp' => $exp[$user['level']],
		'game_coin' => $params['item3'],
		'sns_coin' => $params['item4'],
		'free_sns_coin' => $params['item4'],
		'paid_sns_coin' => 0,
		'social_point' => $params['item2'],
		'unit_max' => 9000,
		'friend_max' => 999,
		'tutorial_state' => -1,
		'insert_date' => '2014-01-01 00:00:00',
		'update_date' => '2014-01-01 00:00:00',
		'unlock_random_live_muse' => 0,
		'unlock_random_live_aqours' => 0
	]];
	$energy = getCurrentEnergy();
	$ret['user'] = array_merge($ret['user'], $energy);
	return $ret;
}

//user/changeName 改名
function user_changeName($post) {
	global $user;
	$ret['before_name'] = '';
	$ret['after_name'] = $post['name'];
	$user['name'] = $post['name'];
	return $ret;
}


//user/showAllItem 返回单抽券和辅助券的数目
function user_showAllItem() {
	global $params;
	return json_decode('{
		"items": [{
				"item_id": 1,
				"amount": '.$params['item1'].'
		}, {
				"item_id": 5,
				"amount": '.$params['item5'].'
		}, {
				"item_id": 6,
				"amount": '.$params['item6'].'
		}, {
				"item_id": 7,
				"amount": '.$params['item7'].'
		}, {
				"item_id": 8,
				"amount": '.$params['item8'].'
		}, {
				"item_id": 9,
				"amount": '.$params['item9'].'
		}, {
				"item_id": 10,
				"amount": '.$params['item10'].'
		}, {
				"item_id": 11,
				"amount": '.$params['item11'].'
		}, {
				"item_id": 12,
				"amount": '.$params['item12'].'
		}, {
				"item_id": 13,
				"amount": '.$params['item13'].'
		}, {
				"item_id": 14,
				"amount": '.$params['item14'].'
		}]
}', true);
}

function user_getNavi() {
	global $uid, $params, $mysql;
	if (!$params['card_switch']) {
		$navi = 10;
	} else {
		$navi = isset($params['navi']) ? $params['navi'] : $mysql->query('SELECT center_unit FROM user_deck WHERE user_id=' . $uid)->fetchColumn();
	}
	return ['user' => [
		'user_id' => $uid,
		'unit_owning_user_id' => (int)$navi
	]];
}

function user_changeNavi($post) {
	global $params;
	$params['navi'] = $post['unit_owning_user_id'];
	return [];
}

?>