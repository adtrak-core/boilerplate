<?php


namespace PGMB\Components;


use PGMB\API\APIInterface;

class BusinessSelector {
	protected $api;
	protected $field_name = 'mbp_selected_location';
	protected $default_location;
	protected $multiple;
	protected $selected;
	protected $flush_cache;

	public function __construct(APIInterface $api) {
		$this->api = $api;
	}

	public function generate(){
		if(!$this->selected){
			$this->selected = $this->default_location;
		}

		return "<div class=\"mbp-business-selector\"><table>{$this->account_rows()}</table></div>";
	}

	public static function draw(APIInterface $api, $field_name = 'mbp_selected_location', $selected = false, $default_location = false, $multiple = false){
		$component = new static($api);
		$component->set_field_name($field_name);
		$component->set_selected_locations($selected);
		$component->set_default_location($default_location);
		$component->enable_multiple($multiple);
		echo $component->location_blocked_info();
		echo $component->generate();
		echo $component->business_selector_controls();
	}

	protected function notice_row($message){
		return sprintf( "<tr><td colspan=\"2\">%s</td></tr>", $message );
	}

	protected function account_rows(){
		$accounts = $this->api->get_accounts($this->flush_cache);
		if(!is_object($accounts) || count($accounts->accounts) < 1) {
			return $this->notice_row(__('No user account or location groups found. Did you log in to the correct Google account?', 'post-to-google-my-business'));
		}

		$rows = '';
		$accounts->accounts = apply_filters('mbp_business_selector_accounts', $accounts->accounts);
		foreach($accounts->accounts as $account){
			$rows .= "<tbody><tr><td colspan='2'><strong>{$account->accountName}</strong>";
			if($this->multiple){
				$rows .= sprintf(" [ <a href='#' class='pgmb-toggle-group'>%s</a> ]", __('Toggle selection', 'post-to-google-my-business'));
			}
			$rows .= '</td></tr>';
			$rows .= $this->location_rows($account->name);
			$rows .= '</tbody>';
		}
		return $rows;
	}

	protected function location_rows($account_name){
		$locations = $this->api->get_locations($account_name, $this->flush_cache);
		if (!is_object( $locations ) || !isset($locations->locations) || count( $locations->locations ) < 1 ) {
			return $this->notice_row(__('No businesses found.', 'post-to-google-my-business'));
		}

		$rows = '';
		$locations->locations = apply_filters('mbp_business_selector_locations', $locations->locations);
		foreach ( $locations->locations as $location ) {
			//$disabled = false; //Todo: Temporary due to covid-19  //isset($location->locationState->isLocalPostApiDisabled) && $location->locationState->isLocalPostApiDisabled;
			$disabled = !isset($location->locationState->isVerified) || !$location->locationState->isVerified || !isset($location->locationState->isPublished) || !$location->locationState->isPublished;
			$checked = (is_array($this->selected) && in_array($location->name, $this->selected) || $location->name == $this->selected);

			$rows .= sprintf( '<tr class="mbp-business-item%s">', $disabled ? ' mbp-business-disabled' : '' );

			$rows .= sprintf(
				'<td class="mbp-checkbox-container"><input type="%s" name="%s"  value="%s"%s%s></td>',
				$this->multiple ? 'checkbox' : 'radio',
				esc_attr($this->field_name),
				esc_attr($location->name),
				disabled($disabled, true, false),
				checked($checked, true, false)
			);

			$rows .= $this->location_data_column($location);

			$rows .= '</tr>';
		}
		return $rows;
	}

	protected function location_data_column($location) {
		$addressLines = implode(' - ', (array)$location->address->addressLines);

		return sprintf(
	"<td class=\"mbp-info-container\">
				<label for=\"%s\">
					<strong>%s</strong>
					<a href=\"%s\" target=\"_blank\">
						<span class=\"mbp-address\">
							%s - 
							%s
							%s
						</span> 
					</a>
				</label>
			</td>",
			$location->name,
			$location->locationName,
			isset( $location->metadata->mapsUrl ) ? $location->metadata->mapsUrl : '',
			$addressLines,
			$location->address->postalCode,
			$location->address->locality
		);
	}

	public function location_blocked_info(){
		return sprintf("				
			<div class=\"mbp-info mbp-location-blocked-info\">
				<strong>%s</strong>
				%s
				<a href=\"https://posttogmb.com/localpostapiblocked\" target=\"_blank\">%s</a>
			</div>
		",
			__('Location grayed out?', 'post-to-google-my-business'),
			__('It means the location is blocked from using the LocalPostAPI, and can\'t be posted to using the plugin.', 'post-to-google-my-business'),
			__('Learn more...', 'post-to-google-my-business')
		);
	}

	public function business_selector_controls(){
		$options = '<div class="mbp-business-options">
				<input type="text" class="mbp-filter-locations" placeholder="'.__('Search/Filter locations...', 'post-to-google-my-business').'" />';

		if($this->multiple){
			$options .= '&nbsp;<button class="button mbp-select-all-locations">'.__('Select all', 'post-to-google-my-business').'</button>';
			$options .= '&nbsp;<button class="button mbp-select-no-locations">'.__('Select none', 'post-to-google-my-business').'</button>';
		}

		$options .= '
			<button class="button mbp-refresh-locations refresh-api-cache" style="float:right;">'.__('Refresh locations', 'post-to-google-my-business').'</button>
			</div>';
		return $options;
	}

	public function flush_cache($flush_cache = true){
		$this->flush_cache = $flush_cache;
		return $flush_cache;
	}

	public function ajax_refresh(){
		$refresh = isset($_POST['refresh']) && $_POST['refresh'] == "true";

		$selected = isset($_POST['selected']) ? (array)$_POST['selected'] : [];
		$selected = array_map("sanitize_text_field", $selected);

		$this->set_selected_locations($selected);

		$this->flush_cache($refresh);
		echo $this->generate();
		wp_die();
	}

	public function set_field_name($field_name){
		$this->field_name = $field_name;
	}

	public function set_selected_locations($locations){
		$this->selected = $locations;
	}

	public function set_default_location( $default_location ) {
		$this->default_location = $default_location;
	}

	public function enable_multiple( $multiple ) {
		$this->multiple = $multiple;
	}

	public function register_ajax_callbacks($prefix) {
		add_action("wp_ajax_{$prefix}_refresh_locations", [$this, 'ajax_refresh']);
	}
}
