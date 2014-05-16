<div class="frame-inside page-text">
  <div class="container">
    <div class="guestbook">
    	<div class="guestbook__head clearfix">
				<h1>{lang('Leave message', 'guestbook')}</h1>
				<a class="pjax" href="{$BASE_URL}guestbook">
					<i class="icon-white icon-chevron-left"></i> {lang('Back to entries', 'guestbook')}
				</a>
			</div>
			<div class="guestbook__body">
				{if $guest_allow == 1 || ($guest_allow == 0 && $user_id != 0)}
				<div class="guestbook__info">
					{if $form_errors}<div class="errors">{$form_errors}</div>{/if}
					{if $message_sent}<div class="success">{lang('Your message has been sent.', 'guestbook')}</div>{/if}
				</div>
				<form id="guestbook" action="{site_url('guestbook/message')}" method="post">
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
                <label class="left" for="message">{lang('Message', 'guestbook')}</label>
                <label class="right">
                	{lang('You input', 'guestbook')} <span id="txt_now">0</span> {lang('simbols from', 'guestbook')} <span id="txt_max">{$message_max_len}</span> {lang('max available', 'guestbook')}
                </label>
                {if $validation}
                <div class="error" style="color: red">{echo $validation->error('message')}</div>{/if}
                <textarea id="message" cols="45" rows="10" name="message" class="must" placeholder="{lang('Message text', 'guestbook')}">{if $_POST.message}{$_POST.message}{/if}</textarea>
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
                  {if $captcha_type =='captcha'}<label>&nbsp;</label>{/if}
                  <input type="submit" id="submit" class="submit" value="{lang('Send', 'guestbook')}" />
                </div>
              </div>
              {form_csrf()}
            </form>
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