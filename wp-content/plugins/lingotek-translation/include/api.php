<?php

require_once('http.php');

/**
 * manages communication with Lingotek TMS
 * uses Lingotek API V5
 *
 * @since 0.1
 */
class Lingotek_API extends Lingotek_HTTP {
	protected $base_url;
	protected $api_url;
	protected $client_id;
	private $auth_temp;

	const PRODUCTION_URL = "https://myaccount.lingotek.com";
	const SANDBOX_URL = "https://cms.lingotek.com";
	const CLIENT_ID = "780966c9-f9c8-4691-96e2-c0aaf47f62ff";// Lingotek App ID


	private $bridge_url = '';

	/**
	 * constructor
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$base_url = get_option('lingotek_base_url');
		$this->base_url = $base_url ? $base_url : self::PRODUCTION_URL;
		$this->api_url = $this->base_url.'/api';
		$this->client_id = self::CLIENT_ID;
		$token_details = get_option('lingotek_token');
		$this->headers['Authorization'] = 'bearer ' . $token_details['access_token'];
		$this->defaults = get_option('lingotek_defaults');

		$this->bridge_url = BRIDGE_URL . '/api/v2/transaction/translation/';
	}

	public function get_token_details($access_token) {
		$url = $this->base_url . "/auth/oauth2/access_token_info?access_token=" . $access_token;
		Lingotek::log("GET " . $url . " (" . __METHOD__ . ")");
		$response = wp_remote_get($url);
		$response_code = wp_remote_retrieve_response_code($response);

		if ($response_code == 200) {
			$response_body = json_decode(wp_remote_retrieve_body($response));
			$token_details = $response_body;
		}
		else {
			$token_details = FALSE;
		}
		return $token_details;
	}

	public function get_api_url() {
		return $this->api_url;
	}

	/**
	 * updates the projet callback
	 *
	 * @since 0.2
	 *
	 * @param string $project_id
	 */
	public function update_callback_url($project_id) {
		$args = array('callback_url' => add_query_arg('lingotek', 1, site_url()));
		$response = $this->patch($this->api_url . '/project/' . $project_id, $args);
		return !is_wp_error($response) && 204 == wp_remote_retrieve_response_code($response);
	}

  /**
	 * creates a new project
	 *
	 * @since 0.2
	 *
	 * @param string $title
	 */
	public function create_project($title, $community_id) {
		$args = array(
			'title' => $title,
			'community_id' => $community_id,
			'workflow_id' => $this->defaults['workflow_id'],
			'callback_url' => add_query_arg('lingotek', 1, site_url()),
		);

		$response = $this->post($this->api_url . '/project', $args);
		if(!is_wp_error($response) && 201 == wp_remote_retrieve_response_code($response)) {
			$new_id = json_decode(wp_remote_retrieve_body($response));
			return $new_id->properties->id;
		}
		else {
			return false;
		}
	}

	/**
	 * uploads a document
	 *
	 * @since 0.1
	 *
	 * @param array $args expects array with title, content and locale_code
	 * @returns bool|string document_id, false if something got wrong
	 */
	public function upload_document($args, $wp_id = null) {
		$args = wp_parse_args($args, array('format' => 'JSON', 'project_id' => $this->defaults['project_id'], 'workflow_id' => $this->defaults['workflow_id']));
		$this->format_as_multipart($args);
		$response = $this->post($this->api_url . '/document', $args);

		if ($wp_id){
			$arr = get_option('lingotek_log_errors');

			if (202 == wp_remote_retrieve_response_code($response)) {
				if (isset($arr[$wp_id])) {
					unset($arr[$wp_id]);
				}
			}
			else if (is_wp_error($response)) {
				$arr[$wp_id]['wp_error'] = __('Make sure you have internet connectivity', 'lingotek-translation');
			}
			else if (400 == wp_remote_retrieve_response_code($response)) {
				$arr[$wp_id]['upload_document'] = sprintf(
					__('There was an error uploading WordPress item %1$s', 'lingotek-translation'), $wp_id
				);
			}
			update_option('lingotek_log_errors', $arr);
		}

		if (!is_wp_error($response) && 202 == wp_remote_retrieve_response_code($response)) {
			$b = json_decode(wp_remote_retrieve_body($response));
			return $b->properties->id;
		}
		return false;
	}

