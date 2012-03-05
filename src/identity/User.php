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
 * @package com.makeabyte.agilephp.identity
 */

/**
 * Domain model used by the Identity component. Represents a persistable
 * user.
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.identity
 */
class User extends DomainModel implements IdentityModel {

    /**
     * @var string $username The users username
     */
    private $username;
    /**
     * @var string $password The users password
     */
    private $password;
    /**
     * @var string $email The users email address
     */
    private $email;
    /**
     * @var string $created The date when the user was created
     */
    private $created;
    /**
     * @var string $lastLogin The date when the user last logged in
     */
    private $lastLogin;
    /**
     * @var bool $enable True if the user account is enabled, false to disable
     */
    private $enabled;
    /**
     * @var Role $Role The role which the user belongs
     */
    private $Role;
    /**
     * @var array<Role> $Roles A list of roles which the user belongs
     */
    private $Roles;

    public function __construct($username = null, $password = null, $email = null,
            $created = null, $lastLogin = null, $enabled = null, Role $Role = null) {

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->created = $created;
        $this->lastLogin = $lastLogin;
        $this->enabled = $enabled;
        $this->Role = $Role;
    }

    /**
     * (non-PHPdoc)
     * @param string $username The users username
     * @see src/identity/IdentityModel#setUsername($username)
     */
    #@Id
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * (non-PHPdoc)
     * @return string The users username
     * @see src/identity/IdentityModel#getUsername()
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * (non-PHPdoc)
     * @param string $password The users password
     * @see src/identity/IdentityModel#setPassword($password)
     */
    #@Password
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * (non-PHPdoc)
     * @return string The users password
     * @see src/identity/IdentityModel#getPassword()
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * (non-PHPdoc)
     * @param string $email The users email address
     * @see src/identity/IdentityModel#setEmail($email)
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * (non-PHPdoc)
     * @return string The users email address
     * @see src/identity/IdentityModel#getEmail()
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * (non-PHPdoc)
     * @param string $dateTime The datetime when the account was created
     * @see src/identity/IdentityModel#setCreated($dateTime)
     */
    public function setCreated($dateTime) {
        $this->created = date('Y-m-d H:i:s', strtotime($dateTime));
    }

    /**
     * (non-PHPdoc)
     * @return string The datetime when the account was created
     * @see src/identity/IdentityModel#getCreated()
     */
    public function getCreated() {
        return (string)$this->created;
    }

    /**
     * (non-PHPdoc)
     * @param string $timestamp The datetime when the user last logged in
     * @see src/identity/IdentityModel#setLastLogin($dateTime)
     */
    public function setLastLogin($timestamp) {
        $this->lastLogin = date('Y-m-d H:i:s', strtotime($timestamp));
    }

    /**
     * (non-PHPdoc)
     * @return string The datetime when the user last logged in
     * @see src/identity/IdentityModel#getLastLogin()
     */
    public function getLastLogin() {
        return $this->lastLogin;
    }

    /**
     * (non-PHPdoc)
     * @param Role $role The role which the user belongs
     * @see src/identity/IdentityModel#setRole($role)
     */
    public function setRole(Role $role = null) {
        $this->Role = $role;
    }

    /**
     * (non-PHPdoc)
     * @return Role The role which the user belongs
     * @see src/identity/IdentityModel#getRole()
     */
    public function getRole() {
        return $this->Role;
    }
     
    /**
     * (non-PHPdoc)
     * @param array<Role> $roles A list of roles which the user belongs
     * @see src/identity/IdentityModel#setRoles($roles)
     */
    public function setRoles(array $roles = null) {
        $this->Roles = $roles;
    }

    /**
     * (non-PHPdoc)
     * @param array<Role> A list of roles which the user belongs
     * @see src/identity/IdentityModel#getRoles()
     */
    public function getRoles() {
        return $this->Roles;
    }

    /**
     * (non-PHPdoc)
     * @param boolean $value True to enable the account, false to disable
     * @see src/identity/IdentityModel#setEnabled($value)
     */
    public function setEnabled($value) {
        $this->enabled = $value;
    }

    /**
     * (non-PHPdoc)
     * @return boolean True if the account is enable, false if disabled
     * @see src/identity/IdentityModel#getEnabled()
     */
    public function getEnabled() {
        return $this->enabled;
    }
}
?>