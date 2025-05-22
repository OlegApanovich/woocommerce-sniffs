<?php
/**
 * Sniff to ensure hooks have doc comments.
 */

namespace WooCommerce\Sniffs\Commenting;

use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

/**
 * Comment tags sniff.
 */
class CommentHooksSniff implements Sniff
{
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = [
        'PHP',
    ];

    public function register() {
        return [T_CONCAT_EQUAL]; // .=
    }

    public function process(File $phpcsFile, $stackPtr) {
        $tokens = $phpcsFile->getTokens();

        // Make sure the LHS is $output
        $prev = $phpcsFile->findPrevious(T_VARIABLE, $stackPtr - 1);
        if ($prev === false || $tokens[$prev]['content'] !== '$output') {
            return;
        }

        // Scan forward from .= to the end of the expression
        $end = $this->findExpressionEnd($phpcsFile, $stackPtr);
        for ($i = $stackPtr + 1; $i <= $end; $i++) {
            if ($tokens[$i]['code'] === T_VARIABLE) {
                // Skip $this->something
                $next = $phpcsFile->findNext(T_OBJECT_OPERATOR, $i + 1, $i + 3);
                if ($tokens[$i]['content'] === '$this' && $next !== false) {
                    continue;
                }

                if (! $this->isEscaped($phpcsFile, $i)) {
                    $phpcsFile->addWarning(
                        'Variable %s concatenated to $output without escaping.',
                        $i,
                        'UnescapedOutputConcat',
                        [$tokens[$i]['content']]
                    );
                }
            }
        }
    }

    private function isEscaped(File $phpcsFile, $varIndex) {
        $tokens = $phpcsFile->getTokens();
        $func = $phpcsFile->findPrevious(T_STRING, $varIndex - 1, null, false, null, true);

        if ($func === false) {
            return false;
        }

        $escapers = [
            'esc_html', 'esc_attr', 'esc_url',
            'wp_kses_post', 'esc_js', 'esc_textarea'
        ];

        return in_array($tokens[$func]['content'], $escapers, true);
    }

    private function findExpressionEnd(File $phpcsFile, $start) {
        $tokens = $phpcsFile->getTokens();
        $end = $start;
        $openBrackets = 0;
        $max = count($tokens);

        for ($i = $start + 1; $i < $max; $i++) {
            $end = $i;
            if ($tokens[$i]['code'] === T_SEMICOLON && $openBrackets === 0) {
                break;
            }
            if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
                $openBrackets++;
            }
            if ($tokens[$i]['code'] === T_CLOSE_PARENTHESIS) {
                $openBrackets--;
            }
        }

        return $end;
    }
}
