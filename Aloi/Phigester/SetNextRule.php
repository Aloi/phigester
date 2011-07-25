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
 * Rule implementation that calls a method on the (top-1) (parent) object
 * , passing the top object (child) as an argument
 *
 * It is commonly used to establish parent-child relationships.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_SetNextRule extends Aloi_Phigester_Rule {
  /**
   * The method name to call on the parent object
   *
   * @var string
   */
  protected $methodName = null;

  /**
   * Construct a "set next" rule with the specified method name
   *
   * @param string $methodName The method name of the parent method to call
   */
  public function __construct($methodName) {
    $this->methodName  = (string) $methodName;
  }

  /**
   * Process the end of this element
   */
  public function end() {
    // Identify the objects to be used
    $child = $this->digester->peek(0);
    $parent = $this->digester->peek(1);

    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    
    if (is_null($parent)) {
      $logger->debug($indentLogger . '  [SetNextRule]{' . $match
          . '} Call [NULL PARENT]->' . $this->methodName . '('
          . get_class($child) . ')');
    } else {
      $logger->debug($indentLogger . '  [SetNextRule]{' . $match
        . '} Call ' . get_class($parent) . '->' . $this->methodName . '('
        . get_class($child) . ')');
    }

    // Call the specified method
    if (!is_null($child) && !is_null($parent)) {
      $methodName = $this->methodName;
      if (!method_exists($parent, $methodName)) {
        $msg = 'Class "' . get_class($parent) . '" has no method named '
            . $methodName;
        throw new Aloi_Phigester_Exception_NoSuchMethodException($msg);
      }
      $parent->$methodName($child);
    }
  }

  /**
   * Render a printable version of this Rule
   *
   * @return string
   */
  public function toString() {
    $sb = 'SetNextRule[';
    $sb .= 'methodName=';
    $sb .= $this->methodName;
    $sb .= ']';
    return $sb;       
  }
}
?>
