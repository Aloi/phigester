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
 * Rule implementation that creates a new object and pushes it
 * onto the object stack
 *
 * When the element is complete, the object will be popped.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_ObjectCreateRule extends Aloi_Phigester_Rule {
  /**
   * The attribute containing an override class name if it is present
   *
   * @var string
   */
  protected $attributeName = null;

  /**
   * The class name of the object to be created
   *
   * @var string
   */
  protected $className = null;

  /**
   * Construct an object create rule with the specified class name and an
   * optional attribute name containing an override
   *
   * @param string $className Class name of the object to be created
   * @param string $attributeName Attribute name which, if present, contains
   * an override of the class name to create.
   */
  public function __construct($className, $attributeName = null) {
    $this->className = (string) $className;
    if (!is_null($attributeName)) $this->attributeName
        = (string) $attributeName;
  }

  /**
   * Process the beginning of this element
   *
   * @param array $attributes The attribute list of this element
   * @throws Exception
   */
  public function begin(array $attributes) {
    //Identify the name of the class to instantiate
    $realClassName = $this->className;

    if (!is_null($this->attributeName)) {
      if (array_key_exists($this->attributeName, $attributes)) {
        $realClassName = $attributes[$this->attributeName];
      }
    }

    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    $logger->debug($indentLogger . '  [ObjectCreateRule]{' . $match
        . '} New ' . $realClassName);

    //Try to load the class
    try {
      $className = Aloi_Phigester_ClassLoader::loadClass($realClassName);
    } catch (Exception $exception) {
      throw $exception;
    }
    
    //Instantiate the new object an push it on the context stack
    $object = new $className;
    $this->digester->push($object);
  }

  /**
   * Process the end of this element
   */
  public function end() {
    $top = $this->digester->pop();

    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    $logger->debug($indentLogger . "  [ObjectCreateRule]{" . $match
        . "} Pop " . get_class($top));
  }

  /**
   * Render a printable version of this Rule
   *
   * @return string
   */
  public function toString() {
    $sb = 'ObjectCreateRule[className=' . $this->className;
    $sb .= ', attributeName=' . $this->attributeName . ']';
    return $sb;
  }
}
?>
