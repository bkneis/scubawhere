<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use ScubaWhere\Helper;
use ScubaWhere\Context;

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
}