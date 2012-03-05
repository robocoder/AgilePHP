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
 * @package com.makeabyte.agilephp.studio.control
 */

/**
 * Controller responsible for exposing server side PHP classes to AgilePHP client
 * side remoting operations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.control
 */
class RemotingController extends Remoting {

    /**
     * Overloads the parent invoke method to require an authenticated session
     * before allowing a client to invoke any remote methods
     *
     * @return void
     */
    public function invoke() {

        // Require authentication for all remote invocations
        if( !Identity::isLoggedIn() )
        throw new AccessDeniedException( 'You must be logged in to view the requested content.' );

        parent::invoke();
    }
}
?>