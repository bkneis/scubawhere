<?php namespace ScubaWhere\Services;

use ScubaWhere\Context; 
use ScubaWhere\Repositories\ObjectStoreRepoInterface;
use ScubaWhere\Exceptions\InvalidInputException;

class ObjectStoreService {

	protected $object_store_repo;

	public function __construct(ObjectStoreRepoInterface $obj_store_repo)
	{
		$this->object_store_repo = $obj_store_repo;
	}

	public function uploadTerms($file)
	{
		if(!$file) throw new InvalidInputException(array('Please upload a file.'));
		
		if(!$file->isValid()) throw new InvalidInputException(array('Uploaded file is not valid'));
		
		$save_path = storage_path() . '/scubawhere/' . Context::get()->name . '/';
		$dest_path = 'sw-rms-terms-conditions';
		$filename = Context::get()->name . '/' . 'terms.pdf';
		$tmp_name = 'terms.pdf';
		$this->object_store_repo->uploadFile($file, $filename, $tmp_name, $save_path, $dest_path, 'application/pdf');
	}

	public function uploadCustomerCSV($file_path)
	{
		if(!$file_path) throw new InvalidInputException(array('Please upload a file.'));

		return $this->object_store_repo->uploadObject($file_path, 'sw-rms-customer-imports', Context::get()->name . 'customer-imports.csv', 'text/csv');
	}

	public function uploadHeartbeatsLog()
	{
		$file_path = storage_path() . '/logs/heartbeats.log';
		return $this->object_store_repo->uploadObject($file_path, 'sw-rms-log', 'heartbeats.log', 'text/plain');
	}

	public function getHeartbeatsLogUrl()
	{
		return $this->object_store_repo->getPreSignedObjectUrl('sw-rms-log', 'heartbeats.log', '+5 minutes');
	}

	public function getCustomerCSVUrl()
	{
		return $this->object_store_repo->getPreSignedObjectUrl('sw-rms-customer-imports', Context::get()->name . 'customer-imports.csv', '+15 minutes');
	}

	public function getTermsUrl()
	{
		return $this->object_store_repo->getPreSignedObjectUrl('sw-rms-terms-conditions', Context::get()->name . '/terms.pdf', '+5 minutes');
	}

	public function uploadEmailImage($image)
	{
		if(!$image) throw new InvalidInputException('Please upload a file.');
				
		if(!$image->isValid()) throw new InvalidInputException('Uploaded file is not validate');

		$save_path = storage_path() . '/scubawhere/crm/images/' . Context::get()->name . '/';
		$storage_path = 'sw-rms-crm-images';
		
        $o_filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Context::get()->name . $o_filename . str_random(20) . '.' . $image->getClientOriginalExtension();

		$url = $this->object_store_repo->uploadFile($image, $filename, $filename, $save_path, $storage_path, 'image/jpeg');

		return $url;	
	}

}
