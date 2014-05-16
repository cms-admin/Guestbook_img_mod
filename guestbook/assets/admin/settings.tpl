<div class="container">
	<section class="mini-layout">
		<div class="frame_title clearfix">
			<div class="pull-left">
				<span class="help-inline"></span>
				<span class="title">{lang('Settings', 'guestbook')}</span>
			</div>
			<div class="pull-right">
				<div class="d-i_b">
					<a href="{$BASE_URL}admin/components/cp/guestbook" class="t-d_n m-r_15 pjax">
						<span class="f-s_14">&larr;</span>
						<span class="t-d_u">{lang('Back', 'guestbook')}</span>
					</a>
					<button type="button" class="btn btn-small btn-primary action_on formSubmit" data-form="#guestbook_settings_form" data-action="tomain"><i class="icon-ok"></i>{lang("Save", 'guestbook')}</button>
				</div>
			</div>
		</div>
		<form method="post" action="{site_url('admin/components/cp/guestbook/update_settings')}" class="form-horizontal" id="guestbook_settings_form">
			<div class="inside_padd">
				<div class="control-group">
					<label class="control-label" for="admin_email">{lang('Email for notices', 'guestbook')}:</label>
					<div class="controls">
						<input type = "text" name="admin_email" class="textbox_short" value="{$settings.admin_email}" id="gs__admin-email"/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="admin_email">{lang('Message max length', 'guestbook')}:</label>
					<div class="controls">
						<input type = "text" name="message_max_len" class="textbox_short" value="{$settings.message_max_len}"/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="per_page">{lang('Entries per page', 'guestbook')}:</label>
					<div class="controls">
						<input type = "text" name="per_page" class="textbox_short" value="{$settings.per_page}" id="gs__per-page"/>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="can_guest">{lang('Allow guests to write', 'guestbook')}:</label>
					<div class="controls">
						<select name="can_guest" id="gs__can-guest">
							<option {if $settings.can_guest == 0} selected {/if} value="0">{lang('No', 'guestbook')}</option>
							<option {if $settings.can_guest == 1} selected {/if} value="1">{lang('Yes', 'guestbook')}</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="default_status">{lang('To publish immediately', 'guestbook')}:</label>
					<div class="controls">
						<select name="default_status" id="gs__default-status">
							<option {if $settings.default_status == 0} selected {/if} value="0">{lang('No', 'guestbook')}</option>
							<option {if $settings.default_status == 1} selected {/if} value="1">{lang('Yes', 'guestbook')}</option>
						</select>
					</div>
				</div>
			</div>
			{form_csrf()}
		</form>
	</section>
</div>