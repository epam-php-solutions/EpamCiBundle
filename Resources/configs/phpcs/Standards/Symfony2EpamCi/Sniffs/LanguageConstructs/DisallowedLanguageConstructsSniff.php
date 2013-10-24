<?php

class Symfony2EpamCi_Sniffs_LanguageConstructs_DisallowedLanguageConstructsSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_ECHO,
            T_EVAL,
            T_EXIT,
            T_GLOBAL,
            T_GOTO,
            T_HALT_COMPILER,
            T_PRINT,
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
        $content = $tokens[$stackPtr]['content'];
        $error = 'Language construct "%s" should not be used directly';
        $data = array($content);
        $phpcsFile->addError($error, $stackPtr, 'DisallowedLanguageConstructUsed', $data);
    }
}
