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
		
		$save_path = storage_path() . '/scubawhere/' . Context::get()->name;
		$dest_path = 'sw-rms-terms';
		$filename = Context::get()->name . '/' . 'terms.pdf';
		$this->object_store_repo->uploadFile($file, $filename, $save_path, $dest_path);
	}

	public function uploadEmailImage($image)
	{
		if(!$image) throw new InvalidInputException('Please upload a file.');
				
		if(!$image->isValid()) throw new InvalidInputException('Uploaded file is not validate');

		$save_path = storage_path() . '/scubawhere/crm/images/' . Context::get()->name . '/';
		$storage_path = 'sw-rms-crm-photos';
		
        $o_filename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = $o_filename . str_random(20) . '.' . $image->getClientOriginalExtension();

		$url = $this->object_store_repo->uploadFile($image, $filename, $save_path, $storage_path);

		return $url;	
	}

}
