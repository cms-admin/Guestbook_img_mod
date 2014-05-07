<div class="container">
	<section class="mini-layout">
		<div class="frame_title clearfix">
			<div class="pull-left">
				<span class="help-inline"></span>
				<span class="title">{lang('Guestbook', 'guestbook')}</span>
			</div>
			<div class="pull-right">
				<div class="d-i_b">
					<div class="dropdown d-i_b">
						<button type="button" class="btn btn-small dropdown-toggle disabled action_on" data-toggle="dropdown">
							<i class="icon-tag"></i>
							{lang("Status", 'guestbook')}
							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<li><a href="#" class="to_hide">{lang('To waiting', 'guestbook')}</a></li>
							<li><a href="#" class="to_show">{lang('To approved', 'guestbook')}</a></li>
						</ul>
					</div>
					<button type="button" class="btn btn-small btn-danger disabled action_on" id="to_del"><i class="icon-trash icon-white"></i>{lang("Delete", 'guestbook')}</button>
					<a class="btn btn-small pjax" href="{$BASE_URL}admin/components/cp/guestbook/settings"><i class="icon-wrench"></i>{lang('Settings', 'guestbook')}</a>
				</div>
			</div>
		</div>
		<div class="tab-pane active" id="mail">
			<table class="table table-striped table-bordered table-hover table-condensed">
				<thead>
					<tr>
						<th class="t-a_c span1">
							<span class="frame_label">
								<span class="niceCheck b_n">
									<input type="checkbox" value="On"/>
								</span>
							</span>
						</th>
						<th width="24px">{lang("ID", 'guestbook')}</th>
						<th width="120px">{lang("User name", 'guestbook')}</th>
						<th width="150px">{lang("User e-mail", 'guestbook')}</th>
						<th>{lang("Entry text", 'guestbook')}</th>
						<th width="100px">{lang("Date", 'guestbook')}</th>
						<th width="120px">{lang("Rating", 'guestbook')}</th>
						<th width="100px">{lang("Status", 'guestbook')}</th>
					</tr>
				</thead>
				<tbody>
					{if $g_comm}
					{foreach $g_comm as $item}
					<tr data-id="{$item.id}" data-tree>
						<td class="t-a_c">
							<span class="frame_label">
								<span class="niceCheck b_n">
									<input type="checkbox" value="{echo $item.id}" id="nc{$item.id}" name="ids"/>
								</span>
							</span>
						</td>
						<td>{$item.id}</td>
						<td>
							{if $item.user_id}<a href="{$BASE_URL}admin/components/cp/user_manager/edit_user/{$item.user_id}" class="pjax">{/if}
								{$item.user_name}
							{if $item.user_id}</a>{/if}
						</td>
						<td>{$item.user_mail}</td>
						<td>{$item.text}</td>
						<td>{date('d.m.Y H:i', $item.date)}</td>
						<td>
							{if $item.rate == 0}
								<i class="icon-minus-sign"></i> {lang('Negative', 'guestbook')}
							{else:}
								<i class="icon-plus-sign"></i> {lang('Positive', 'guestbook')}
							{/if}
						</td>
						<td>
							{if $item.status == 0}
								{lang('Approval pending', 'guestbook')}
							{else:}
								{lang('Approved', 'guestbook')}
							{/if}
						</td>
					</tr>
					{/foreach}
					{else:}
					<tr><td colspan="8" style="padding:10px;text-align:center;">{lang('Entries not found', 'guestbook')}</td></tr>
					{/if}
				</tbody>
			</table>
		</div>
	</section>
</div>
