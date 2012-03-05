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
 * @package com.makeabyte.agilephp.studio.model
 */

/**
 * Server model
 *
 * @author Jeremy Hahn
 * @copyright Make A Byte, inc
 * @package com.makeabyte.agilephp.studio.model
 */
class Server extends DomainModel {

    private $id;
    private $ip;
    private $hostname;
    private $profile;
    private $ServerType;

    public function __construct() { }

    public function setId($value) {
        $this->id = $value;
    }

    public function setIp($value) {
        $this->ip = $value;
    }

    public function setHostname($value) {
        $this->hostname = $value;
    }

    public function setProfile($value) {
        $this->profile = $value;
    }

    public function getId() {
        return $this->id;
    }

    public function getIp() {
        return $this->ip;
    }

    public function getHostname() {
        return $this->hostname;
    }

    public function getProfile() {
        return $this->profile;
    }

    public function setServerType(ServerType $st = null) {
        $this->ServerType = $st;
    }

    public function getServerType() {
        return $this->ServerType;
    }
}
?>