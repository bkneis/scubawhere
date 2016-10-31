<?php 

namespace Scubawhere\Repositories;

interface ObjectStoreRepoInterface
{
	public function uploadFile($file, $filename, $tmp_name, $tmp_dir, $dest_dir, $mime_type);
	
	public function uploadObject($file_path, $bucket, $file_name, $mime_type);

	public function getObject($bucket, $file);

	public function saveObject($bucket, $file, $save_path);

	public function getPreSignedObjectUrl($bucket, $file, $expiration);
}
