<?php
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CrmTrackingController extends Controller {

	public function getScubaimage()
	{ 
        $data['campaign_id'] = Input::get('campaign_id');
        $data['customer_id'] = Input::get('customer_id');
        $data['token'] = Input::get('token');
        
        $token_match = ['campaign_id' => $data['campaign_id'], 'token' => $data['token']];
        $token = CrmToken::where($token_match)->first();

        if($token) {
            $token->opened += 1;
            $token->opened_time = time();
            $token->save();
        }
        
        $image_path = public_path() . '/img/scubawhere_logo.png';
        
        $response = Response::make(File::get($image_path));
        $response->header('Content-Type', 'image/png');
        return $response;
	}
    
    public function getLink()
    {
        $data['link_id'] = Input::get('link_id');
        $data['customer_id'] = Input::get('customer_id');
        $data['token'] = Input::get('token');
        
        $link_query = ['customer_id' => $data['customer_id'], 'token' => $data['token']];
        $tracker = CrmLinkTracker::where($link_query)->first();
        if($tracker)
        {
            $tracker->count = $tracker->count + 1;
            $tracker->opened_time = time();
            $tracker->save();
        }
        $link = CrmLink::findOrFail($data['link_id']);
        return Redirect::to($link->link);
    }
    
    public function getUnsubscribe()
    {
        $data['customer_id'] = Input::get('customer_id');
        $data['token'] = Input::get('token');
        $data['campaign_id'] = Input::get('campaign_id');
        
        $customer = Customer::find($data['customer_id'])->with('crmSubscription')->first();
        //return $customer;
        if((string)$data['token'] == $customer->crm_subscription->token)
        {
            $customer->crm_subscription->subscribed = 0;
            $customer->crm_subscription->unsubscribed_campaign_id = $data['campaign_id'];
            $customer->crm_subscription->save();
            return Redirect::to(public_path() . '/crm_unsubscribed');
        }
        else 
        {
            return "Sorry, you could not be unsubscribed due to a token mismatch"; // replace with error
        }
    }
    
}
