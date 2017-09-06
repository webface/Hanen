<?php
/**
  * Functions related to Stripe
**/

function create_new_customer ($cc_card, $email, $description) {
        //error_log("Create new customer");
        //error_log(json_encode($cc_card));
	try {
		$customer = Stripe_Customer::create(array(
			"email" => $email,
			"description" => $description,
			"card" => $cc_card
		));		
	} catch (Exception $e) {
		throw new Exception ("Customer: " . $e->getMessage());
	}
        //error_log(json_encode(array ('customer_id' => $customer->{'id'}, 'card_id' => $customer->{'default_source'})));
	return array ('customer_id' => $customer->{'id'}, 'card_id' => $customer->{'default_source'});
}

function charge_customer ($price, $customer_id, $card, $description) {
        //error_log("Charge customer");
        //error_log(json_encode($card));
	try {
		if (is_array($card)) {
                    //error_log("charge customer with new card");
			$cu = Stripe_Customer::retrieve($customer_id);
//			$new_card = $cu->cards->create(array("card" => $card));
			$new_card = $cu->sources->create(array("source" => $card));
			$card = $new_card->{'id'};
			$cu->default_card = $card;
			$cu->save();
		}

		$charge = Stripe_Charge::create(
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
	include_once ('stripe/Stripe.php');
	Stripe::setApiKey(STRIPE_SECRET);

	try {
		$cu = Stripe_Customer::retrieve($cus_id);
//		$cards = $cu->cards->all( array( 'limit' => 3 ) );
		$cards = $cu->sources->all( array( 'limit' => 3, 'object' => 'card' ) );
	} catch (Exception $e) {
		return '';
	}

	return $cards->{'data'};
}

?>