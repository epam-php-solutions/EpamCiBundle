<?php

class Symfony2EpamCi_Sniffs_Variables_DisallowedGlobalVariablesSniff implements PHP_CodeSniffer_Sniff
{
    private static $disallowedVariableNames = array(
        '_SERVER',
        '_GET',
        '_POST',
        '_REQUEST',
        '_SESSION',
        '_ENV',
        '_COOKIE',
        '_FILES',
        'GLOBALS',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_VARIABLE,
        );

    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in
     *                                        the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $varName = ltrim($tokens[$stackPtr]['content'], '$');
        if (in_array($varName, self::$disallowedVariableNames)) {
            $error = 'Direct access to superglobal variable "$%s" is denied';
            $data = array($varName);
            $phpcsFile->addError($error, $stackPtr, 'SuperGlobalAccessed', $data);
        }

    }
}
