<?php


namespace PGMB\Components;


use PGMB\API\APIInterface;

class BusinessSelector {
	protected $field_name;
	protected $api;
	protected $default_location;
	protected $multiple;
	protected $selected;
	protected $flush_cache;

	public function __construct(APIInterface $api, $field_name = 'mbp_selected_location', $selected = false, $default_location = false, $multiple = false) {
		$this->field_name = $field_name;
		$this->api = $api;
		$this->default_location = $default_location;
		$this->multiple = $multiple;
		$this->selected = $selected;
	}

	public function generate(){
		if(!$this->selected){
			$this->selected = $this->default_location;
		}

		return sprintf( "<div class=\"mbp-business-selector\"><table>%s</table></div>", $this->account_rows());
	}

	public static function draw(APIInterface $api, $field_name = 'mbp_selected_location', $selected = false, $default_location = false, $multiple = false){
		$component = new static($api, $field_name, $selected, $default_location, $multiple);
		echo $component->generate();
		echo $component->business_selector_controls();
	}

	protected function notice_row($message){
		return sprintf( "<tr><td colspan=\"2\">%s</td></tr>", $message );
	}

	protected function account_rows(){
		$accounts = $this->api->get_accounts($this->flush_cache);
		if(!is_object($accounts) || count($accounts->accounts) < 1) {
			return $this->notice_row(__('No user account or location groups found', 'post-to-google-my-business'));
		}

		$rows = '';
		foreach($accounts->accounts as $account){
			$rows .= sprintf( "<tr><td colspan=\"2\"><strong>%s</strong></td></tr>", $account->accountName );
			$rows .= $this->location_rows($account->name);
		}
		return $rows;
	}

	protected function location_rows($account_name){
		$locations = $this->api->get_locations($account_name, $this->flush_cache);
		if (!is_object( $locations ) || count( $locations->locations ) < 1 ) {
			return $this->notice_row(__('No businesses found. Did you log in to the correct Google account?', 'post-to-google-my-business'));
		}

		$rows = '';
		foreach ( $locations->locations as $location ) {
			$disabled = (isset($location->locationState->isLocalPostApiDisabled) && $location->locationState->isLocalPostApiDisabled ? true : false);
			$checked = (is_array($this->selected) && in_array($location->name, $this->selected) || $location->name == $this->selected);

			$rows .= sprintf( '<tr class="mbp-business-item%s">', $disabled ? ' mbp-business-disabled' : '' );

			$rows .= sprintf(
				'<td class="mbp-checkbox-container"><input type="%s" name="%s" id="%s" value="%s"%s%s></td>',
				$this->multiple ? 'checkbox' : 'radio',
				$this->field_name . ($this->multiple ? '[]' : ''),
				$location->name,
				$location->name,
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



	public function business_selector_controls(){
		$options = '<div class="mbp-business-options">
				<input type="text" class="mbp-filter-locations" placeholder="'.__('Search/Filter locations...', 'post-to-google-my-business').'" />';

		if($this->multiple){
			$options .= '&nbsp;<button class="button mbp-select-all-locations">'.__('Select all', 'post-to-google-my-business').'</button>';
			$options .= '&nbsp;<button class="button mbp-select-no-locations">'.__('Select none', 'post-to-google-my-business').'</button>';
		}

		$options .= '
			</div>
			<script>
			 jQuery(document).ready(function($) {

				 $.extend($.expr[":"], {
					 "containsi": function(elem, i, match, array) {
						return (elem.textContent || elem.innerText || "").toLowerCase()
							.indexOf((match[3] || "").toLowerCase()) >= 0;
					}
					});
					
					$(".mbp-filter-locations").keyup(function(){
						let search = $(this).val();

						
						 $( ".mbp-business-selector tr.mbp-business-item").hide()
						 .filter(":containsi(" + search + ")")
						 .show();
					});
					
					$(".mbp-select-all-locations").click(function(event){
						$(".mbp-checkbox-container input:checkbox:visible").prop("checked", true);	    
						event.preventDefault();
					});
					
					$(".mbp-select-no-locations").click(function(event){
						$(".mbp-checkbox-container input:checkbox:visible").prop("checked", false);	   			    
						event.preventDefault();
					});

				});
			</script>';
		return $options;
	}

	public function flush_cache($flush_cache = true){
		$this->flush_cache = $flush_cache;
		return $flush_cache;
	}

}
