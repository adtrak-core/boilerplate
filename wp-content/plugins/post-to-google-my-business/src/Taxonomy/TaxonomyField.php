<?php


namespace PGMB\Taxonomy;

class TaxonomyField {

	protected $taxonomy;
	protected $field_id;
	protected $field_type;
	protected $field_label;
	protected $field_description;
	protected $field_options = [];

	protected $field_types = [
		'singlecheck',
	];

	public function __construct($taxonomy, $field_id, $field_type, $field_label, $field_description, $field_options = []) {
		if(!taxonomy_exists($taxonomy)){
			throw new \InvalidArgumentException(__("The taxonomy does not exist", "post-to-google-my-business"));
		}

		if(!in_array($field_type, $this->field_types)){
			throw new \InvalidArgumentException(__("Invalid field type", "post-to-google-my-business"));
		}
        $this->taxonomy = $taxonomy;
		$this->field_id = $field_id;
		$this->field_type = $field_type;
		$this->field_label = $field_label;
		$this->field_description = $field_description;
		$this->field_options = $field_options;
	}

	public static function init($taxonomy, $field_id, $field_type, $field_label, $field_description, $field_options = []){
	    $instance = new static($taxonomy, $field_id, $field_type, $field_label, $field_description, $field_options = []);
		add_action("{$taxonomy}_add_form_fields", [$instance, 'draw_add_field'], 10, 2);
		add_action("{$taxonomy}_edit_form_fields", [$instance, 'draw_edit_field'], 10, 2);
		add_action("edited_{$taxonomy}", [$instance, 'save_field'], 10, 2);
		add_action("created_{$taxonomy}", [$instance, 'save_field'], 10, 2);
		return $instance;
	}

	public function draw_add_field($taxonomy){
		?>
		<div class="form-field term-group">
            <input type="hidden" name="<?php echo $this->field_id; ?>_submitted" value="1" />
			<?php $this->draw_input(); ?>
            <p><?php echo $this->field_description; ?></p>
		</div>
		<?php
	}

	public function draw_edit_field($term, $taxonomy){
        $value = get_term_meta($term->term_id, $this->field_id, true);
        ?>
		<tr class="form-field term-group-wrap">
			<th scope="row"><label for="feature-group"><?php echo $this->field_label ?></label></th>
			<td>
				<input type="hidden" name="<?php echo $this->field_id; ?>_submitted" value="1" />
				<?php $this->draw_input($value); ?>
				<p class="description"><?php echo $this->field_description; ?></p>
			</td>
		</tr>
		<?php
	}

	public function save_field($term_id, $tt_id){
	    if(!isset($_POST["{$this->field_id}_submitted"])){ return; }

        switch($this->field_type){
            case 'singlecheck':
                if(isset($_POST[$this->field_id])){
                    $value = true;
                    break;
                }
                $value = false;
                break;
            default:
                if(!isset($_POST[$this->field_id])){ return; }
                $value = sanitize_text_field($_POST[$this->field_id]);
        }
	    update_term_meta($term_id, $this->field_id, $value);
	}

	protected function draw_input($value = null) {
		switch($this->field_type){
			case 'singlecheck':
				$this->draw_single_check($value);
		}
	}

	protected function draw_single_check($value = null) {
		?>
		<label for="<?php echo $this->field_id; ?>">
			<input type="checkbox" id="<?php echo $this->field_id; ?>" name="<?php echo $this->field_id; ?>" value="1" <?php checked($value); ?> /> <?php echo $this->field_label; ?>
		</label>
		<?php
	}

	/**
	 * @return mixed
	 */
	public function getTaxonomy() {
		return $this->taxonomy;
	}

	/**
	 * @return mixed
	 */
	public function getFieldId() {
		return $this->field_id;
	}
}
