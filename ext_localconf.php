<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$boot = function($extensionKey) {
	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);

	// Save hook
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
		'EXT:' . $extensionKey . '/Classes/Hooks/tx_saveDce.php:tx_saveDce';

	// ImpExp Hooks
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/impexp/class.tx_impexp.php']['before_setRelation'][] =
		'EXT:' . $extensionKey . '/Classes/Hooks/tx_dce_impexp.php:tx_dce_impexp->beforeSetRelation';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/impexp/class.tx_impexp.php']['before_writeRecordsRecords'][] =
		'EXT:' . $extensionKey . '/Classes/Hooks/tx_dce_impexp.php:tx_dce_impexp->beforeWriteRecordsRecords';

	// Ajax Calls
	$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['AJAX']['Dce::updateContentElement'] =
		'EXT:' . $extensionKey . '/Classes/Hooks/tx_update_contentelement.php:tx_update_contentelement->updateContentElement';

	// User conditions
	require_once($extensionPath . 'Classes/UserConditions/user_dceOnCurrentPage.php');

	// Rendering hook of content elements in backend
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] =
		'EXT:dce/Classes/Hooks/tx_renderDceContentElement.php:tx_renderDceContentElement';

	// Clear cache hook
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
		'EXT:dce/Classes/Hooks/tx_clearCache.php:tx_clearCache->clearDceCache';

	// Access check
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/alt_doc.php']['makeEditForm_accessCheck'][] =
		'EXT:dce/Classes/Hooks/tx_accessCheck.php:tx_accessCheck->checkAccess';

	// DataPreprocessor XClass
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Backend\\Form\\DataPreprocessor'] = array(
		'className' => 'DceTeam\Dce\XClass\DataPreprocessor',
	);


	if (TYPO3_MODE === 'BE') {
		require_once($extensionPath . 'Classes/UserFunction/class.tx_dce_codemirrorField.php');
		require_once($extensionPath . 'Classes/UserFunction/class.tx_dce_dceFieldCustomLabel.php');
	}

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/template.php']['docHeaderButtonsHook'][] =
		'EXT:dce/Classes/Hooks/tx_docHeaderButtonsHook.php:tx_docHeaderButtonsHook->addQuickDcePopupButton';

		// Special tce validators (eval)
	require_once($extensionPath . 'Classes/UserFunction/class.tx_dce_abstract_formeval.php');
	$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_dce_formevals_lowerCamelCase'] = 'EXT:dce/Classes/UserFunction/class.tx_dce_formevals_lowerCamelCase.php';
	$TYPO3_CONF_VARS['SC_OPTIONS']['tce']['formevals']['tx_dce_formevals_noLeadingNumber'] = 'EXT:dce/Classes/UserFunction/class.tx_dce_formevals_noLeadingNumber.php';

	$GLOBALS['TYPO3_CONF_VARS']['USER']['dce']['dceLocalconfPath'] = PATH_typo3conf . 'temp_CACHED_dce_ext_localconf.php';
	if (!file_exists($GLOBALS['TYPO3_CONF_VARS']['USER']['dce']['dceLocalconfPath'])) {
		/** @var $dceCache \DceTeam\Dce\Cache */
		$dceCache = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('DceTeam\Dce\Cache');
		$dceCache->createLocalconf($GLOBALS['TYPO3_CONF_VARS']['USER']['dce']['dceLocalconfPath']);
	}
	require_once($GLOBALS['TYPO3_CONF_VARS']['USER']['dce']['dceLocalconfPath']);
};

$boot($_EXTKEY);
unset($boot);