	/**
	 * modifies a document
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @param array $args expects array with content
	 * @return bool false if something got wrong
	 */
	public function patch_document($id, $args, $wp_id = null) {
		$args = wp_parse_args($args, array('format' => 'JSON'));
		$this->format_as_multipart($args);
		$response = $this->patch($this->api_url . '/document/' . $id, $args);

		if ($wp_id) {
			$arr = get_option('lingotek_log_errors');

			if (202 == wp_remote_retrieve_response_code($response)) {
				if (isset($arr[$wp_id])) {
					unset($arr[$wp_id]);
				}
			}
			else if (is_wp_error($response)) {
				$arr[$wp_id]['wp_error'] = __('Make sure you have internet connectivity', 'lingotek-translation');
			}
			else if (400 == wp_remote_retrieve_response_code($response) || 404 == wp_remote_retrieve_response_code($response)) {
				$arr[$wp_id]['patch_document'] = sprintf(
					__('There was an error updating WordPress item %1$s', 'lingotek-translation') ,$wp_id
				);
			}
			update_option('lingotek_log_errors', $arr);
		}

		return !is_wp_error($response) && 202 == wp_remote_retrieve_response_code($response);
	}

	/**
	 * deletes a document
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 */
	public function delete_document($id, $wp_id = null) {
		$response = $this->delete($this->api_url . '/document/' . $id);

		if ($wp_id) {
			$arr = get_option('lingotek_log_errors');
			if (isset($arr[$wp_id])) {
				unset($arr[$wp_id]);
				update_option('lingotek_log_errors', $arr);
			}
		}

		return !is_wp_error($response) && (204 == wp_remote_retrieve_response_code($response) || 202 == wp_remote_retrieve_response_code($response));
	}


	/**
	 * get all documents ids
	 *
	 * @since 0.1
	 */
	public function get_document_ids($args = array()) {
		$response = $this->get(add_query_arg($args, $this->api_url . '/document'));
		$ids = array();

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$documents = json_decode(wp_remote_retrieve_body($response));
			foreach ($documents->entities as $doc) {
				$ids[] = $doc->properties->id;
			}
		}

