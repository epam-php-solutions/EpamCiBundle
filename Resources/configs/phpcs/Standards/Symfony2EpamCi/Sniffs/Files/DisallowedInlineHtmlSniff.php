<?php

class Symfony2EpamCi_Sniffs_Files_DisallowedInlineHtmlSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_INLINE_HTML,
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
        $error = 'Inline HTML "%s" is not allowed';
        $data = array(trim($content));
        $phpcsFile->addError($error, $stackPtr, 'DisallowedInlineHtmlUsed', $data);
    }
}
