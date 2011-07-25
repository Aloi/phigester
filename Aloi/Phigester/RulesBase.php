<?php
/* Copyright 2010 aloi-project 
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA
 *
 * This file incorporates work covered by the following copyright and 
 * permissions notice:
 * 
 * Copyright (C) 2007 Phigester
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * You may obtain a copy of the License at
 * 	
 * 		http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * This file incorporates work covered by the following copyright and
 * permission notice:
 *
 * Copyright 2004 The Apache Software Foundation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Default implementation of the Rules interface that supports
 * the standard rule matching behavior
 *
 * This class can also be used as a base class for specialized
 * Rules implementations
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_RulesBase implements Aloi_Phigester_Rules {
  /**
   * The set of registered Rule instances, keyed by the matching pattern
   *
   * Each value is a List containing the Rules for that pattern, in the
   * order that they were orginally registered.
   *
   * @var array
   */
  protected $cache = array();

  /**
   * The Digester instance with which this Rules instance is associated
   *
   * @var Aloi_Phigester_Digester
   */
  protected $digester = null;

  /**
   * The set of registered Rule instances, in the order that they were
   * originally registered
   *
   * @var array
   */
  protected $rules = array();

  /**
   * Return the Digester instance with which this Rules instance is
   * associated
   *
   * @return Aloi_Phigester_Digester
   */
  public function getDigester() {
    return $this->digester;
  }

  /**
   * Set the Digester instance with which this Rules instance is associated
   *
   * @param Aloi_Phigester_Digester $digester The newly associated Digester
   * instance reference
   */
  public function setDigester(Aloi_Phigester_Digester $digester) {
    $this->digester = $digester;
    
    foreach ($this->rules as $rule) {
      $rule->setDigester($digester);
    }
  }

  /**
   * Register a new Rule instance matching the specified pattern
   *
   * @param string $pattern The nesting pattern to be matched for this Rule
   * @param Aloi_Phigester_Rule $rule The Rule instance to be registered
   */
  public function add($pattern, Aloi_Phigester_Rule $rule) {
    $pattern = (string) $pattern;
    //To help users who accidently add '/' to the end of their patterns
    if (strlen($pattern) > 1 && substr($pattern, -1) == '/') {
      $pattern = substr($pattern, 0, -1);
    }
        
    $this->cache[$pattern][] = $rule;
    $this->rules[] = $rule;
    
    if (!is_null($this->digester)) {
      $rule->setDigester($this->digester);
    }
  }

  /**
   * Clear all existing Rule instance registrations.
   */
  public function clear() {
    $this->cache = array();
    $this->rules = array();
  }

  /**
   * Return a List of all registered Rule instances that match the specified
   * nesting pattern, or a zero-length List if there are no matches
   *
   * If more than one Rule instance matches, they must be returned
   * in the order originally registered through the add()
   * method.
   *
   * @param string $pattern The nesting pattern to be matched
   * @return array
   */
  public function match($pattern) {   
    $rules = $this->lookup($pattern);

    if (is_null($rules)) {
      $longKey = '';
      $keys = array_keys($this->cache);

      foreach ($keys as $key) {
        if (substr($key, 0, 2) == '*/') {
          $lenKey = strlen(substr($key, 1));
          
          if ($pattern == substr($key, 2)
              || substr($pattern, -$lenKey) == substr($key, 1)) {
            if (strlen($key) > strlen($longKey)) {
              $rules = $this->lookup($key);
              $longKey = $key;
            }
          }
        }
      }
    }
    if (is_null($rules)) {
      $rules = array();
    }
    return $rules;
  }

  /**
   * Return a List of all registered Rule instances, or a zero-length List
   * if there are no registered Rule instances
   *
   * If more than one Rule instance has been registered, they must
   * be returned in the order originally registered through the add()
   * method
   *
   * @return array
   */
  public function rules() {
    return $this->rules;
  }

  /**
   * Return a List of Rule instances for the specified pattern
   *
   * If there are no such rules, return null.
   *
   * @param string $pattern The pattern to be matched
   * @return array
   */
  protected function lookup($pattern) {
    $pattern = (string) $pattern;
    
    if (array_key_exists($pattern, $this->cache)) {
      $rules = $this->cache[$pattern];
    } else {
      return null;
    }
    return $rules;
  }
}
?>
