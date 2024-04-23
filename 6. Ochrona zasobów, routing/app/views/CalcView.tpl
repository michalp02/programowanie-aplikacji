{extends file="main.tpl"}

{block name=content}

<div class="pure-menu pure-menu-horizontal bottom-margin">
	<a href="{$conf->action_url}logout"  class="pure-menu-heading pure-menu-link">wyloguj</a>
	<span style="float:right;">użytkownik: {$user->login}, rola: {$user->role}</span>
</div>

<div class="">
<div class="l-box-lrg pure-u-1 pure-u-med-2-5">
	<form class="pure-form pure-form-stacked" action="{$conf->action_root}calcCompute" method="post">
		<label for="id_value">Kwota kredytu: [PLN]</label>
		<input id="id_value" type="text" name="value" value="{if isset($form->credit_value)}{$form->credit_value}{/if}" /><br />
		{if ($user->role == "user")}
		<p class="pure-form-message">Jako użytkownik możesz podać kwotę kredytu do 100000 PLN</p>
		{/if}
		<label for="id_period">Okres kredytowania: </label>
		<input id="id_period" type="text" name="period_value" value="{if isset($form->credit_period)}{$form->credit_period}{/if}" />
		<select name="period_type">
			<option value="months">miesiące</option>
			<option value="years" {if isset($form->period_type)}{if $form->period_type == "years"}selected{/if}{/if}>lata</option>
		</select><br />
		<label for="id_intrest">Oprocentowanie nominalne (roczne): [%]</label>
		<input id="id_intrest" type="text" name="interest_rate" value="{if isset($form->interest_rate)}{$form->interest_rate}{/if}" /><br />
		<label for="id_type">Forma rat: </label>
		<select name="type">
			<option value="constant">Raty stałe</option>
			<option value="decreasing" {if isset($form->type)}{if $form->type == "decreasing"}selected{/if}{/if}>Raty malejące</option>
		</select><br />
		<input type="submit" value="Oblicz raty" class="pure-button"/>
	</form>	
</div>

{include file='messages.tpl'}

{if !$msgs->isError() && $msgs->isInfo() && isset($res) && isset($form->type)}
<div class="messages info">
	<p class="res">
		{if $form->type == 'constant'}
			Miesięczna rata kredytu wynosi: {round($res['rate'], 2)} PLN
		{else}
			Miesięczne raty kredytu wynoszą: <br>
			{foreach $res['rate'] as $key => $r}
				- Rata {($key+1)} wynosi: {round($r, 2)} PLN<br>
			{/foreach}
		{/if}
		<br />
		Całkowity koszt kredytu wynosi: {round($res['cost'])} PLN<br />
		W tym odsetki: {round($res['intrest'])} PLN
	</p>
</div>
{/if}

{/block}