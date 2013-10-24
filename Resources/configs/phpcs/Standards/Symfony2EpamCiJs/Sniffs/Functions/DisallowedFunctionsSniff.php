<?php

class Symfony2EpamCiJs_Sniffs_Functions_DisallowedFunctionsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'JS',
    );

    private static $disallowedFunctionNames = array(
        'alert',
        'console',
    );

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_STRING,
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
        if (in_array(strtolower($content), self::$disallowedFunctionNames)) {
            //Checking previous token as it could be an object method
            $previousTokenPtr = $phpcsFile->findPrevious(PHP_CodeSniffer_Tokens::$emptyTokens, $stackPtr - 1, null, true);
            if (!is_null($previousTokenPtr)) {
                switch($tokens[$previousTokenPtr]['code']) {
                    case T_OBJECT_OPERATOR:
                        return;
                }
            }
            $error = 'Disallowed function "%s" was called';
            $data = array($content);
            $phpcsFile->addError($error, $stackPtr, 'DisallowedFunctionCalled', $data);
        }
    }
}
