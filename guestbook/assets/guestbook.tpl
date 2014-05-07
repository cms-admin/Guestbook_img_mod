{$default = 'http://img-fotki.yandex.ru/get/5401/96344121.1c/0_a6985_bbae3123_XS.jpg'}
{$size = '70'}
<div class="frame-inside page-text">
  <div class="container">
    <div class="text">
      <h1>{lang('Guestbook', 'guestbook')}</h1>
      <div id="tabs">
        <ul>
          <li><a href="#all">{lang('All entries', 'guestbook')} ({$all_total})</a></li>
          <li><a href="#positive">{lang('Positive entries', 'guestbook')} ({if $pos_items}{echo count($pos_items)}{else:}0{/if})</a></li>
          <li><a href="#negative">{lang('Negative entries', 'guestbook')} ({if $neg_items}{echo count($neg_items)}{else:}0{/if})</a></li>
          <li><a href="#write">{lang('Write entry', 'guestbook')}</a></li>
        </ul>
        <div id="all">
          {if $all_items}
          <div class="item-list">
            {foreach $all_items as $item}
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
          </div>
          {else:}
          <p>{lang('No entries', 'guestbook')}</p>
          {/if}
        </div>
        <div id="positive">
          {if $pos_items}
          <div class="item-list">
            {foreach $pos_items as $item}
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
          </div>
          {else:}
          <p>{lang('No entries', 'guestbook')}</p>
          {/if}
        </div>
        <div id="negative">
          {if $neg_items}
          <div class="item-list">
            {foreach $neg_items as $item}
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
          </div>
          {else:}
          <p>{lang('No entries', 'guestbook')}</p>
          {/if}
        </div>
        <div id="write">
          {if $guest_allow == 1 || ($guest_allow == 0 && $user_id != 0)}
          <div id="contact">
            {if $form_errors}
              <div class="errors">{$form_errors}</div>
            {/if}
            {if $message_sent}
              <div style="color: green;">{lang('Your message has been sent.', 'guestbook')}</div>
            {/if}
            <form action="{site_url('guestbook')}" method="post">
              <div class="clearfix">
                <div class="gb-form__input third">
                  <label for="name">{lang('Your name', 'guestbook')}</label>
                  {if $validation}
                  <div class="error" style="color: red">{echo $validation->error('name')}</div>
                  {/if}
                  <input type="text" id="name" class="must" name="name" value="{if $_POST.name}{$_POST.name}{/if}" placeholder="{lang('Your name', 'guestbook')}"/>
                </div>

                <div class="gb-form__input third">
                  <label for="email">{lang('Email', 'guestbook')}</label>
                  {if $validation}
                  <div class="error" style="color: red">{echo $validation->error('email')}</div>
                  {/if}
                  <input type="text" id="email" name="email" class="must" value="{if $_POST.email}{$_POST.email}{/if}" placeholder="{lang('Email', 'guestbook')}"/>
                </div>
            
                <div class="gb-form__input third">
                  <label for="rate">{lang('Your feedback', 'guestbook')}:</label>
                  <select name = "rate" class="protocolSettings" id="protocol">
                    <option selected value="1" >{lang('Positive', 'guestbook')}</option>
                    <option value="0">{lang('Negative', 'guestbook')}</option>
                  </select>
                </div>
            
              </div>
          
              <div class="gb-form__input">
                <label for="message">{lang('Message', 'guestbook')}</label>
                {if $validation}
                <div class="error" style="color: red">{echo $validation->error('message')}</div>{/if}
                <textarea cols="45" rows="10" name="message" class="must" placeholder="{lang('Message text', 'guestbook')}">{if $_POST.message}{$_POST.message}{/if}</textarea>
              </div>
          
              <div class="clearfix">
                {if $captcha_type =='captcha'}
                <div class="gb-form__input captcha">
                  {if $validation}
                  <div class="error" style="color: red">
                    {echo $validation->error('captcha')}
                  </div>
                  {/if}
                  <label for="captcha">{lang('Protection code', 'guestbook')}</label>
                  <input type="text" name="captcha" class="must" value="" placeholder="{lang('Enter protection code', 'guestbook')}"/>
                  <span class="cap-img">{$cap_image}</span>
                </div>
                {/if}
                <div class="gb-form__input third">
                  <label>&nbsp;</label>
                  <input type="submit" id="submit" class="submit" value="{lang('Send', 'guestbook')}" />
                </div>
              </div>
              {form_csrf()}
            </form>
          </div>
          {else:}
          <div id="contact">
            <div class="errors">{lang('To leave a message, you must log in or register.', 'guestbook')}</div>
          </div>
          {/if}
        </div>
      </div>
    </div>
  </div>
</div>