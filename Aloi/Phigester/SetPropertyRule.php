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
 * Rule implementation that sets an individual property on the object at the
 * top of the stack, based on attributes with specified names
 *
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_SetPropertyRule extends Aloi_Phigester_Rule {
  /**
   * The attribute that will contain the property name
   *
   * @var string
   */
  protected $name = null;

  /**
   * The attribute that will contain the property value
   *
   * @var string
   */
  protected $value = null;

  /**
   * Construct a "set property" rule with the specified name and value
   * attributes
   *
   * @param string $name The name of the attribute that will contain the name
   * of the property to be set
   * @param string $value The name of the attribute that will contain the value
   * to which the property should be set
   */
  public function __construct($name, $value) {
    $this->name = (string) $name;
    $this->value = (string) $value;
  }

  /**
   * Process the beginning of this element
   *
   * @param array $attributes The attribute list of this element
   * @throws Aloi_Phigester_Exception_NoSuchPropertyException - If the bean does not have
   * a writeable property of the specified name
   */
  public function begin($attributes) {
    // Identify the actual property name and value to be used
    $actualName = '';
    $actualValue = '';
    foreach ($attributes as $attribName => $attribValue) {
      if ($attribName == $this->name) {
        $actualName = $attribValue;
      } elseif ($attribName == $this->value) {
        $actualValue = $attribValue;
      }
    }
    
    // Get a reference to the top object
    $top = $this->digester->peek();
    
    // Log some debugging information
    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    $logger->debug($indentLogger . '  [SetPropertyRule]{' . $match . '} Set '
        . get_class($top) . ' property ' . $actualName . ' to ' . $actualValue);

    // Do nothing if the top object is null
    if (!is_null($top)) {
      $reflection = new ReflectionClass(get_class($top));
      $reflectionProperty = null;
      if(property_exists($top, $actualName)) $reflectionProperty = $reflection->getProperty($actualName);
      if (!empty($reflectionProperty) && $reflectionProperty->isPublic()) {
        $top->$actualName = $actualValue;
      } else {
        $propertySetter = 'set' . ucfirst($actualName);
        if (method_exists($top, $propertySetter)) {
          $top->$propertySetter($actualValue);
        } else {
          $msg = 'Class "' . get_class($top) . '" has no property named "'
              . $actualName . '"';
          throw new Aloi_Phigester_Exception_NoSuchPropertyException($msg);
        }
      }
    }
  }

  /**
   * Render a printable version of this Rule
   *
   * @return string
   */
  public function toString() {
    $sb  = 'SetPropertyRule[';
    $sb .= 'name=';
    $sb .= $this->name;
    $sb .= ', value=';
    $sb .= $this->value;
    $sb .= ']';
    return $sb;
  }
}
