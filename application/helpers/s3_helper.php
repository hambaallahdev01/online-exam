<?php
function s3_putObject($file_name, $temp_file_location)
{
	try {
		$s3 = new Aws\S3\S3Client([
			'region'  => getenv('S3_REGION'),
			'endpoint'  => getenv('S3_ENDPOINT'),
			'version' => 'latest',
			'credentials' => [
				'key'    => getenv('S3_KEY'),
				'secret' => getenv('S3_SECRET'),
			]
		]);		

		$result = $s3->putObject([
			'Bucket' => getenv('S3_BUCKET'),
			'Key'    => $file_name,
			'SourceFile' => $temp_file_location			
		]);
		unlink($temp_file_location);
		return true;
	} catch (S3Exception $e) {
		return $e->getMessage();
	}
}

function s3_getObject($slug)
{
	$keyname = $slug;

	$s3 = new Aws\S3\S3Client([
		'version' => 'latest',
		'region'  => getenv('S3_REGION'),
		'endpoint'  => getenv('S3_ENDPOINT'),
		'credentials' => [
			'key'    => getenv('S3_KEY'),
			'secret' => getenv('S3_SECRET'),
		]
	]);

	try {
		$result = $s3->getObject([
			'Bucket' => getenv('S3_BUCKET'),
			'Key'    => $keyname
		]);
		header("Content-Type: {$result['ContentType']}");
		echo $result['Body'];
	} catch (Aws\S3\Exception\S3Exception $e) {
		show_404();
	}
}

function upload_image($path = '')
{
	$config['upload_path']          = sys_get_temp_dir();
	$config['allowed_types']        = 'gif|jpg|png|jpeg';
	$config['max_filename']         = 255;
	$config['max_size']             = 1024*5;
	$ci =& get_instance();
	$ci->load->library('upload', $config);

	if ( ! $ci->upload->do_upload('image')){
		$error = $ci->upload->display_errors('', '');
		return ['status' => false, 'data' => null, 'msg' => $error];
	} else {
		$data = $ci->upload->data();
		_resize_image($data);
		$file_name = _clean_path($path).'/'.$data['file_name'];
		$temp_file_location = $data['full_path'];
		s3_putObject($file_name, $temp_file_location);
		return ['status' => true, 'data' => 'files/'.$file_name, 'msg' => null];
	}
}

function _resize_image($image_data, $width=1024, $height=1024)
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

function _clean_path($path)
{
	$path = rtrim($path, '/');
	return preg_replace('~//+~', '/', $path);
}