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
 * Aloi_Phigester_Rules
 *
 * Public interface defining a collection of Rule instances (and 
 * corresponding matching patterns) plus an implementation of a matching policy
 * that selects the rules that match a particular pattern of nested elements 
 * discovered during parsing.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
interface Aloi_Phigester_Rules {
  /**
   * Return the Digester instance with which this Rules instance
   * is associated
   *
   * @return Aloi_Phigester_Digester
   */
  public function getDigester();

  /**
   * Set the Digester instance with which this Rules instance
   * is associated
   *
   * @param Aloi_Phigester_Digester $digester The newly associated
   * Digester instance
   */
  public function setDigester(Aloi_Phigester_Digester $digester);

  /**
   * Register a new Rule instance matching the specified pattern
   *
   * @param string $pattern Nesting pattern to be matched for this Rule
   * @param Aloi_Phigester_Rule $rule The Rule instance to be registered
   */
  public function add($pattern, Aloi_Phigester_Rule $rule);

  /**
   * Clear all existing Rule instance registrations
   */
  public function clear();

  /**
   * Return a List of all registered Rule instances that match
   * the specified nesting pattern, or a zero-length list if there are
   * no matches
   * 
   * If more than one Rule instance matches, they must be returned
   * in the order originally registered through the add() method.
   *
   * @param string $pattern Nesting pattern to be matched
   * @return array
   */
  public function match($pattern);

  /**
   * Return a List of all registered Rule instances, or a zero-length
   * list if there are no registered Rule instances
   *
   * If more than one Rule instance has been registered
   * , they must be returned in the order originally registered through
   * the add() method.
   *
   * @return array
   */
  public function rules();
}
?>
