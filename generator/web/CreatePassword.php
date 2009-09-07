<?php

require_once '../../src/AgilePHP.php';

AgilePHP::getFramework()->setFrameworkRoot( '../../src' );
$crypto = new Crypto();

if( isset( $_GET['action'] ) ) {

	if( !$_POST['password1'] == $_POST['password2'] )
		die( 'passwords dont match' );

	if( $_GET['action'] == 'generate' )
		echo $crypto->hash( $_POST['algorithm'], $_POST['password1'] ) . '<hr>';
}
?>

<html>
	<head>
		<title>AgilePHP Framework :: Password Generator</title>
	</head>

	<body>
		<form name="frmGenny" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?action=generate">
			<div style="color: #FF0000; padding-bottom: 10px;">Enter the password you want hashed.</div>
			<table border="0">
				<tr>
					<td>Password</td>
					<td><input type="password" name="password1"/></td>
				</tr>
				<tr>
					<td>Confirm</td>
					<td><input type="password" name="password2"/></td>
				</tr>
				<tr>
					<td>Algorithms</td>
					<td>
						<select name="algorithm">
							<?php 
								foreach( $crypto->getSupportedHashAlgorithms() as $algo ) {

									$selected = ($algorithm == $algo) ? ' selected="yes"' : '';
									if( isset( $_POST['algorithm'] ) )
										$selected = ($algo == $_POST['algorithm']) ? 'selected="yes"' : '';

									echo '<option ' . $selected . ' value="' . $algo . '">' . $algo . '</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="Generate"/></td>
				</tr>
			</table>
		</form>
	</body>
</html>