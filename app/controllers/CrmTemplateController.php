<?php

use Scubawhere\Context;
use Scubawhere\Entities\CrmTemplate;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class CrmTemplateController extends Controller {
    
    public function postAdd()
	{
		$data = Input::only(
			'html_string',
            'name'
		);

		$template = new CrmTemplate($data);

		if( !$template->validate() )
		{
			return Response::json( array('errors' => $template->errors()->all()), 406 ); // 406 Not Acceptable
		}

		$template = Context::get()->templates()->save($template);

		return Response::json( array('status' => '<b>OK</b> Email template has been saved'), 201 ); // 201 Created

	}
    
    public function getAll()
	{
		return Context::get()->templates()->get();
	}
    
    public function postUpdate()
	{
		$data = Input::only('html_string', 'template_id');
        
        $template = Context::get()->templates()->find($data['template_id']);
        $template->html_string = $data['html_string'];
        $template->save();
        if( !$template->validate() )
        {
            return Response::json( array('errors' => $template->errors()->all()), 406 ); // 406 Not Acceptable
        }
        
        return Response::json( array('status' => '<b>OK</b> Email template has been updated'), 201 ); // 201 Created 
    }

	public function postDelete()
	{
		$id = Input::get('id');
		if(is_null($id)) {
			throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The ID field is required']);
		}
		$template = CrmTemplate::onlyOwners()->findOrFail($id);
		$template->delete();
		return Response::json(array(
			'status' => 'Success. Email template deleted'
		));
	}
}