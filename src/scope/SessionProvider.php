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
 * @package com.makeabyte.agilephp.scope
 */

/**
 * Interface for Session persistence providers.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.scope
 */
interface SessionProvider {

    /**
     * Retrieves the Session domain model
     * 
     * @return Session The Session domain model
     */
    public function getSession();
    
    /**
     * Sets the session id. Useful for manually restoring a session.
     * 
     * @param string $id The id of the session to restore
     * @return void
     */
    public function setSessionId($id);
    
    /**
     * Retrieves the current session id. If a session does not exist
     * it will be created.
     * 
     * @return string The current session id
     */
    public function getSessionId();
    
    /**
     * Persists the Session
     * 
     * @return void
     */
    public function persist();
    
    /**
     * Refreshes the session by reloading its data from its source
     * 
     * @return void
     */
    public function refresh();
    
    /**
     * Clears all variables in the current session
     * 
     * @return void
     */
    public function clear();
    
    /**
     * Destroys the current session and its variables
     * 
     * @return void
     */
    public function destroy();
}
?>