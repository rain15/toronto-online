<div class="tec-meetup-settings-form" id="tec-meetup-import-wrap">
	<div class="tec-meetup-settings-form-wrap">

		<form id="tec-meetup-import-form" method="POST">
			<div class="setting">
				<span class="label-wrap">
					<label for="tec-meetup-import-group-url">Group URL:</label>
					<small>Ex: http://www.meetup.com/austinwordpress/</small>
				</span>
				<input
					type="text"
					name="tec-meetup-import-group-url"
					id="tec-meetup-import-group-url"
					value=""
					required>
			</div>

			<div class="setting">
				<span class="label-wrap">
					<label for="tec-meetup-import-import-cats">Event Categories:</label>
					<small>All events from this import will be assigned these categories.</small>
				</span>
				<select multiple name="tec-meetup-import-cats[]" id="tec-meetup-import-cats">
					<?php foreach ($event_cats as $cat) : ?>
						<option value="<?php echo $cat->term_id; ?>"><?php echo $cat->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<?php wp_nonce_field( 'add-meetup-recurring-import', 'tec-meetup-add-meetup-recurring-import' ); ?>

			<input type="hidden" name="action" value="tec_meetup_add_meetup_recurring_import">

			<div class="submit-wrap">
				<input class="button-secondary" type="submit" value="Add Recurring Import">
			</div>
		</form>

		<hr>

		<?php
			$meetup_imports_table = new TMI_Saved_Imports_Table();
			$meetup_imports_table->prepare_items();
			$meetup_imports_table->display();
		?>

	</div>
</div>
