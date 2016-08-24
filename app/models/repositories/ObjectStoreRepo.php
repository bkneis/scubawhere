<?php namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use Aws\S3\S3Client;

class ObjectStoreRepo implements ObjectStoreRepoInterface
{
	protected $s3_client;

	public function __construct()
	{
		$this->s3_client = S3Client::factory(array(
            'credentials'   => array(
                'key'       => 'AKIAIDSABYCUP5PJ5IDQ',
                'secret'    => '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'
            ),
            'region'            => 'eu-central-1',
            'signatureVersion'  => 'v4'
		));
	}

	public function uploadFile($file, $filename, $tmp_dir, $dest_dir)
	{
		if(!file_exists($tmp_dir))
		{
			\File::makeDirectory($tmp_dir);
		}

		$file->move($tmp_dir, $filename);

		$key = Context::get()->name . $filename;
		$result = $this->s3_client->putObject(array(
				'Bucket'		=> $dest_dir,
				'Key'			=> $key,
				'Body'			=> $tmp_dir . $filename,
				'MetaData'		=> array(
					'account'	=> Context::get()->name
				)
		));

		$this->s3_client->waitUntil('ObjectExists', array(
				'Bucket'	=> $dest_dir,
				'Key'		=> $key
		)); 
        
		\File::delete($tmp_dir . $filename);

		return $this->s3_client->getObjectUrl($dest_dir, $key);
	}
}
