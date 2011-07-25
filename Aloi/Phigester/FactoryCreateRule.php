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

//require 'Aloi/Phigester/AbstractObjectCreationFactory.php';

/**
 * Rule implementation that uses an Aloi_Phigester_ObjectCreationFactory to create
 * a new object which it pushes onto the object stack.
 * 
 * <p>When the element is complete, the object will be popped.</p>
 * <p>This rule is intented in situations where the element's attributes
 * are needed before the object can be created. A common scenario is for
 * the Aloi_Phigester_ObjectCreationFactory implementation to use the attributes
 * as parameters in a call to either a factory method or to a no-empty
 * constructor.</p>
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_FactoryCreateRule extends Aloi_Phigester_Rule {
  /**
   * The object creation factory we will use to instantiate objects as required
   * based on the attributes specified in the matched XML element.
   *
   * @var Aloi_Phigester_ObjectCreationFactory
   */
  protected $creationFactory = null;
  
  public function __construct(Aloi_Phigester_ObjectCreationFactory $creationFactory) {
    $this->creationFactory = $creationFactory;
  }
  
  /**
   * Process the beginning of this element.
   *
   * @param array $attributes The attribute list of this element
   * @throws Exception
   */
  public function begin(array $attributes) {    
    try {
      $instance = $this->creationFactory->createObject($attributes);

      $logger = $this->digester->getLogger();
      $indentLogger = $this->digester->getIndentLogger();
      $match = $this->digester->getMatch();
      $logger->debug($indentLogger . "  [FactoryCreateRule]{" . $match
          . "} New " . get_class($instance));        

      $this->digester->push($instance);
    } catch (Exception $exception) {
      throw $exception;
    }
  }
  
  /**
   * Process the end of this element
   */
  public function end() {
    $top = $this->digester->pop();

    $logger = $this->digester->getLogger();
    $indentLogger = $this->digester->getIndentLogger();
    $match = $this->digester->getMatch();
    $logger->debug($indentLogger . "  [FactoryCreateRule]{" . $match
        . "} Pop " . get_class($top));
  }
  
  /**
   * Render a printable version of this Rule
   *
   * @return string
   */
  public function toString() {
    $sb = 'FactoryCreateRule[creationFactory='
        . get_class($this->creationFactory) . ']';
    return $sb;
  }
}
?>
