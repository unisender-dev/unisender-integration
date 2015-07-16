<table class="wp-list-table widefat fixed">
	<colgroup>
		<col style="width: 80px;">
		<col style="min-width: 200px;">
		<col style="width: 200px;">
		<col style="width: 100px; text-align: center;">
		<col style="width: 180px; text-align: center;">
	</colgroup>
	<thead>
	<tr>
		<th class="manage-column" style="width: 80px;">Id</th>
		<th class="manage-column">
			<?php _e('Title', $this->textdomain) ?>
		</th>
		<th class="manage-column">
			<?php _e('Related WP field', $this->textdomain) ?>

		</th>
        <th class="manage-column">
            <?php _e('Enabled', $this->textdomain) ?>
        </th>
        <th class="manage-column">
            <?php _e('Displays in form', $this->textdomain) ?>
        </th>
        <th><a href="<?php echo admin_url('tools.php?page=unisender&action=edit&field=0'); ?>" class="add-new-h2" style="float: right;"><?php _e('New additional field', $this->textdomain); ?></a></th>
	</tr>
	</thead>

	<tbody id="the-list">
	<?php foreach ($fields as $f) : ?>
		<tr>
			<th class="manage-column">
				<span><?php echo $f['id']; ?></span>
			</th>
			<td class="manage-column">
				<strong><?php echo $f['public_name']; ?> (<?php echo $f['name']; ?>)</strong>

				<div class="row-actions">
                <?php if ($f['name'] === 'email') { ?>
                    <span class="description"><?php _e('The field email is mandatory and can not be edited', $this->textdomain); ?></span>
                <?php } else { ?>
					<span><a href="<?php echo admin_url('tools.php?page=unisender&action=edit&field=' . $f['id']); ?>"><?php _e('Edit', $this->textdomain); ?></a> | </span>
					<span class="trash"><a class="submitdelete" href="#" onClick="return actionDelete(<?php echo $f['id']; ?>, '<?php echo admin_url('tools.php?page=unisender&action=delete&field=' . $f['id']); ?>')"><?php _e('Delete', $this->textdomain); ?></a></span>
                <?php } ?>
				</div>
			</td>
			<td class="manage-column" align="center">
				<span><?php echo $f['connect']; ?></span>
			</td>
            <td class="manage-column" align="center">
                <span><?php echo $f['is_enabled'] ? '<img src="'.admin_url('images/yes.png').'">' : ''; ?></span>
            </td>
            <td class="manage-column" align="center">
                <span><?php echo $f['is_in_form'] ? '<img src="'.admin_url('images/yes.png').'">': ''; ?></span>
            </td>
            <td></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
