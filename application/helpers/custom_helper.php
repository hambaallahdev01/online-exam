<?php
/**
* This function used to generate the hashed password
* @param {string} $plainPassword : This is plain text password
*/
if(!function_exists('getHashedPassword'))
{
	function getHashedPassword($plainPassword)
	{
		return password_hash($plainPassword, PASSWORD_DEFAULT);
	}
}
/**
* This function used to generate the hashed password
* @param {string} $plainPassword : This is plain text password
* @param {string} $hashedPassword : This is hashed password
*/
if(!function_exists('verifyHashedPassword'))
{
	function verifyHashedPassword($plainPassword, $hashedPassword)
	{
		return password_verify($plainPassword, $hashedPassword) ? true : false;
	}
}

function load_view($views, $data = false)
{
	$ci =& get_instance();
	$ci->load->view('_header', $data);
	if (is_array($views)) {
		foreach ($views as $key => $view) {
			if ($key == 0) {
				$ci->load->view($view);
			}else{
				$ci->load->view($view);
			}
		}
	}else{
		$ci->load->view($views);
	}
	$data_footer['active_link'] = $data['active_link'];
	$ci->load->view('_footer', $data_footer);
}

function print_pre($value)
{
	echo "<pre>";
	print_r($value);
	die("</pre>");
}

function create_slug($table, $id_AI, $title_col, $title_val)
{
	$ci =& get_instance();
	$config = array(
		'table' => $table,
		'id' => $id_AI,
		'field' => 'slug',
		'title' => $title_col
	);
	$ci->load->library('slug', $config);
	$data_slug = array(
		'judul' => $title_val,
	);
	return $ci->slug->create_uri($data_slug);
}

function db_custom_err_msg()
{
	$ci =& get_instance();
	$err = $ci->db->error();
	$ret = '<b>Gagal! </b>';
	if ($err['code'] == 0) {
		$ret = false;
	}else{
		$ret .= $err['message'];
		log_message('error', print_r($err, true));
	}
	return $ret;
}

function get_obj($obj, $key, $number = true)
{
	if (isset($obj)) {
		if (isset($obj->{$key})) {
			return $obj->{$key};
		}else{
			if ($number) {
				return 0;
			}else{
				return false;
			}
		}
	}else{
		if ($number) {
			return 0;
		}else{
			return false;
		}
	}
}

function get_excerpt($str, $maxLength=150) {
	$str = strip_tags($str);
	$str = str_replace('&nbsp;', '', $str);
	$str = trim(preg_replace('/\s\s+/', ' ', $str));
	$length = strlen($str);
	$excerpt = $str;
	if ($length+3 > $maxLength) {
		$excerpt = substr($str, 0, $maxLength).'...';
	}        
	return $excerpt;
}

function _datatable_search($column_search, $column_order, $order = null)
{
	$ci =& get_instance();
	$i = 0;
	foreach ($column_search as $item){
		if($ci->input->post('search')['value']){
			if($i===0){
				$ci->db->group_start();
				$ci->db->like($item, $ci->input->post('search')['value']);
			}else{
				$ci->db->or_like($item, $ci->input->post('search')['value']);
			}
			if(count($column_search) - 1 == $i){
				$ci->db->group_end();
			}
		}
		$i++;
	}
	if($ci->input->post('order')){
		if ($ci->input->post('order')[0]['column'] == 0) {
			goto default_order;
		}
		$ci->db->order_by($column_order[$ci->input->post('order')[0]['column']],$ci->input->post('order')[0]['dir']);
	}else if(isset($order)){
		default_order:
		$ci->db->order_by(key($order), $order[key($order)]);
	}
}

function _datatable_get($get, $recordsTotal, $recordsFiltered)
{
	$ci =& get_instance();
	$get;
	if($ci->input->post('length') != -1){
		$ci->db->limit($ci->input->post('length'), $ci->input->post('start'));
	}
	$query = $ci->db->get();
	$return = new stdClass();
	$return->data = $query->result();
	$return->recordsTotal = $recordsTotal;
	$return->recordsFiltered = $recordsFiltered;
	return $return;
}

function _datatable_output($data, $recordsTotal, $recordsFiltered)
{
	$ci =& get_instance();
	$output = [
		'draw' => $ci->input->post('draw'),
		'recordsTotal' => $recordsTotal,
		'recordsFiltered' => $recordsFiltered,
		'data' => $data,
	];
	echo json_encode($output);
}

function clear_cache()
{
	$files = glob(FCPATH.'application'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'*');
	foreach($files as $file){
		if (strpos($file, 'index.html') !== false) {
			continue;
		}
		if(is_file($file)){
			unlink($file);
		}
	}
}

function reobject_value($array, $key_selected='slug', $value_selected='nilai', $slug=null)
{
	$new_array = new stdClass();
	foreach ($array as $key => $value) {
		if ($slug) {
			$new_array->{str_replace($slug.'_', '', $value->{$key_selected})} = $value->{$value_selected};
		}else{
			$new_array->{$value->{$key_selected}} = $value->{$value_selected};
		}
	}
	return $new_array;
}

function insert_csrf()
{
	$ci = &get_instance();
	$name = $ci->security->get_csrf_token_name();
	$hash = $ci->security->get_csrf_hash();
	return '<input type="hidden" name="'.$name.'" value="'.$hash.'" />';
}

function resize_image($image_data, $width=800, $height=800)
{
	$ci =& get_instance();
	$ci->load->library('image_lib');
	if ($image_data['image_width']>$width || $image_data['image_height']>$height) {
		$config =  array(
			'image_library'   => 'gd2',
			'source_image'    =>  $image_data['full_path'],
			'maintain_ratio'  =>  TRUE,
			'width'           =>  $width,
			'height'          =>  $height,
		);
		$ci->image_lib->clear();
		$ci->image_lib->initialize($config);
		$ci->image_lib->resize();
	}
}

function encode_url($string) {
	$ret = strtr($string, array('+' => '.', '=' => '-', '/' => '~'));
	return $ret;
}

function decode_url($string) {
	$string = strtr($string, array('.' => '+', '-' => '=', '~' => '/'));
	return $string;
}

function short_number_format($num) {
	if($num > 1000) {
		$x = round($num);
		$x_number_format = number_format($x);
		$x_array = explode(',', $x_number_format);
		$x_parts = array('K', 'M', 'B', 'T');
		$x_count_parts = count($x_array) - 1;
		$x_display = $x;
		$x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
		$x_display .= $x_parts[$x_count_parts - 1];
		return $x_display;
	}
	return $num;
}

function starts_with ($string, $startString) 
{ 
	$len = strlen($startString); 
	return (substr($string, 0, $len) === $startString); 
}

function file_get_contents_curl($url) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);   
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);   
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'); 

	$data = curl_exec($ch);
	curl_close($ch);

	return $data;
}

function format_rupiah($value=0)
{
	$angka = (float)$value;
	$hasil_rupiah = "Rp" . number_format($angka,2,',','.');
	return $hasil_rupiah;
}