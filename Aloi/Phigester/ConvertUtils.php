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
 * Utility methods for converting string scalar value to value
 * of the specified primitive type
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
class Aloi_Phigester_ConvertUtils {
  /**
   * Convert the specified value to a value of the specified primitive type
   *
   * @param string $value Value to be converted
   * @param string $type Primitive type to be converted to
   * @return mixed The converted value
   * @throws Aloi_Phigester_Exception_ConversionException
   */
  public static function convert($value, $type) {
    $value = (string) $value;
    $type = strtolower($type);
    
    if ($type == 'boolean') {
      $temp = strtolower($value);
      if ($temp === 'false') {
        return false;
      } else {
        if (!@settype($value, $type))
          throw new Aloi_Phigester_Exception_ConversionException();
        return $value;
      }
    } else {
      if (!@settype($value, $type))
        throw new Aloi_Phigester_Exception_ConversionException();
      return $value;
    }
  }
}
?>
