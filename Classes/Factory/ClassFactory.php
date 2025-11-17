<?php
declare(strict_types=1);

namespace Aix\Dataviewer\Factory;

use Aix\Dataviewer\Domain\Model\Datatype;
use Aix\Dataviewer\Fluid\View\StandaloneView;
use TYPO3\CMS\Core\Core\ClassLoadingInformation;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Reflection\ReflectionService;


class ClassFactory implements SingletonInterface
{
    /**
     * Domain Mode/Repository Template File Paths
     *
     * @var string
     */
    const DOMAIN_MODEL_TEMPLATE_FILE = "EXT:dataviewer/Resources/Private/Init/Record.php.phtml";
    const DOMAIN_REPOSITORY_TEMPLATE_FILE = "EXT:dataviewer/Resources/Private/Init/RecordRepository.php.phtml";

    /**
     * Reflection Service
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @param ReflectionService $reflectionService
     */
    public function injectReflectionService(ReflectionService $reflectionService)
    {
        $this->reflectionService = $reflectionService;
    }

    /**
     * Creates a class file by a given datatype
     *
     * @param string $classTemplateFile
     * @param Datatype $datatype
     * @return string
     */
    public function getClassFileContents(string $classTemplateFile, Datatype $datatype): string
    {
        /* @var \Aix\Dataviewer\Fluid\View\StandaloneView $view */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $templateFile = GeneralUtility::getFileAbsFileName($classTemplateFile);
        $standaloneView->setTemplatePathAndFilename($templateFile);

        $className = $datatype->getClassName();

        $variables = [
            "className" => $className,
            "classValid" => $this->classValid($datatype),
            "datatype"  => $datatype,
            "fileName" => $classTemplateFile,
        ];

        return $standaloneView->renderSection('class', $variables);
    }

    /**
     * Creates a class file by a given datatype
     *
     * @param Datatype $datatype
     * @return bool
     */
    public function buildDomainModelClassFile(Datatype $datatype): bool
    {
        $classFileName = GeneralUtility::getFileAbsFileName($datatype->getClassFilePath());
        $contents = $this->getClassFileContents(self::DOMAIN_MODEL_TEMPLATE_FILE, $datatype);
        return $this->createFile($classFileName, $contents);
    }

    /**
     * Creates a class file by a given datatype
     *
     * @param \Aix\Dataviewer\Domain\Model\Datatype $datatype
     * @return bool
     */
    public function buildDomainRepositoryClassFile(Datatype $datatype): bool
    {
        $classFileName = GeneralUtility::getFileAbsFileName($datatype->getRepositoryFilePath());
        $contents = $this->getClassFileContents(self::DOMAIN_REPOSITORY_TEMPLATE_FILE, $datatype);
        return $this->createFile($classFileName, $contents);
    }

    /**
     * Creates a file with given contents
     *
     * @param string $filename
     * @param string $contents
     * @return bool
     */
    public function createFile(string $filename, string $contents): bool
    {
        try {
            $path = dirname($filename);
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $result = GeneralUtility::writeFile($filename, $contents);
        } catch (\Exception $e) {
            return false;
        }

        return $result;
    }

    /**
     * Dump autoload information
     *
     * @return void
     */
    public function dumpAutoload(): void
    {
        ClassLoadingInformation::dumpClassLoadingInformation();
    }

    /**
     * Checks if a class is valid
     *
     * @param Datatype $datatype
     * @return bool
     */
    public function classValid(Datatype $datatype): bool
    {
        $domainModelClassName = $datatype->getFullyQualifiedClassName();
        $domainRepositoryClassName = $datatype->getFullyQualifiedRepositoryClassName();
        $domainModelFilePath = GeneralUtility::getFileAbsFileName($datatype->getClassFilePath());
        $domainRepositoryFilePath = GeneralUtility::getFileAbsFileName($datatype->getRepositoryFilePath());

        // Check if class is loaded
        if (!class_exists($domainModelClassName) || !class_exists($domainRepositoryClassName)) {
            return false;
        }

        // Check if class file exists
        if (!file_exists($domainModelFilePath) || !file_exists($domainRepositoryFilePath)) {
            return false;
        }

        // Check if php class can be loaded
        try {
            require_once($domainModelFilePath);
            require_once($domainRepositoryFilePath);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a class is actual
     *
     * @param Datatype $datatype
     */
    public function classActual(Datatype $datatype): bool
    {
        // Checks both domain model and domain repository to be valid and actual
        $domainModelClassFilePath = GeneralUtility::getFileAbsFileName($datatype->getClassFilePath());
        $domainRepositoryClassFilePath = GeneralUtility::getFileAbsFileName($datatype->getRepositoryFilePath());

        // Check Domain Model
        try {
            $oldDomainModelClassContents = @file_get_contents($domainModelClassFilePath);
            $newDomainModelClassContents = $this->getClassFileContents(self::DOMAIN_MODEL_TEMPLATE_FILE, $datatype);

            if (!$oldDomainModelClassContents) {
                return false;
            }

            if (md5($oldDomainModelClassContents) != md5($newDomainModelClassContents)) {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }

        // Check Domain Repository
        try {
            $oldDomainRepositoryClassContents = @file_get_contents($domainRepositoryClassFilePath);
            $newDomainRepositoryClassContents = $this->getClassFileContents(self::DOMAIN_REPOSITORY_TEMPLATE_FILE, $datatype);

            if (md5($oldDomainRepositoryClassContents) != md5($newDomainRepositoryClassContents)) {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }

        // All checks were valid
        return true;
    }

}