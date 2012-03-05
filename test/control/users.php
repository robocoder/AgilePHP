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
 * @package com.makeabyte.agilephp.test.control
 */

/**
 * Demonstrates the ability to make a simple, secure rest style web service using
 * the MVC and REST framework components. Note this class can be used as a standard
 * PHP class or a DAO for example, in conjunction with being a REST service, due to
 * the Aspect Oriented Programming style using interceptors and annotations.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.test.control
 */
#@BasicAuthentication
#@RestService
class users extends BaseController {

    #@GET
    public function index() {
        return ORM::find(new User());
    }

    #@GET
    #@Path(resource = '/{username}')
    #@ProduceMime(type = 'application/xml')
    public function getUser($username) {

        $user = new User();
        $user->setUsername($username);

        return $user;
    }

    #@GET
    #@Path(resource = '/{username}/role')
    public function getRole($username) {

        $user = new User();
        $user->setUsername($username);

        return $user->getRole();
    }

    #@GET
    #@Path(resource = '/{username}/session')
    public function getSession($username) {

        return Scope::getSessionScope()->getSession();
    }

    #@POST
    #@Path(resource = '/{username}')
    #@ConsumeMime(type = 'application/xml')
    #@ProduceMime(type = 'application/xml')
    public function createUser($username, User $user) {

        $user->persist();
        return $user;
    }

    #@PUT
    #@Path(resource = '/{username}')
    #@ConsumeMime(type = 'application/xml')
    #@ProduceMime(type = 'application/xml')
    public function updateUser($username, User $user) {

        $user->merge();
        return $user;
    }

    #@PUT
    #@Path(resource = '/{username}/json')
    #@ConsumeMime(type = 'application/json')
    #@ProduceMime(type = 'application/json')
    /**
     * Updates the specified user. The {username} in the #@Path annotation
     * causes the first parameter in the resource URI to be captured and
     * passed into the method as the first argument. The second User parameter
     * is the transformed JSON data submitted in the body of the HTTP request.
     *
     * @param string $username The unique username to update
     * @param User $user The data used to update the account
     * @return void
     */
    public function updateUserJSON($username, User $user) {

        $user->merge();
        return $user;
    }

    #@PUT
    #@Path(resource = '/{username}/wildcard')
    /**
     * Updates the specified user using a wildcard.
     *
     * @param string $username The unique username to update
     * @param User $user The user instance to update
     * @return User The modified user instance
     */
    public function updateUserWildcard($username, User $user) {

        $user->merge();
        return $user;
    }

    #@POST
    #@Path(resource = '/{username}/json')
    #@ConsumeMime(type = 'application/json')
    #@ProduceMime(type = 'application/json')
    /**
     * Updates the specified user. The {username} in the #@Path annotation
     * causes the first parameter in the resource URI to be captured and
     * passed into the method as the first argument. The second User parameter
     * is the transformed JSON data submitted in the body of the HTTP request.
     *
     * @param string $username The unique username to update
     * @param User $user The data used to update the account
     * @return void
     */
    public function updateUserJSON2($username, User $user) {

        $user->merge();
        return $user;
    }
     
    #@DELETE
    #@Path(resource = '/{username}')
    public function deleteUser($username) {

        $user = new User();
        $user->setUsername($username);
        $user->delete();
    }
}
?>