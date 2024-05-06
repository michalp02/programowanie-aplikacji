{extends file="main.tpl"}

{block name=top}

<div class="bottom-margin">
<form id="search-form" class="pure-form pure-form-stacked" onsubmit="ajaxPostForm('search-form','{$conf->action_root}personListPart','table'); return false;">
	<legend>Opcje wyszukiwania</legend>
	<fieldset>
		<input type="text" placeholder="nazwisko" name="sf_surname" value="{$searchForm->surname}" /><br />
		<button type="submit" class="pure-button pure-button-primary">Filtruj</button>
	</fieldset>
</form>
</div>	

{/block}

{block name=bottom}

<div class="bottom-margin">
<a class="pure-button button-success" href="{$conf->action_root}personNew">+ Nowa osoba</a>
</div>	

<div id="table">
{include file="PersonListTable.tpl"}
</div>

{/block}
