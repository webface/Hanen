<?php
/**
  * Functions related to Stripe
**/
function refund_customer($charge_id, $part_amount = 0)
{
        try{
            if($part_amount == 0)
            {
            $refund = \Stripe\Refund::create(array("charge"=> $charge_id));
            }
            else 
            {
             $refund = \Stripe\Refund::create(array("charge"=> $charge_id , 'amount' => $part_amount));   
            }
            $success = 1;
        } catch (Exception $ex) {
                throw new Exception ("Refund: " . $ex->getMessage());
        }
        if($success == 1)
        {
            //error_log(print_r($refund));
            return true;
        }
        else 
        {
            return false;
        }
}
function create_new_customer ($cc_card, $email, $description) {
	try {
		$customer = \Stripe\Customer::create(array(
			"email" => $email,
			"description" => $description,
			"card" => $cc_card
		));		
	} catch (Exception $e) {
		throw new Exception ("Customer: " . $e->getMessage());
	}

	return array ('customer_id' => $customer->{'id'}, 'card_id' => $customer->{'default_card'});
}

function charge_customer ($price, $customer_id, $card, $description) {
	try {
		if (is_array($card)) {
			$cu = \Stripe\Customer::retrieve($customer_id);
//			$new_card = $cu->cards->create(array("card" => $card));
			$new_card = $cu->sources->create(array("source" => $card));
			$card = $new_card->{'id'};
			$cu->default_card = $card;
			$cu->save();
		}

		$charge = \Stripe\Charge::create(
			array(
				"amount" => $price * 100,
				"currency" => "usd",
				"customer" => $customer_id,
				"card" => $card,
				"description" => $description
			)
		);
	} catch (Exception $e) {
		throw new Exception ("Charge: " . $e->getMessage());
	}

	return $charge->{'id'};
}

function get_customer_cards ($cus_id) {

	try {
		$cu = \Stripe\Customer::retrieve($cus_id);
//		$cards = $cu->cards->all( array( 'limit' => 3 ) );
		$cards = $cu->sources->all( array( 'limit' => 3, 'object' => 'card' ) );
	} catch (Exception $e) {
		return '';
	}

	return $cards->{'data'};
}

?>