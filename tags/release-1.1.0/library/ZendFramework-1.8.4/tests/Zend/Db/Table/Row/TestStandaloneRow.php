<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestStandaloneRow.php 13552 2009-01-08 14:53:09Z norm2782 $
 */


/**
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table_Row_TestStandaloneRow extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass = 'Zend_Db_Table_TableBugs';
}