<?php namespace ScubaWhere\Repositories;

interface ObjectStoreRepoInterface
{
	public function uploadFile($file, $filename, $tmp_name, $tmp_dir, $dest_dir);
}
