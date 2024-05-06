<table class="pure-table pure-table-bordered">
<thead>
	<tr>
		<th>imię</th>
		<th>nazwisko</th>
		<th>telefon</th>
		<th>data ur.</th>
		<th>opcje</th>
	</tr>
</thead>
<tbody>
{foreach $people as $p}
{strip}
	<tr>
		<td>{$p["name"]}</td>
		<td>{$p["surname"]}</td>
		<td>{$p["phone"]}</td>
		<td>{$p["birthdate"]}</td>
		<td>
			<a class="button-small pure-button button-secondary" href="{$conf->action_url}personEdit/{$p['idperson']}">Edytuj</a>
			&nbsp;
			<a class="button-small pure-button button-warning"
			  onclick="confirmLink('{$conf->action_url}personDelete/{$p['idperson']}','Czy na pewno usunąć rekord ?')">Usuń</a>
		</td>
	</tr>
{/strip}
{/foreach}
</tbody>
</table>