		return $ids;
	}

	public function get_document_count($args = array()) {
		$response = $this->get(add_query_arg($args, $this->api_url . '/document'));
		$docs = array();

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$response = json_decode(wp_remote_retrieve_body($response));
			return $response->properties->total;
		}

		return null;
	}

	/**
	 * get all documents
	 *
	 * @since 0.1
	 */
	public function get_documents($args = array()) {
		$response = $this->get(add_query_arg($args, $this->api_url . '/document'));
		$docs = array();

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$documents = json_decode(wp_remote_retrieve_body($response));
		foreach ($documents->entities as $doc) {
				$docs[] = $doc;
			}
		}

		return $docs;
	}

	/**
	 * get document by id
	 *
	 * @since 0.1
	 */
	public function get_document($doc_id) {
		$response = $this->get($this->api_url . '/document/' . $doc_id);

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$document = json_decode(wp_remote_retrieve_body($response));
		}

		return $document;
	}

	public function get_document_status($doc_id)
	{
		$imported = FALSE;
		$response = $this->get($this->api_url . '/document/' . $doc_id . '/status');

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$imported = TRUE;
		}

		return $imported;
	}


	/**
	 * get specific document content
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @return string
	 */
	public function get_document_content($doc_id) {
		$response = $this->get($this->api_url . '/document/' . $doc_id . '/content');

		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$content = wp_remote_retrieve_body( $response );
		}
		return $content;
	}

	/**
	 * check translations status of a document
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @return array with locale as key and status as value
	 */
	public function get_translations_status($doc_id, $wp_id = null) {
		$response = $this->get($this->api_url . '/document/' . $doc_id . '/translation');
		if (!is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response)) {
			$b = json_decode(wp_remote_retrieve_body($response));
			foreach ($b->entities as $e) {
				$translations[$e->properties->locale_code] = $e->properties->percent_complete;
			}
		}

		if($wp_id) {
			$arr = get_option('lingotek_log_errors');

			if (200 == wp_remote_retrieve_response_code($response)) {
				if (isset($arr[$wp_id])) {
					unset($arr[$wp_id]);
				}
			}
			else if (is_wp_error($response)) {
				$arr[$wp_id]['wp_error'] = __('Make sure you have internet connectivity', 'lingotek-translation');
			}
			else if (400 == wp_remote_retrieve_response_code($response)) {
				$arr[$wp_id]['get_translations_status'] = sprintf(
					__('There was an error updating the translations status for WordPress item %1$s', 'lingotek-translation'), $wp_id
				);
			}
			update_option('lingotek_log_errors', $arr);
		}

		return empty($translations) ? array() : $translations;
	}


	public function get_language_mappings()
	{
		$url = 'https://gmc.lingotek.com/v1/locales';
		$response = $this->get($url);
		return json_decode(wp_remote_retrieve_body($response),true);
	}

	/**
	* Makes an API call to bridge to get the estimated cost for translating a particular document professionally. 
	*
	* @param string $lingotek_auth the auth token to make an API call to bridge.
	* @param string $document_id the id of the specific document. 
	* @param string $locale the locale of the document being requested.
	*/
	public function get_cost_estimate($lingotek_auth, $document_id, $locale) {
		$this->start_bridge_call();

		$args = array(
			'document_id' => $document_id,
			'locale' => $locale
		);
		$response = $this->get($this->bridge_url . 'estimate', $args);

		$success = 200 === wp_remote_retrieve_response_code($response);

		$this->end_bridge_call();
		return array('success' => $success, 'data' => $this->get_response_body_from_bridge($response));
	}

	/**
	* Makes an API call to bridge request translation for a document using the professional workflow. 
	*
	* @param string $document_id the id of the specific document. 
	* @param string $locale the locale of the document being requested.
	* @param string $workflow_id the id used to process the document.
	*/
	public function request_professional_translation($document_id, $locale, $workflow_id) {
		$this->start_bridge_call();
		
		$args = array(
			'document_id' => $document_id,
			'locale' => $locale,
			'workflow_id' => $workflow_id,
		);
		$response = $this->post($this->bridge_url . 'request', $args);
		$success = 200 === wp_remote_retrieve_response_code($response);

		$this->end_bridge_call();
		return array('success' => $success, 'data' => $this->get_response_body_from_bridge($response));
	}

	/**
	* Makes an API call to bridge request bulk translations for a document using the professional workflow. 
	*
	* @param string $document_id the id of the specific document. 
	* @param string $locale the locale of the document being requested.
	* @param string $workflow_id the id used to process the document.
	*/
	public function request_professional_translation_bulk($workflow_id, $translations, $total_estimate, $summary) {
		$this->start_bridge_call();
		
		$args = array(
			'workflow_id' => $workflow_id,
			'translations' => $translations,
			'total_estimate' => $total_estimate,
			'summary' => $summary
		);
		$response = $this->post($this->bridge_url . 'request/bulk', $args, 60);
		$success = 200 === wp_remote_retrieve_response_code($response);

		$this->end_bridge_call();
		return array('data' => $this->get_response_body_from_bridge($response));
	}


	public function get_lingotek_terms_and_conditions() {
		$this->start_bridge_call();
		$response = $this->get(BRIDGE_URL . '/api/v2/transaction/terms/');
		$success = 200 === wp_remote_retrieve_response_code($response);
		$this->end_bridge_call();
		return array('success' => $success, 'data' => $this->get_response_body_from_bridge($response));
	}

	/**
	* Makes an API call to bridge to get the payment information about the user.
	*/
	public function get_user_payment_information() {
		$this->start_bridge_call();

		$response = $this->get(BRIDGE_URL . '/api/v2/transaction/payment');
		$success = 200 === wp_remote_retrieve_response_code($response);

		$this->end_bridge_call();
		return array('success' => $success, 'payment_info' => $this->get_response_body_from_bridge($response));
	}

	/**
	* Helper function to retrieve the response body from bridge.
	*
	* @param array $response the response from bridge.
	*/
	private function get_response_body_from_bridge($response)
	{
		$body = json_decode(wp_remote_retrieve_body($response),true);
		return isset($body) ? $body : wp_remote_retrieve_body($response);
	}

	/**
	 * requests a new translation of a document
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @param string $locale Lingotek locale
	 * @param array $args optional arguments (only workflow_id at the moment)
	 * @return bool true if the request succeeded
	 */
	public function request_translation($id, $locale, $args = array(), $wp_id = null) {
		$locale = Lingotek::map_to_lingotek_locale($locale);
		$args = wp_parse_args($args, array('workflow_id' => $this->defaults['workflow_id']));
		$args = array_merge(array('locale_code' => $locale), $args);
		$response = $this->post($this->api_url . '/document/' . $id . '/translation', $args);

		if ($wp_id) {
			$arr = get_option('lingotek_log_errors');

			if (201 == wp_remote_retrieve_response_code($response)) {
				if (isset($arr[$wp_id])) {
					unset($arr[$wp_id]['wp_error']);
					unset($arr[$wp_id]['request_translation'][$locale]);
					if (empty($arr[$wp_id])){
						unset($arr[$wp_id]);
					}
				}
			}
			else if (is_wp_error($response)) {
				$arr[$wp_id]['wp_error'] = __('Make sure you have internet connectivity', 'lingotek-translation');
			}
			else if (400 == wp_remote_retrieve_response_code($response) || 404 == wp_remote_retrieve_response_code($response)) {
				$arr[$wp_id]['request_translation'][$locale] = sprintf(
					__('There was an error requesting translation %1$s for WordPress item %2$s', 'lingotek-translation'), $locale, $wp_id
				);
			}
			update_option('lingotek_log_errors', $arr);
		}

		return !is_wp_error($response) && 201 == wp_remote_retrieve_response_code($response);
	}

	/**
	 * get a translation
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @param string $locale Lingotek locale
	 * @return string|bool the translation, false if there is none
	 */
	public function get_translation($doc_id, $locale, $wp_id = null) {
		$locale = Lingotek::map_to_lingotek_locale($locale);
		$statuses = $this->get_translations_status($doc_id);
		if (isset($statuses[$locale]) && $statuses[$locale] != 100) {
			return false;
		} 
		$response = $this->get(add_query_arg(array('locale_code' => $locale, 'auto_format' => 'true') , $this->api_url . '/document/' . $doc_id . '/content'));

		if ($wp_id) {
			$arr = get_option('lingotek_log_errors');

			if (200 == wp_remote_retrieve_response_code($response)) {
				if (isset($arr[$wp_id])) {
					unset($arr[$wp_id]['wp_error']);
					unset($arr[$wp_id]['get_translation'][$locale]);
					if (empty($arr[$wp_id])) {
						unset($arr[$wp_id]);
					}
				}
			}
			else if (is_wp_error($response)) {
				$arr[$wp_id]['wp_error'] = __('Make sure you have internet connectivity', 'lingotek-translation');
			}
			else if (400 == wp_remote_retrieve_response_code($response) || 404 == wp_remote_retrieve_response_code($response)) {
				$arr[$wp_id]['get_translation'][$locale] = sprintf(
					__('There was an error downloading translation %1$s for WordPress item %2$s'), $locale, $wp_id
				);
			}
			update_option('lingotek_log_errors', $arr);
		}

		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? wp_remote_retrieve_body($response) : false;
	}

	/**
	 * deletes a translation
	 *
	 * @since 0.1
	 *
	 * @param string $id document id
	 * @param string $locale Lingotek locale
	 */
	public function delete_translation($id, $locale, $wp_id = null) {
		$response = $this->delete($this->api_url . '/document/' . $id . '/translation/' . $locale);

		if ($wp_id) {
			$arr = get_option('lingotek_log_errors');
			if (isset($arr[$wp_id])) {
				unset($arr[$wp_id]);
				update_option('lingotek_log_errors', $arr);
			}
		}
		// FIXME send a response
	}

	/**
	 * get connect account url
	 *
	 * @param string $redirect_uri the location where to redirect to after account has been connected
	 * @return string the complete url for the connect account link
	 */
	public function get_connect_url($redirect_uri, $env = NULL) {
		$base_url = $this->base_url;
		$client_id = $this->client_id;
		if(!is_null($env)){
			$base_url = (strcasecmp($env,'SANDBOX') == 0) ? self::SANDBOX_URL : self::PRODUCTION_URL;
		}
		return $base_url . "/auth/authorize.html?client_id=" . $client_id . "&redirect_uri=" . urlencode($redirect_uri) . "&response_type=token";
	}

	public function get_new_url($redirect_uri) {
		$base_url = self::PRODUCTION_URL;
		$client_id = $this->client_id;
		return $base_url . "/lingopoint/portal/requestAccount.action?client_id=" . $client_id . "&app=" . urlencode($redirect_uri) . "&response_type=token";

	}

	public function get_communities() {
		$response = $this->get(add_query_arg(array('limit' => 1000), $this->api_url . '/community'));
		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response)) : false;
	}

	public function get_projects($community_id) {
		$response = $this->get(add_query_arg(array('community_id' => $community_id, 'limit' => 1000), $this->api_url . '/project'));
		if (wp_remote_retrieve_response_code($response) == 204) {
			return array();// there are currently no projects
		}
		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response)) : false;
	}

	public function get_vaults($community_id) {
		$response = $this->get(add_query_arg(array('community_id' => $community_id, 'limit' => 1000), $this->api_url . '/vault'));
		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response)) : false;
	}

	public function get_workflows($community_id) {
		$response = $this->get(add_query_arg(array('community_id' => $community_id, 'limit' => 1000), $this->api_url . '/workflow'));
		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response)) : false;
	}

	public function get_filters() {
		$response = $this->get(add_query_arg(array('limit' => 1000), $this->api_url . '/filter'));
		return !is_wp_error($response) && 200 == wp_remote_retrieve_response_code($response) ? json_decode(wp_remote_retrieve_body($response)) : false;
	}

	public function upload_filter($name, $type, $content) {
		$args = array('name' => $name, 'type' => $type, 'content' => $content);
		$response = $this->post($this->api_url . '/filter', $args);
	}

	private function start_bridge_call() {
		$this->auth_temp = $this->headers['Authorization'];
		unset($this->headers['Authorization']);
		$option = get_option('lingotek_token');
		$this->headers['Authorization-Lingotek'] = $option['access_token'];
	}

	private function end_bridge_call() {
		unset($this->headers['Authorization-Lingotek']);
		$this->headers['Authorization'] = $this->auth_temp;
	}
}
