<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Utility;

use TYPO3\CMS\Core\Charset\CharsetConverter;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class UrlUtility
{
	/**
	 * Extracts an id from an url
	 *
	 * @param string $url
	 * @return int
	 */
	public static function extractPidFromUrl(string $url): int
	{
		$parsedUrl = parse_url($url, PHP_URL_QUERY);
		if(is_null($parsedUrl)) {
		    return 0;
        }
		parse_str($parsedUrl, $params);

		$id = 0;
		if (isset($params["id"]))
			$id = $params["id"];

		return (int)$id;
	}

    /**
     * Creates a usage friendly code from a given string
     *
     * @param string Entry string
     * @return string
     */
    public static function generatePathSegment(string $inputStr, string $spaceCharacter = '-', bool $strToLower = true): string
    {
        /* @var CharsetConverter $csConverter */
        $csConverter = GeneralUtility::makeInstance(CharsetConverter::class);

        $processed = strip_tags($inputStr);
        $processed = preg_replace('/[ \t\x{00A0}\-+_]+/u', $spaceCharacter, $inputStr);
        $processed = $csConverter->specCharsToASCII('utf-8', $processed);
        $processed = preg_replace('/[^\p{L}0-9' . preg_quote($spaceCharacter) . ']/u', '', $processed);
        $processed = preg_replace('/' . preg_quote($spaceCharacter) . '{2,}/', $spaceCharacter, $processed);
        $processed = trim($processed, $spaceCharacter);

        if ($strToLower) {
            $processed = strtolower($processed);
        }

        return $processed;
    }

    /**
     * Gets a file url from a given
     * filename string
     *
     * @param string $file
     * @return string
     */
    public static function getFileUrl(string $file): string
    {
        $filePath = GeneralUtility::getFileAbsFileName($file);

        if (file_exists($filePath)) {
            $filename = basename($filePath);
            $path = pathinfo($filePath, PATHINFO_DIRNAME);
            $path = PathUtility::getAbsoluteWebPath($path);
            return $path.'/'.$filename;
        }

        return '';
    }
}
