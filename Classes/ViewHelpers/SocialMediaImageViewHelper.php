<?php
namespace YoastSeoForTypo3\YoastSeo\ViewHelpers;

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class SocialMediaImageViewHelper extends AbstractViewHelper
{

    /**
     * @param int $uid
     * @param string $field
     * @param string $tabel
     */
    public function render($uid, $field, $table = 'pages')
    {
        $row = $this->getDBConnection()->exec_SELECTgetSingleRow(
            'uid',
            'sys_file_reference',
            'deleted=0 AND hidden=0 AND tablenames="' .
                $table . '" AND fieldname="' . $field . '" AND uid_foreign=' . $uid,
            '',
            'sorting'
        );

        return (int)$row['uid'];
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDBConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
