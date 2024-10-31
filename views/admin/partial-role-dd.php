<select class="regular-text" 
	id="<?php echo esc_attr($data['name']); ?>" 
	name="<?php echo esc_attr($data['option_name']); ?>[<?php echo esc_attr($data['name']); ?>]">
	<?php foreach ($data['roles'] as $role => $values) { ?>
		<?php if ($role != 'administrator') { ?>
		<option value="<?php echo esc_attr($role); ?>" <?php echo ($role == $data['value'] ? "selected" : ""); ?>>
			<?php echo esc_attr($values['name']); ?>
		</option>
		<?php } ?>
	<?php } ?>
</select>
