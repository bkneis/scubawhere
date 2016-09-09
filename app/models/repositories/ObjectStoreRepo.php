<?php namespace ScubaWhere\Repositories;

use ScubaWhere\Context;
use ScubaWhere\Exceptions;
use Aws\S3\S3Client;
use Guzzle\Http\EntityBody;

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

	public function uploadFile($file, $filename, $tmp_name, $tmp_dir, $dest_dir)
	{
		if(!file_exists($tmp_dir))
		{
			\File::makeDirectory($tmp_dir);
		}

		$file->move($tmp_dir, $filename);

		$result = $this->s3_client->putObject(array(
				'Bucket'		=> $dest_dir,
				'Key'			=> $filename,
				'Body'			=> EntityBody::factory(fopen($tmp_dir . $tmp_name, 'r')),
				'Content'		=> 'application/pdf',
				'MetaData'		=> array(
					'account'	=> Context::get()->name
				)
		));

		$this->s3_client->waitUntil('ObjectExists', array(
				'Bucket'	=> $dest_dir,
				'Key'		=> $filename
		)); 
        
		\File::delete($tmp_dir . $filename);

		return $this->s3_client->getObjectUrl($dest_dir, $filename);
	}
}
