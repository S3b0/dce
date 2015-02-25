<?php
namespace DceTeam\Dce\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2014 Armin Ruediger Vieweg <armin@v.ieweg.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * DCE Module Controller
 * Provides the backend DCE module, for faster and easier access to DCEs.
 *
 * @package dce
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */



class DceModuleController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \DceTeam\Dce\Domain\Repository\DceRepository
	 * @inject
	 */
	protected $dceRepository;


	/**
	 * Index Action
	 *
	 * @return void
	 */
	public function indexAction() {
		$extConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['dce']);
		$enableUpdateCheck = (bool) $extConfiguration['enableUpdateCheck'];


		$this->view->assign('dces', $this->dceRepository->findAllAndStatics());
		$this->view->assign('enableUpdateCheck', $enableUpdateCheck);
	}

	/**
	 * DcePreviewReturnPage Action
	 * @return void
	 */
	public function dcePreviewReturnPageAction() {
		$this->flashMessageContainer->flush();
		self::removePreviewRecords();
	}

	/**
	 * Removes all dce preview records
	 *
	 * @static
	 * @return void
	 */
	static public function removePreviewRecords() {
		require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('dce') . 'Classes/UserFunction/class.tx_dce_dcePreviewField.php');
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tt_content', 'pid = ' . \tx_dce_dcePreviewField::DCE_PREVIEW_PID . ' AND CType LIKE "dce_dceuid%"');
	}
}