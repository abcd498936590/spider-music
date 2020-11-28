<?php
	// res设置json
	header('Content-Type: application/json;charset=utf-8');

	// 请求
	function get_head($url){

		$user_agent = "Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_USERAGENT,$user_agent);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);

		curl_close($ch);
		return $result;

	}
	// 公用返回数据
	function create_res_data($arr1, $arr2){

		$data_list = [];
		foreach($arr1[1] as $key => $value){
			$item_json = [
				"name" => $arr2[1][$key],
				"open" => false,
				"id" => $arr1[1][$key]
			];
			array_push($data_list, $item_json);
		}
		$res_data = [
			"data" => $data_list
		];

		return $res_data;
	}

	$type = $_GET["type"];
	$host_url = "http://m.djye.com/";

	// 分类下的每一页数据 参数 type=tag_list&id=xxx&page=1
	if($type == "tag_list"){

		$tid = $_GET["id"];
		$q_page = $_GET["page"];
		$page = $q_page && $q_page != '0' ? "_0_".$q_page : "";

		$cur_url = $host_url."tag/".$tid.$page.".html";
		$cur_result = get_head($cur_url);

		preg_match_all("|player/([0-9]+)|is", $cur_result, $tag_id_list);
		preg_match_all("|<strong>([^<]+)<\/strong>|is", $cur_result, $tag_name_list);

		$create_data = create_res_data($tag_id_list, $tag_name_list);
		echo json_encode($create_data, true);

	}
	// 获取分类下的所有分页
	if($type == "tag_page"){

		$tid = $_GET["id"];
		$cur_url = $host_url."tag/".$tid.".html";
		$cur_result = get_head($cur_url);

		preg_match("|<li class=\"pagec\">第 [0-9]+ / ([0-9]+) 页<\/li>|is", $cur_result, $max_page_s);
		$max_page = (int)$max_page_s[1];

		$arr = [];
		for($i=1; $i<$max_page; $i++){
			$item_page = [
				"name" => "第".$i."页",
				"open" => false,
				"id" => $i
			];
			array_push($arr, $item_page);
		}
		$res_data = [
			"data" => $arr
		];
		echo json_encode($res_data, true);

	}
	// 获取所有的tag
	if($type == "get_tag"){

		$cur_url = $host_url."tag/";
		$cur_result = get_head($cur_url);

		preg_match_all("|<a href=\"\/tag\/([a-z]+)\.html\">|is", $cur_result, $tag_id_list);
		preg_match_all("|<a href=\"\/tag\/[a-z]+\.html\">([^<]+)</a|is", $cur_result, $tag_name_list);

		$create_data = create_res_data($tag_id_list, $tag_name_list);
		echo json_encode($create_data, true);

	}
	// 获取音乐地址
	if($type == "get_audio"){

		$pid = $_GET["id"];

		$cur_url = $host_url."player/".$pid.".htm";
		$cur_result = get_head($cur_url);

		preg_match("|<audio id=\"mplayer\" src=\"([^<]+)\">|is", $cur_result, $audio_url);

		$res_data = [
			"url" => $audio_url[1]
		];
		echo json_encode($res_data, true);
	}

	// $one_res = get_head($url);


?>