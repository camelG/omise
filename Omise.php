<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Omise {

    public function createCustomer($email, $desc = null, $card = null){
		if(empty($email)) return FALSE;

		$req = ['email' => $email,'desc' => $desc];

		if(!empty($card)) $req['card'] = $card;
		
		$result = OmiseCustomer::create($req);

		$data['customer_id'] = $result['id'];
		$data['card_id'] = @array_pop($result['cards']['data'])['id'];

		if(empty($data['customer_id']) || empty($data['card_id'])) return FALSE;

		return $data;
    }

	public function getByCustomer($cust_id){
		if(empty($cust_id)) return FALSE;
		$result = OmiseCustomer::retrieve($cust_id);
		
		$data['customer_id'] = $result['id'];
		$data['card_id'] = @array_pop($result['cards']['data'])['id'];
		
		if(empty($data['customer_id']) || empty($data['card_id'])) return FALSE;

		return $data;
	}

	public function updateByCustomer($cust_id, $email = null, $desc = null, $card = null){
		if(empty($cust_id)) return FALSE;

		if(!empty($email)) $req['email'] = $email;
		if(!empty($desc)) $req['desc'] = $desc;
		if(!empty($card)) $req['card'] = $card;
		$customer = OmiseCustomer::retrieve($cust_id);
		return $customer->update($req);
	}

	public function deleteByCustomer($cust_id){
		if(empty($cust_id)) return FALSE;

		$customer = OmiseCustomer::retrieve($cust_id);
		$customer->destroy();
		$customer->isDestroyed();
	}

	public function capture($price, $cust_id, $card){
		if(empty($price) || empty($cust_id) || empty($card)) return FALSE;
		
		$charge = OmiseCharge::create(array(
							'amount' => $price,
							'currency' => 'jpy',
							'customer' => $cust_id,
							'card' => $card,
							'capture' => 'true'
						));
		if($charge['status'] == 'successful'):
			$data['status'] = TRUE;
			$data['msg'] = $charge['failure_message'];
		else:
			$data['status'] = FALSE;
			$data['msg'] = $charge['failure_message'];
		endif;
		return $data;
	}
}