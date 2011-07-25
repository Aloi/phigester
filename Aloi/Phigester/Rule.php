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
 * Aloi_Phigester_Rule
 *
 * Concrete implementations of this class implement actions to be taken when
 * a corresponding nested pattern of XML elements has been matched.
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
abstract class Aloi_Phigester_Rule {
  /**
   * The Digester with which this Rule is associated
   *
   * @var Aloi_Phigester_Digester
   */
  protected $digester = null;

  /**
   * Return the Digester with which this Rule is associated
   *
   * @return Aloi_Phigester_Digester
   */
  public function getDigester() {
    return $this->digester;
  }

  /**
   * Set the Digester with which this Rule is associated
   *
   * @param Aloi_Phigester_Digester $digester A Digester object reference
   */
  public function setDigester(Aloi_Phigester_Digester $digester) {
    $this->digester = $digester;
  }

  /**
   * This method is called when the beginning of a matching XML element
   * is encountered
   *
   * @param array $attributes The attribute list of this element
   * @throws Exception
   */
  public function begin(array $attributes) {
    //The default implementation does nothing
  }

  /**
   * This method is called when the body of a matching XML element
   * is encountered
   *
   * If the element has no body, this method is not called at all.
   *
   * @param string $text The text of the body of this element
   * @throws Exception
   */
  public function body($text) {
    //The default implementation does nothing
  }

  /**
   * This method is called when the end of a matching XML element
   * is encountered
   *
   * @throws Exception
   */
  public function end() {
    //The default implementation does nothing
  }

  /**
   * This method is called after all parsing methods have been
   * called, to allow Rules to remove temporary data.
   * 
   * @throws Exception
   */
  public function finish() {
    //The default implementation does nothing
  }
  
  /**
   * Render a printable version of this Rule
   * 
   * @return string
   */
  public function toString() {
    $sb = get_class($this) . '[]';
    return $sb;
  }
}
?>
