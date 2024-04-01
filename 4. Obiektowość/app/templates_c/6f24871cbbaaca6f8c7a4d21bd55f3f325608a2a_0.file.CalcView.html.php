<?php
/* Smarty version 3.1.30, created on 2024-04-01 21:59:43
  from "/home1/mppl/aplikacje.pasierb.ski/4/app/CalcView.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_660b122fd09559_20271951',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6f24871cbbaaca6f8c7a4d21bd55f3f325608a2a' => 
    array (
      0 => '/home1/mppl/aplikacje.pasierb.ski/4/app/CalcView.html',
      1 => 1712001583,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_660b122fd09559_20271951 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1095578191660b122fce3d76_67716446', 'footer');
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_1591099129660b122fd087a6_03537966', 'content');
$_smarty_tpl->inheritance->endChild();
$_smarty_tpl->_subTemplateRender(($_smarty_tpl->tpl_vars['conf']->value->root_path).("/templates/main.html"), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 2, true);
}
/* {block 'footer'} */
class Block_1095578191660b122fce3d76_67716446 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>
przykładowa tresć stopki wpisana do szablonu głównego z szablonu kalkulatora<?php
}
}
/* {/block 'footer'} */
/* {block 'content'} */
class Block_1591099129660b122fd087a6_03537966 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


<h2 class="content-head is-center">Kalkulator kredytowy</h2>

<div class="pure-g">
<div class="l-box-lrg pure-u-1 pure-u-med-2-5">
	<form class="pure-form pure-form-stacked" action="<?php echo $_smarty_tpl->tpl_vars['conf']->value->app_url;?>
/app/calc.php" method="post">
		<label for="id_value">Kwota kredytu: [PLN]</label>
		<input id="id_value" type="text" name="value" value="<?php if (isset($_smarty_tpl->tpl_vars['form']->value->credit_value)) {
echo $_smarty_tpl->tpl_vars['form']->value->credit_value;
}?>" /><br />
		<label for="id_period">Okres kredytowania: </label>
		<input id="id_period" type="text" name="period_value" value="<?php if (isset($_smarty_tpl->tpl_vars['form']->value->credit_period)) {
echo $_smarty_tpl->tpl_vars['form']->value->credit_period;
}?>" />
		<select name="period_type">
			<option value="months">miesiące</option>
			<option value="years" <?php if (isset($_smarty_tpl->tpl_vars['form']->value->period_type)) {
if ($_smarty_tpl->tpl_vars['form']->value->period_type == "years") {?>selected<?php }
}?>>lata</option>
		</select><br />
		<label for="id_intrest">Oprocentowanie nominalne (roczne): [%]</label>
		<input id="id_intrest" type="text" name="interest_rate" value="<?php if (isset($_smarty_tpl->tpl_vars['form']->value->interest_rate)) {
echo $_smarty_tpl->tpl_vars['form']->value->interest_rate;
}?>" /><br />
		<label for="id_type">Forma rat: </label>
		<select name="type">
			<option value="constant">Raty stałe</option>
			<option value="decreasing" <?php if (isset($_smarty_tpl->tpl_vars['form']->value->type)) {
if ($_smarty_tpl->tpl_vars['form']->value->type == "decreasing") {?>selected<?php }
}?>>Raty malejące</option>
		</select><br />
		<input type="submit" value="Oblicz raty" class="pure-button"/>
	</form>	
</div>

<div class="l-box-lrg pure-u-1 pure-u-med-3-5">


<?php if ($_smarty_tpl->tpl_vars['msgs']->value->isError()) {?>
	<h4>Wystąpiły błędy: </h4>
	<ol class="err">
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['msgs']->value->getErrors(), 'err');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['err']->value) {
?>
	<li><?php echo $_smarty_tpl->tpl_vars['err']->value;?>
</li>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

	</ol>
<?php }?>


<?php if ($_smarty_tpl->tpl_vars['msgs']->value->isInfo()) {?>
	<h4>Informacje: </h4>
	<ol class="inf">
	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['msgs']->value->getInfos(), 'inf');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['inf']->value) {
?>
	<li><?php echo $_smarty_tpl->tpl_vars['inf']->value;?>
</li>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

	</ol>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['msgs']->value->isInfo() && !$_smarty_tpl->tpl_vars['msgs']->value->isError()) {?>
	<h4>Wynik</h4>
	<p class="res">
		<?php if ($_smarty_tpl->tpl_vars['form']->value->type == 'constant') {?>
			Miesięczna rata kredytu wynosi: <?php echo round($_smarty_tpl->tpl_vars['res']->value['rate'],2);?>
 PLN
		<?php } else { ?>
			Miesięczne raty kredytu wynoszą: <br>
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['res']->value['rate'], 'r', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['r']->value) {
?>
				- Rata <?php echo ($_smarty_tpl->tpl_vars['key']->value+1);?>
 wynosi: <?php echo round($_smarty_tpl->tpl_vars['r']->value,2);?>
 PLN<br>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

		<?php }?>
		<br />
		Całkowity koszt kredytu wynosi: <?php echo round($_smarty_tpl->tpl_vars['res']->value['cost']);?>
 PLN<br />
		W tym odsetki: <?php echo round($_smarty_tpl->tpl_vars['res']->value['intrest']);?>
 PLN
	</p>
<?php }?>

</div>
</div>

<?php
}
}
/* {/block 'content'} */
}
