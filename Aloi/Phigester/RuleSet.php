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
 * Aloi_Phigester_RuleSet
 * 
 * Public interface defining a shorthand means of configuring a complete
 * set of related Rule definitions in one operation. To use an instance
 * of a class that implements this interface:
 * <ul>
 * <li>Create a concrete implementation of this interface.</li>
 * <li>As you are configuring your Digester instance, call digester->addRuleSet()
 * and pass the RuleSet instance.</li>
 * <li>Digester will call the addRuleInstances() method of your RuleSet
 * to configure the necessary rules.</li>
 * </ul>
 * 
 * @author Olivier Henry <oliv.henry@gmail.com> (PHP5 port)
 * @author John C. Wildenauer <freed001@gmail.com> (PHP4 port)
 * @version $Id$
 */
interface Aloi_Phigester_RuleSet {
  /**
   * Add the set of Rule instances defined in this RuleSet to the specified
   * Digester instance.
   * 
   * This method should only be called by a Digester instance.
   * @param PHPMVC_Digester $digester Digester intance to which the new Rule
   * instances should be added.
   */
  public function addRuleInstances(Aloi_Phigester_Digester $digester);
}
?>
