<?php
/**
 * AgilePHP Framework :: The Rapid "for developers" PHP5 framework
 * Copyright (C) 2009-2010 Make A Byte, inc
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package com.makeabyte.agilephp.generator
 */

/**
 * Point your browser to this file to generate a password using the AgilePHP
 * Crypto component.
 * 
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.generator
 * @version 0.1a
 */
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