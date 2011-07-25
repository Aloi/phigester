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
 * Rule implementation that saves a parameter for use by a surrounding
 * CallMethodRule
 * 
 * This parameter may be :
 * <ul>
 * <li>from an attribute of the current element</li>
 * <li>from the current element body</li>
 * <li>from the top object on the stack</li>
 * </ul>
 *
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_CallParamRule extends Aloi_Phigester_Rule {
  /**
   * The attribute from which to save the parameter value
   *
   * @var string
   */
  protected $attributeName = null;

  /**
   * The zero-relative index of the parameter we are saving
   *
   * @var integer
   */
  protected $paramIndex = 0;
  
  /**
   * Is the parameter to be set from the stack ?
   *
   * @var boolean
   */
  protected $fromStack = false;
  
  /**
   * The position of the object from the top of the stack
   * 
   * @var integer
   */
  protected $stackIndex = 0;
  
  /**
   * Stack is used to allow nested body text to be processed
   *
   * @var array
   */
  protected $bodyTextStack = array();
  
  /**
   * Construct a "call parameter" rule that will save the value
   * of the specified attribute as the parameter value
   * 
   * @param integer $paramIndex The zero-relative parameter number.
   * @param string $attributeName The name of the attribute to save.
   * @param boolean $fromStack Should this parameter be taken from
   * the top of the stack?
   * @param integer $stackIndex The index of the object which will be passed
   * as a parameter. The zeroth object is the top of the stack, 1 is the next
   * object down and so on.
   */
  public function __construct($paramIndex, $attributeName = ''
      , $fromStack = false, $stackIndex = 0) {
    $this->paramIndex = (integer) $paramIndex;
    $this->attributeName = (string) $attributeName;
    $this->fromStack = (boolean) $fromStack;
    $this->stackIndex = (integer) $stackIndex;
  }
  
  /**
   * Process the beginning of this element
   *
   * @param array $attributes The attribute list of this element
   * @throws Exception
   */
  public function begin(array $attributes) {
    $param = null;
    
    if ($this->fromStack) {
      $param = $this->digester->peek($this->stackIndex);
      
      $logger = $this->digester->getLogger();
      $indentLogger = $this->digester->getIndentLogger();
      $match = $this->digester->getMatch();
      $sb = $indentLogger . '  [CallParamRule]{' . $match;
      $sb .= '} Save from stack; from stack? true';
      $sb .= '; object=' . get_class($param);
      $logger->debug($sb);
      
    } elseif ($this->attributeName != '') {
      if (array_key_exists($this->attributeName, $attributes)) {
        $param = $attributes[$this->attributeName];
      }
    }
    
    //Have to save the param object to the param stack frame here.
    //Can't wait until end(). Otherwise, the object will be lost.
    //We can't save the object as instance variables, as the instance
    //variables will be overwritten if this CallParamRule is reused
    //in subsequent nesting.
    if (!is_null($param)) {
      $parameters = &$this->digester->peekParams();
      $parameters[$this->paramIndex] = $param;
    }
  }
  
  /**
   * Process the body text of this element
   * 
   * @param string $bodyText The text of the body of this element
   */
  public function body($bodyText) {
    if ($this->attributeName == '' && !$this->fromStack) {
      //We must wait to set the parameter until end
      //so that we can make sure that the right set of parameters
      //is at the top of the stack
      $this->bodyTextStack[] = trim($bodyText);
    }
  }
  
  /**
   * Process any body texts now
   */
  public function end() {
    if (!empty($this->bodyTextStack)) {
      //What we do now is push one parameter onto the top set of parameters
      $parameters = &$this->digester->peekParams();
      $parameters[$this->paramIndex] = array_pop($this->bodyTextStack);
    }
  }
  
  /**
   * Render a printable version of this Rule
   * 
   * @return string
   */
  public function toString() {
    $sb = 'CallParamRule[';
    $sb .= 'paramIndex=' . $this->paramIndex;
    $sb .= ', attributeName=' . $this->attributeName;
    $sb .= ', from stack=' . var_export($this->fromStack, true);
    $sb .= ']';
    return $sb;
  }
}
?>
