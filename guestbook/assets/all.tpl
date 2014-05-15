{$default = 'http://img-fotki.yandex.ru/get/5401/96344121.1c/0_a6985_bbae3123_XS.jpg'}
{$size = '70'}
<div class="frame-inside page-text">
	<div class="container">
		<div class="guestbook">
			<div class="guestbook__head clearfix">
				<h1>{lang('Guestbook', 'guestbook')}</h1>
				<a class="pjax" href="{$BASE_URL}guestbook/message">
					<i class="icon-white icon-plus-sign"></i> {lang('Leave message', 'guestbook')}
				</a>
			</div>
			<div class="guestbook__body">
				{if $count > 0}
					{foreach $items as $item}
					<div class="entry" data-rating="{$item.rate}">
						<div class="entry__autor-img">
							<img class="avatar" width="60" height="60" src="http://www.gravatar.com/avatar/{md5( strtolower( trim( $item.user_mail ) ) )}?d={urlencode( $default )}&s={$size}" alt="{$item.user_name}">
						</div>
						<div class="entry__text-wrap">
							<div class="entry__text-head">
								<h4>{$item.user_name}</h4>
								<span>{date('d.m.Y H:i', $item.date)}</span>
							</div>
							<div class="entry__text-body">{$item.text}</div>
						</div>
					</div>
					{/foreach}
          {$paginator}
          {else:}
          <p>{lang('No entries', 'guestbook')}</p>
				{/if}
			</div>
		</div>
	</div>
</div>